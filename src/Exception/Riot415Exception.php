<?php
namespace App\Exception;
/*
415 (Unsupported Media Type)
This error indicates that the server is refusing to service the request because the body of the request is in a format that is not supported.

Common Reasons
The Content-Type header was not appropriately set.
 */
class Riot415Exception extends RiotRequestErrorException
{

}