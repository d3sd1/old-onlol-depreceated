<?php

namespace App\Model;


class ApiKey
{
    private $apiKey;
    private $rateLimit10s;
    private $rateLimit10m;

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return mixed
     */
    public function getRateLimit10s()
    {
        return $this->rateLimit10s;
    }

    /**
     * @param mixed $rateLimit10s
     */
    public function setRateLimit10s($rateLimit10s): void
    {
        $this->rateLimit10s = $rateLimit10s;
    }

    /**
     * @return mixed
     */
    public function getRateLimit10m()
    {
        return $this->rateLimit10m;
    }

    /**
     * @param mixed $rateLimit10m
     */
    public function setRateLimit10m($rateLimit10m): void
    {
        $this->rateLimit10m = $rateLimit10m;
    }


}