<?php
namespace App\Exception;
/*
400 (Bad Request)
This error indicates that there is a syntax error in the request and the request has therefore been denied. The client should not continue to make similar requests without modifying the syntax or the requests being made.

Common Reasons
A provided parameter is in the wrong format (e.g., a string instead of an integer).
A provided parameter is invalid (e.g., beginTime and startTime specify a time range that is too large).
A required parameter was not provided.
 */
class Riot400Exception extends RiotRequestErrorException
{

}