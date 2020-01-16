<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class League
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=180)
     */
    private $leagueId;

    /**
     * @ORM\Column(type="string")
     */
    private $tier;

    /**
     * @ORM\Column(type="string")
     */
    private $queue;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /*
     * We won't save this on database.
     * In fact, we will use this as a wrapper for tmp only.
     */
    private $entries;

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
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param mixed $queue
     */
    public function setQueue($queue): void
    {
        $this->queue = $queue;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param mixed $entries
     */
    public function setEntries($entries): void
    {
        $this->entries = $entries;
    }


}