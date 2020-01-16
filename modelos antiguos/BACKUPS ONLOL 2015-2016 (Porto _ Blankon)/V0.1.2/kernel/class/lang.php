<?php
class lang{
	public static function trans($str)
	{
		if(!empty($GLOBALS['lang'][$str]))
		{
			return $GLOBALS['lang'][$str];
		}
		else
		{
			require($_SERVER['DOCUMENT_ROOT'].'/kernel/langs/'.$GLOBALS['default_lang'].'.php');
			if(!empty($lang[$str]))
			{
				return $lang[$str];
				error_log('Translating failure: ['.$_SESSION['onlol_lang'].'] -> '.$str);
			}
			else
			{
				error_log('Translating failure: ['.$_SESSION['onlol_lang'].'] -> '.$str);
			}
		}
	}
	public static function filterlang($filterlang)
	{
		if(array_key_exists($filterlang,$GLOBALS['langs']))
		{
			return $filterlang;
		}
		else
		{
			return $GLOBALS['default_lang'];
		}
	}
}