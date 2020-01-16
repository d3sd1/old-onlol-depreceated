<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class SummonerChampionMastery
{

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     */
    private $playerId;

    /**
     * @ORM\Id
     * @ORM\Column(type="smallint")
     */
    private $championId;

    /**
     * @ORM\Column(type="smallint")
     */
    private $championLevel;

    /**
     * @ORM\Column(type="boolean")
     */
    private $chestGranted;

    /**
     * @ORM\Column(type="integer")
     */
    private $championPoints;

    /**
     * @ORM\Column(type="integer")
     */
    private $championPointsSinceLastLevel;

    /**
     * @ORM\Column(type="integer")
     */
    private $championPointsUntilNextLevel;

    /**
     * @ORM\Column(type="integer")
     */
    private $tokensEarned;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastPlayTime;

    /**
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param mixed $playerId
     */
    public function setPlayerId($playerId): void
    {
        $this->playerId = $playerId;
    }

    /**
     * Wrapper only.
     * @return mixed
     */
    public function getSummonerId()
    {
        return $this->playerId;
    }

    /**
     * Wrapper only.
     * @param mixed $playerId
     */
    public function setSummonerId($playerId): void
    {
        $this->playerId = $playerId;
    }

    /**
     * @return mixed
     */
    public function getChampionLevel()
    {
        return $this->championLevel;
    }

    /**
     * @param mixed $championLevel
     */
    public function setChampionLevel($championLevel): void
    {
        $this->championLevel = $championLevel;
    }

    /**
     * @return mixed
     */
    public function getChestGranted()
    {
        return $this->chestGranted;
    }

    /**
     * @param mixed $chestGranted
     */
    public function setChestGranted($chestGranted): void
    {
        $this->chestGranted = $chestGranted;
    }

    /**
     * @return mixed
     */
    public function getChampionPoints()
    {
        return $this->championPoints;
    }

    /**
     * @param mixed $championPoints
     */
    public function setChampionPoints($championPoints): void
    {
        $this->championPoints = $championPoints;
    }

    /**
     * @return mixed
     */
    public function getChampionPointsSinceLastLevel()
    {
        return $this->championPointsSinceLastLevel;
    }

    /**
     * @param mixed $championPointsSinceLastLevel
     */
    public function setChampionPointsSinceLastLevel($championPointsSinceLastLevel): void
    {
        $this->championPointsSinceLastLevel = $championPointsSinceLastLevel;
    }

    /**
     * @return mixed
     */
    public function getChampionPointsUntilNextLevel()
    {
        return $this->championPointsUntilNextLevel;
    }

    /**
     * @param mixed $championPointsUntilNextLevel
     */
    public function setChampionPointsUntilNextLevel($championPointsUntilNextLevel): void
    {
        $this->championPointsUntilNextLevel = $championPointsUntilNextLevel;
    }

    /**
     * @return mixed
     */
    public function getTokensEarned()
    {
        return $this->tokensEarned;
    }

    /**
     * @param mixed $tokensEarned
     */
    public function setTokensEarned($tokensEarned): void
    {
        $this->tokensEarned = $tokensEarned;
    }

    /**
     * @return mixed
     */
    public function getChampionId()
    {
        return $this->championId;
    }

    /**
     * @param mixed $championId
     */
    public function setChampionId($championId): void
    {
        $this->championId = $championId;
    }

    /**
     * @return mixed
     */
    public function getLastPlayTime()
    {
        return $this->lastPlayTime;
    }

    /**
     * @param mixed $lastPlayTime
     */
    public function setLastPlayTime($lastPlayTime): void
    {
        /* Cast from epoch ms to datetime*/
        if(is_int($lastPlayTime)) {
            $date = new \DateTime();
            $lastPlayTime = $date->setTimestamp( ($lastPlayTime/1000) );
        }
        $this->lastPlayTime = $lastPlayTime;
    }



}