<?php
namespace App\Util;


use App\Entity\League;
use App\Entity\LeagueBoard;
use App\Entity\PendingSummoner;
use App\Entity\Summoner;
use App\Entity\SummonerChampionMastery;
use App\Entity\SummonerChampionMasteryScore;
use App\Model\ApiKey;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

//TODO: pasar a constantes las url y usar regiones
//TODO: excepciones por metodo, por ejemplo 422 en ligas
class LoadRiotData
{
    private $em;
    private $container;
    private $objMgr;
    private $apiKeys;
    public function __construct(ContainerInterface $container, EntityManager $em)
    {
        $this->em = $em;
        $this->container = $container;
        $this->objMgr = $this->container->get("onlol.objmgr");
        $this->apiKeys = $this->getApiKeys();
    }

    private function getApiKeys() {
        return $this->objMgr->deserialize($this->container->getParameter('api-keys'), ApiKey::class);
    }

    private function getSeasonCodes() {
        //TODO: meter esto en la db, pero ademas, que se autactualice y poco mas, que sea una constante en la db.
        $seasons = ["PRESEASON 3", "SEASON 3"];
    }

    private function doCall($url) {
        $client   = $this->container->get('guzzle.client');
        $response = $client->request('GET', $url, [
            'headers' => [
                'X-Riot-Token' => 'RGAPI-33fd354a-c8fa-41f7-8007-cc58443773b1'
            ]
        ]);

        //TODO: qeu cargue e intercambie las api keys indicadas en la clase, y si da un 429 en alguna cambie la clave.
        //si da 429 en todas mandar excepcion

        /* Fix for memory leak */
        $content = $response->getBody()->getContents();
        $statusCode = $response->getStatusCode();
        $response->getBody()->close();
        switch($statusCode) {
            case 200:
                return $content;
            case 400:
                throw new Riot400Exception();
            case 401:
                throw new Riot401Exception();
            case 403:
                throw new Riot403Exception();
            case 404:
                throw new Riot404Exception();
            case 415:
                throw new Riot415Exception();
            case 422:
                throw new Riot422Exception();
            case 429:
                throw new Riot429Exception();
            case 500:
                throw new Riot500Exception();
            case 503:
                throw new Riot503Exception();
            case 504:
                throw new Riot504Exception();
        }
    }

    public function getSummoner($summonerId) {
        /* Fetch */
        $data = $this->doCall('https://euw1.api.riotgames.com/lol/summoner/v3/summoners/' . $summonerId);
        /* Cast */
        $summonner = $this->objMgr->deserialize(
            $data,
            Summoner::class
        );
        /* Insert or update on db */
        $this->em->merge($summonner);

        //TODO: que se borre al eliminar, esto se ha puesto por debugging
        /* Delete pending summoner if needed */
        $pendingSummonner = $this->em->getRepository(PendingSummoner::class)->find($summonner->getId());
        if($pendingSummonner !== null) {
            //QUITAR ESTO DE ABAJO! DESCOMENTAR!
            //$this->em->remove($pendingSummonner);
        }
        $this->em->flush();

        return $summonner;
    }
    public function getSummonerLeagues($summonerId) {
        /* Fetch */
        $data = $this->doCall('https://euw1.api.riotgames.com/lol/league/v3/positions/by-summoner/' . $summonerId);

        $output = [];
        /* Iterate over the array by json */
        $data = json_decode($data);
        foreach($data as $indexData) {
            /* Cast */
            $league = $this->objMgr->deserialize(
                json_encode($indexData),
                LeagueBoard::class
            );
            /* Insert or update on db */
            $this->em->merge($league);
            $output[] = $league;
        }

        $this->em->flush();

        return $output;
    }
    public function getLeagueBoards($leagueId) {
        /* Fetch */
        $data = $this->doCall('https://euw1.api.riotgames.com/lol/league/v3/leagues/' . $leagueId);

        /* Iterate over the array by json */
        $league = $this->objMgr->deserialize(
            $data,
            League::class
        );
        /* Insert or update on db */
        $this->em->merge($league);

        /* Now insert League Boards */
        $entries = $this->objMgr->deserialize(
            $league->getEntries(),
            LeagueBoard::class
        );
        foreach($entries as $entry) {
            /* Insert or update on db */
            $entry->setLeagueId($league->getLeagueId());
            $entry->setQueueType($league->getQueue());
            $entry->setTier($league->getTier());
            $entry->setLeagueName($league->getName());
            $this->em->merge($entry);

            /* Insert pending summoner from the entry. It also filters between teams and summoners, since teams are composed with letters and numbers. */
            if(is_numeric($entry->getPlayerOrTeamId()) && null === $this->em->getRepository(Summoner::class)->find($entry->getPlayerOrTeamId())) {
                $pendingSummoner = new PendingSummoner();
                $pendingSummoner->setId($entry->getPlayerOrTeamId());
                $this->em->merge($pendingSummoner);
            }
        }

        $this->em->flush();

        return $league;
    }

    public function getSummonerChampMasteryScore($summonerId) {
        /* Fetch data */
        $data = $this->doCall('https://euw1.api.riotgames.com/lol/champion-mastery/v3/scores/by-summoner/' . $summonerId);

        $champMasteryScore = new SummonerChampionMasteryScore();
        $champMasteryScore->setSummonerId($summonerId);
        $champMasteryScore->setScore($data);
        $this->em->merge($champMasteryScore);
        $this->em->flush();
        return $champMasteryScore;
    }
    public function getSummonerChampMastery($summonerId) {
        /* Fetch data */
        $data = $this->doCall('https://euw1.api.riotgames.com/lol/champion-mastery/v3/champion-masteries/by-summoner/' . $summonerId);

        /* Now insert League Boards */
        $entries = $this->objMgr->deserialize(
            json_decode($data),
            SummonerChampionMastery::class
        );
        foreach($entries as $entry) {
            /* Insert or update on db */
            $this->em->merge($entry);
        }

        $this->em->flush();
        return $entries;
    }
    public function getAllSummonerGames($summonerId) {
        /* Fetch data */
        $data = $this->doCall('https://euw1.api.riotgames.com/lol/champion-mastery/v3/champion-masteries/by-summoner/' . $summonerId);

        //TODO: implement real seasons lol xd
        for($i = 0; $i < 12; $i++) {

        }

        /* Now insert League Boards */
        $entries = $this->objMgr->deserialize(
            json_decode($data),
            SummonerChampionMastery::class
        );
        foreach($entries as $entry) {
            /* Insert or update on db */
            $this->em->merge($entry);
        }

        $this->em->flush();
        return $entries;
    }
}