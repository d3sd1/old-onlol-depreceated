<?php
/* Local info about LoL */
class lol{
	public static function parseserver($server)
	{
		if(array_key_exists($server,$GLOBALS['lol_servers']) == true)
		{
			return $server;
		}
		else
		{
			return $GLOBALS['lol_default_server'];
		}
	}
	public static function shards($region)
	{
		global $db;
		$region = lol::parseserver($region);
		$region_db = $db->query('SELECT timestamp_last_check,status FROM lol_shards WHERE region="'.$region.'"')->fetch_row();
		return $region_db[1];
	}
	public static function league_name($league_short)
	{
		switch($league_short)
		{
			case 'U':
			return lang::trans('league_unranked');
			break;
			case 'B':
			return lang::trans('league_bronze');
			break;
			case 'S':
			return lang::trans('league_silver');
			break;
			case 'G':
			return lang::trans('league_gold');
			break;
			case 'P':
			return lang::trans('league_platinum');
			break;
			case 'D':
			return lang::trans('league_diamond');
			break;
			case 'M':
			return lang::trans('league_master');
			break;
			case 'C':
			return lang::trans('league_challenger');
			break;
		}
	}
	public static function series_to_icons($series)
	{
		$position = 0;
		$match = null;
		while($position < strlen($series))
		{
			if(substr($series, $position, 1) == 'L')
			{
				$match .= '<i class="fa fa-times-circle-o"></i>';
			}
			if(substr($series, $position, 1) == 'W')
			{
				$match .= '<i class="fa fa-check-circle-o"></i>';
			}
			if(substr($series, $position, 1) == 'N')
			{
				$match .= '<i class="fa fa-circle-o"></i>';
			}
			$position++;
		}
		echo $match;
	}
	public static function getmmr($lp, $league, $division)
	{
		global $db;
		switch($league)
		{
			case 'C':
			$division_data = 'C_'.$division;
			break;
			case 'M':
			$division_data = 'M_'.$division;
			break;
			case 'D':
			$division_data = 'D_'.$division;
			break;
			case 'P':
			$division_data = 'P_'.$division;
			break;
			case 'G':
			$division_data = 'G_'.$division;
			break;
			case 'S':
			$division_data = 'S_'.$division;
			break;
			case 'B':
			$division_data = 'B_'.$division;
			break;
		}
		$elo = $db->query('SELECT mmr FROM lol_mmr_average WHERE league="'.$division_data.'"')->fetch_row()[0];
		$finalmmr = round($elo+($lp*70)/100);
		return $finalmmr;
	}
	public static function division_mmr($tier)
	{
		global $db;
		$mmr = $db->query('SELECT mmr FROM lol_mmr_average WHERE league="'.$tier.'"')->fetch_row()[0] or die($db->error);
		return $mmr;
	}
	public static function tier_oneless($tier, $division)
	{
		if($division == 'I')
		{
			$division = 'II';
		}
		if($division == 'II')
		{
			$division = 'III';
		}
		if($division == 'III')
		{
			$division = 'IV';
		}
		if($division == 'IV')
		{
			$division = 'V';
		}
		if($division == 'V')
		{
			$division = 'I';
			if($tier == 'C')
			{
				$tier = 'M';
			}
			if($tier == 'M')
			{
				$tier = 'D';
			}
			if($tier == 'D')
			{
				$tier = 'P';
			}
			if($tier == 'P')
			{
				$tier = 'G';
			}
			if($tier == 'G')
			{
				$tier = 'S';
			}
			if($tier == 'S')
			{
				$tier = 'B';
			}
			if($tier == 'B')
			{
				$tier = 'B';
			}
		}
		return $tier.'_'.$division;
	}
	public static function tier_onemore($tier, $division)
	{
		if($division == 'I')
		{
			$division = 'I';
			if($tier == 'C')
			{
				$tier = 'C';
			}
			if($tier == 'M')
			{
				$tier = 'C';
			}
			if($tier == 'D')
			{
				$tier = 'M';
			}
			if($tier == 'P')
			{
				$tier = 'D';
			}
			if($tier == 'G')
			{
				$tier = 'P';
			}
			if($tier == 'S')
			{
				$tier = 'G';
			}
			if($tier == 'B')
			{
				$tier = 'S';
			}
		}
		if($division == 'II')
		{
			$division = 'I';
		}
		if($division == 'III')
		{
			$division = 'II';
		}
		if($division == 'IV')
		{
			$division = 'III';
		}
		if($division == 'V')
		{
			$division = 'V';
		}
		return $tier.'_'.$division;
	}
	public static function champ_id2name($id)
	{
		global $db;
		$data = $db->query('SELECT champ_name FROM lol_champs WHERE champ_id='.$id.' AND lang="'.lang::parselang(@$_SESSION['onlol_lang']).'"')->fetch_row()[0] or die($db->error);
		return $data;
	}
	public static function champ_name2id($name)
	{
		global $db;
		$data = $db->query('SELECT champ_id FROM lol_champs WHERE champ_name="'.$name.' AND lang="'.lang::parselang(@$_SESSION['onlol_lang']).'"')->fetch_row()[0] or die($db->error);
		return $data;
	}
	public static function champ_id2key($id)
	{
		global $db;
		$data = $db->query('SELECT champ_key FROM lol_champs WHERE champ_id='.$id)->fetch_row()[0] or die($db->error);
		return $data;
	}
	public static function summonerspell_id2key($id)
	{
		if($id != null)
		{
			global $db;
			$data = $db->query('SELECT spell_key FROM lol_summonerspells WHERE spell_id='.$id.' AND lang="'.lang::parselang(@$_SESSION['onlol_lang']).'"')->fetch_row()[0] or die($db->error);
		}
		else
		{
			$data = 'SummonerBoost';
		}
		return $data;
	}
	public static function summoner_id2name($id)
	{
		global $db;
		$data = $db->query('SELECT name FROM lol_summoners WHERE summoner_id='.$id)->fetch_row()[0] or die($db->error);
		return $data;
	}
	public static function summoner_name2id($name,$region)
	{
		global $db;
		$data = $db->query('SELECT summoner_id FROM lol_summoners WHERE region="'.$region.'" AND name="'.$name.'"')->fetch_row()[0] or die($db->error);
		return $data;
	}
	public static function queue2txt($queue)
	{
		switch($queue)
		{
			case 'NONE':
			return lang::trans('queue_none');
			break;
			case 'NORMAL':
			return lang::trans('queue_normal');
			break;
			case 'BOT':
			return lang::trans('queue_bot');
			break;
			case 'RANKED_SOLO_5x5':
			return lang::trans('queue_rankedsolo');
			break;
			case 'RANKED_PREMADE_3x3':
			return lang::trans('queue_rankedprem3x3');
			break;
			case 'RANKED_PREMADE_5x5':
			return lang::trans('queue_rankedprem5x5');
			break;
			case 'RANKED_TEAM_3x3':
			return lang::trans('queue_rankedprem3x3');
			break;
			case 'RANKED_TEAM_5x5':
			return lang::trans('queue_rankedprem5x5');
			break;
			case 'ODIN_UNRANKED':
			return lang::trans('queue_dominion');
			break;
			case 'NORMAL_3x3':
			return lang::trans('queue_normal3x3');
			break;
			case 'BOT_3x3':
			return lang::trans('queue_bot3x3');
			break;
			case 'CAP_5x5':
			return lang::trans('queue_teambuilder5x5');
			break;
			case 'ARAM_UNRANKED_5x5':
			return lang::trans('queue_aram');
			break;
			case 'ONEFORALL_5x5':
			return lang::trans('queue_oneforall5x5');
			break;
			case 'FIRSTBLOOD_1x1':
			return lang::trans('queue_firstblood1x1');
			break;
			case 'FIRSTBLOOD_2x2':
			return lang::trans('queue_firstblood2x2');
			break;
			case 'SR_6x6':
			return lang::trans('queue_hexakill');
			break;
			case 'URF':
			return lang::trans('queue_urf');
			break;
			case 'URF_BOT':
			return lang::trans('queue_urfbot');
			break;
			case 'NIGHTMARE_BOT':
			return lang::trans('queue_urfdoombots');
			break;
			case 'ASCENSION':
			return lang::trans('queue_ascension');
			break;
			case 'HEXAKILL':
			return lang::trans('queue_hexakill');
			break;
			case 'KING_PORO':
			return lang::trans('queue_kingporo');
			break;
			case 'COUNTER_PICK':
			return lang::trans('queue_counterpick');
			break;
			case 'BILGEWATER':
			return lang::trans('queue_bilgewater');
			break;
			case 'CoopVsAI':
			return lang::trans('queue_bot');
			break;
			case 'CoopVsAI3x3':
			return lang::trans('queue_bot3x3');
			break;
			case 'CUSTOM_GAME':
			return lang::trans('queue_custom');
			break;
			case 'TUTORIAL_GAME':
			return lang::trans('queue_tutorial');
			break;
			case 'MATCHED_GAME':
			return lang::trans('queue_matched');
			break;
			case 'TEAM_BUILDER_DRAFT_RANKED_5x5':
			return lang::trans('queue_rankedsolo');
			break;
			default: error_log('Queue type not found: '.$queue);return lang::trans('queue_none');
		}
	}
	public static function position2txt($position)
	{
		switch($position)
		{
			case 'TOP':
			return lang::trans('position_top');
			break;
			case 'MID':
			return lang::trans('position_mid');
			break;
			case 'JUN':
			return lang::trans('position_jgl');
			break;
			case 'SUP':
			return lang::trans('position_sup');
			break;
			case 'ADC':
			return lang::trans('position_adc');
			break;
			default: lang::trans('position_not_found');
		}
	}
}