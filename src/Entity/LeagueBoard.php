<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class LeagueBoard
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=180)
     */
    private $leagueId;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     */
    private $playerOrTeamId;

    /**
     * @ORM\Column(type="string")
     */
    private $queueType;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hotStreak;

    /**
     * @ORM\Column(type="integer")
     */
    private $wins;

    /**
     * @ORM\Column(type="integer")
     */
    private $losses;

    /**
     * @ORM\Column(type="string")
     */
    private $leagueName;

    /**
     * @ORM\Column(type="string")
     */
    private $playerOrTeamName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inactive;

    /**
     * @ORM\Column(type="string")
     */
    private $rank;

    /**
     * @ORM\Column(type="boolean")
     */
    private $freshBlood;

    /**
     * @ORM\Column(type="string")
     */
    private $tier;
    /**
     * @ORM\Column(type="smallint")
     */
    private $leaguePoints;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLeagueId()
    {
        return $this->leagueId;
    }

    /**
     * @param mixed $leagueId
     */
    public function setLeagueId($leagueId): void
    {
        $this->leagueId = $leagueId;
    }

    /**
     * @return mixed
     */
    public function getPlayerOrTeamId()
    {
        return $this->playerOrTeamId;
    }

    /**
     * @param mixed $playerOrTeamId
     */
    public function setPlayerOrTeamId($playerOrTeamId): void
    {
        $this->playerOrTeamId = $playerOrTeamId;
    }

    /**
     * @return mixed
     */
    public function getQueueType()
    {
        return $this->queueType;
    }

    /**
     * @param mixed $queueType
     */
    public function setQueueType($queueType): void
    {
        $this->queueType = $queueType;
    }

    /**
     * @return mixed
     */
    public function getHotStreak()
    {
        return $this->hotStreak;
    }

    /**
     * @param mixed $hotStreak
     */
    public function setHotStreak($hotStreak): void
    {
        $this->hotStreak = $hotStreak;
    }

    /**
     * @return mixed
     */
    public function getWins()
    {
        return $this->wins;
    }

    /**
     * @param mixed $wins
     */
    public function setWins($wins): void
    {
        $this->wins = $wins;
    }

    /**
     * @return mixed
     */
    public function getLosses()
    {
        return $this->losses;
    }

    /**
     * @param mixed $losses
     */
    public function setLosses($losses): void
    {
        $this->losses = $losses;
    }

    /**
     * @return mixed
     */
    public function getLeagueName()
    {
        return $this->leagueName;
    }

    /**
     * @param mixed $leagueName
     */
    public function setLeagueName($leagueName): void
    {
        $this->leagueName = $leagueName;
    }

    /**
     * @return mixed
     */
    public function getPlayerOrTeamName()
    {
        return $this->playerOrTeamName;
    }

    /**
     * @param mixed $playerOrTeamName
     */
    public function setPlayerOrTeamName($playerOrTeamName): void
    {
        $this->playerOrTeamName = $playerOrTeamName;
    }

    /**
     * @return mixed
     */
    public function getInactive()
    {
        return $this->inactive;
    }

    /**
     * @param mixed $inactive
     */
    public function setInactive($inactive): void
    {
        $this->inactive = $inactive;
    }

    /**
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param mixed $rank
     */
    public function setRank($rank): void
    {
        $this->rank = $rank;
    }

    /**
     * @return mixed
     */
    public function getFreshBlood()
    {
        return $this->freshBlood;
    }

    /**
     * @param mixed $freshBlood
     */
    public function setFreshBlood($freshBlood): void
    {
        $this->freshBlood = $freshBlood;
    }

    /**
     * @return mixed
     */
    public function getTier()
    {
        return $this->tier;
    }

    /**
     * @param mixed $tier
     */
    public function setTier($tier): void
    {
        $this->tier = $tier;
    }

    /**
     * @return mixed
     */
    public function getLeaguePoints()
    {
        return $this->leaguePoints;
    }

    /**
     * @param mixed $leaguePoints
     */
    public function setLeaguePoints($leaguePoints): void
    {
        $this->leaguePoints = $leaguePoints;
    }

}