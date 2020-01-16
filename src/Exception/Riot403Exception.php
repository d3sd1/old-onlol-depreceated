<?php
namespace App\Exception;
/*
403 (Forbidden)
This error indicates that the server understood the request but refuses to authorize it. There is no distinction made between an invalid path or invalid authorization credentials (e.g., an API key). The client should not continue to make similar requests.

Common Reasons
An invalid API key was provided with the API request.
A blacklisted API key was provided with the API request.
The API request was for an incorrect or unsupported path.
 */
class Riot403Exception extends RiotRequestErrorException
{

}