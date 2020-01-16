<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Summoner
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     */
    private $accountId;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $profileIconId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $revisionDate;

    /**
     * @ORM\Column(type="smallint")
     */
    private $summonerLevel;

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
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param mixed $accountId
     */
    public function setAccountId($accountId): void
    {
        $this->accountId = $accountId;
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
    public function getProfileIconId()
    {
        return $this->profileIconId;
    }

    /**
     * @param mixed $profileIconId
     */
    public function setProfileIconId($profileIconId): void
    {
        $this->profileIconId = $profileIconId;
    }

    /**
     * @return mixed
     */
    public function getRevisionDate()
    {
        return $this->revisionDate;
    }

    /**
     * @param mixed $revisionDate
     */
    public function setRevisionDate($revisionDate): void
    {
        /* Cast from epoch ms to datetime*/
        if(is_int($revisionDate)) {
            $date = new \DateTime();
            $revisionDate = $date->setTimestamp( ($revisionDate/1000) );
        }
        $this->revisionDate = $revisionDate;
    }

    /**
     * @return mixed
     */
    public function getSummonerLevel()
    {
        return $this->summonerLevel;
    }

    /**
     * @param mixed $summonerLevel
     */
    public function setSummonerLevel($summonerLevel): void
    {
        $this->summonerLevel = $summonerLevel;
    }

}