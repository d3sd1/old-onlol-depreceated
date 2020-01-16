<?php
namespace App\Exception;
/*
404 (Not Found)
This error indicates that the server has not found a match for the API request being made. No indication is given whether the condition is temporary or permanent.

Common Reasons
The ID or name provided does not match any existing resource (e.g., there is no summoner matching the specified ID).
There are no resources that match the parameters specified.

 */
class Riot404Exception extends RiotRequestErrorException
{

}