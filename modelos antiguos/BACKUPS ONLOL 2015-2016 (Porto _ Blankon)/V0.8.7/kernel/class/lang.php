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
	public static function parselang($lang)
	{
		if(array_key_exists($lang,$GLOBALS['langs']) == true)
		{
			return $lang;
		}
		else
		{
			return $GLOBALS['default_lang'];
			return $GLOBALS['default_lang'];
		}
	}
}