<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class SummonerChampionMasteryScore
{

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     */
    private $summonerId;

    /**
     * @ORM\Column(type="integer")
     */
    private $score;

    /**
     * @return mixed
     */
    public function getSummonerId()
    {
        return $this->summonerId;
    }

    /**
     * @param mixed $summonerId
     */
    public function setSummonerId($summonerId): void
    {
        $this->summonerId = $summonerId;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score): void
    {
        $this->score = $score;
    }



}