<?php
$convert = new conversor;
$champInfo = $api->staticData('champion', false, $core->langApi($userLang), null, $userRegion)['data'];
class conversor{
	public function champId2Name($champId)
	{
		global $api;
		global $core;
		global $userLang;
		global $userRegion;
		global $champInfo;
		foreach ($champInfo as $key => $val) {
			if($val['id'] === $champId) {
				   return $val['name'];
				   break;
				}
		}
	}
	public function champId2Keyname($champId)
	{
		global $api;
		global $core;
		global $userLang;
		global $userRegion;
		global $champInfo;
		foreach ($champInfo as $key => $val) {
			if($val['id'] === $champId) {
				   return $val['key'];
				   break;
				}
		}
	}
}