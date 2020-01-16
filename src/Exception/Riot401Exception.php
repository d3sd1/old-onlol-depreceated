<?php
namespace App\Exception;
/*
401 (Unauthoritzed)
This error indicates that the request being made did not contain the necessary authentication credentials (e.g., an API key) and therefore the client was denied access. The client should not continue to make similar requests without including an API key in the request.

Common Reasons
An API key has not been included in the request.
 */
class Riot401Exception extends RiotRequestErrorException
{

}