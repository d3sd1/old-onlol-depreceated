<?php
namespace App\Exception;
/*
429 (Rate Limit Exceeded)
This error indicates that the application has exhausted its maximum number of allotted API calls allowed for a given duration. If the client receives a Rate Limit Exceeded response the client should process this response and halt future API calls for the duration, in seconds, indicated by the Retry-After header. Applications that are in violation of this policy may have their access disabled to preserve the integrity of the API. Please refer to our Rate Limiting documentation for more information on determining if you have been rate limited and how to avoid it.

Common Reasons
Unregulated API calls. Check your API Call Graphs to monitor your Dev and Production API key activity.
 */
class Riot429Exception extends RiotKeepTryingException
{

}