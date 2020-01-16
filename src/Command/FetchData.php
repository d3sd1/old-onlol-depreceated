<?php
namespace App\Command;
ini_set('memory_limit', '-1');

use App\Entity\PendingSummoner;
use App\Entity\Summoner;
use App\Exception\RiotIgnorableException;
use App\Exception\RiotKeepTryingException;
use App\Exception\RiotRequestErrorException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchData extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('onlol:fetch:data')
            // the short description shown while running "php bin/console list"
            ->setDescription('Fetchs tree data of whole pending or non resolved objects (as summoners, matches, champion mastery,...).')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Retrieves all data that needed for stats.')
            ->addArgument('region', InputArgument::REQUIRED, 'You must insert the region where the data should be retrieved.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mgr = $this->getContainer()->get("onlol.loadriotdata");
        $region = $input->getArgument('region');
        $pendingSummoners = $this->getContainer()->get('doctrine')->getRepository(PendingSummoner::class)->findAll();

        while (count($pendingSummoners) > 0) {
            try {
                try {
                    /* Check if user was already looked up on database */
                    $pendingSummonner = $this->getContainer()->get('doctrine')->getRepository(Summoner::class)->find($pendingSummoners[0]->getId());
                    if (null !== $pendingSummonner) {
                        /* Remove unwanted summoner from queue */
                        $this->getContainer()->get('doctrine')->getEntityManager()->remove($pendingSummoners[0]);
                        $this->getContainer()->get('doctrine')->getEntityManager()->flush();
                        $pendingSummoners = $this->getContainer()->get('doctrine')->getRepository(PendingSummoner::class)->findAll();

                        $output->writeln("Summoner dropped from queue, already looked up: " . $pendingSummoners[0]->getId());
                        continue;
                    }
                    /* Retrieve base summoner, if we don't know him */
                    $summoner = $mgr->getSummoner($pendingSummoners[0]->getId());
                    $output->writeln("Summoner look up: " . $pendingSummoners[0]->getId());
                } catch
                (RiotRequestErrorException $e) {

                    $output->writeln("Summoner not found on api: " . $pendingSummoners[0]->getId());
                    /* Remove unwanted summoner from queue */
                    $this->em->remove($pendingSummoners[0]);
                    $this->em->flush();
                    $pendingSummoners = $this->getContainer()->get('doctrine')->getRepository(PendingSummoner::class)->findAll();
                    continue;
                }

                try {
                    $output->writeln("Retrieving summoner leagues: " . $pendingSummoners[0]->getId());
                    /* Retrieve base summoner leagues */
                    $summonerLeagues = $mgr->getSummonerLeagues($pendingSummoners[0]->getId());

                    $associatedLeaguesBoards = [];
                    foreach ($summonerLeagues as $summonerLeague) {
                        /* Retrieve associated summoners on base summoner leagues, and league info */
                        $associatedLeaguesBoards[] = $mgr->getLeagueBoards($summonerLeague->getLeagueId());
                    }
                } catch (RiotIgnorableException $e) {
                    $output->writeln("User has no associated leagues... " . $pendingSummoners[0]->getId());
                }

                /* Get summoner champion mastery */
                try {
                    $output->writeln("Retrieving summoner champ mastery & score... " . $pendingSummoners[0]->getId());
                    $championMastery = $mgr->getSummonerChampMastery($pendingSummoners[0]->getId());
                    $championMasteryScore = $mgr->getSummonerChampMasteryScore($pendingSummoners[0]->getId());
                } catch (RiotIgnorableException $e) {
                    $output->writeln("User has no champ mastery... " . $pendingSummoners[0]->getId());
                }

                /* Get summoner matches
                try {
                    $games = $mgr->getAllSummonerGames($pendingSummoners[0]->getId());
                } catch (RiotIgnorableException $e) {
                    $output->writeln("User has no recent games on season lol... " . $pendingSummoners[0]->getId());
                }

                TODO: ADD RECENT GAMES HERE */

                /* Prepare for next iteration */
                $output->writeln("Removing from queue..." . $pendingSummoners[0]->getId());
                /* Remove unwanted summoner from queue */
                $this->getContainer()->get('doctrine')->getEntitymanager()->remove($pendingSummoners[0]);
                $this->getContainer()->get('doctrine')->getEntitymanager()->flush();
                $pendingSummoners = $this->getContainer()->get('doctrine')->getRepository(PendingSummoner::class)->findAll();

            } catch (RiotKeepTryingException $e) {
                $output->writeln("Sleeping... Something needed to wait about riot api. " . $pendingSummoners[0]->getId());
                sleep(5000);
            }
        }

        $output->writeln("Done fetching! No more pending summoners on " . $region);
    }
}