<?php
namespace App\Exception;
/*
503 (Service Unavailable)
This error indicates the server is currently unavailable to handle requests because of an unknown reason. The Service Unavailable response implies a temporary condition which will be alleviated after some delay.
 */
class Riot503Exception extends RiotKeepTryingException
{

}