<?php
$convert = new conversor;
class conversor{
	public function champId2Name($champId)
	{
		global $api;
		global $core;
		global $userLang;
		global $userRegion;
		return $api->staticData('champion', false, $core->langApi($userLang), null, $userRegion, $champId)['name'];
	}
	public function champId2Keyname($champId)
	{
		global $api;
		global $core;
		global $userLang;
		global $userRegion;
		return $api->staticData('champion', false, $core->langApi($userLang), null, $userRegion, $champId)['key'];
	}
}