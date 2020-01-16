<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');

/* Default MMR */
$default_average = array('B_V' => '870',
			'B_IV' => '940',
            'B_III' => '1010',
            'B_II' => '1080',
            'B_I' => '1150',
            'S_V' => '1220',
            'S_IV' => '1290',
            'S_III' => '1360',
            'S_II' => '1430',
            'S_I' => '1500',
            'G_V' => '1570',
            'G_IV' => '1640',
            'G_III' => '1710',
            'G_II' => '1780',
            'G_I' => '1850',
            'P_V' => '1920',
            'P_IV' => '1990',
            'P_III' => '2060',
            'P_II' => '2130',
            'P_I' => '2200',
            'D_V' => '2270',
            'D_IV' => '2340',
            'D_III' => '2410',
            'D_II' => '2480',
            'D_I' => '2550',
            'M_I' => '2600',
            'C_I' => '2900');
		
		
		
	foreach($default_average as $formatted => $average)
	{
		$league = explode('_',$formatted)[0];
		$division = explode('_',$formatted)[1];
		$total_summoners = $db->query('SELECT id FROM lol_summoners_leagues WHERE tier="'.$league.'" AND division="'.$division.'"')->num_rows;
		$result = $db->query('SELECT mmr FROM lol_summoners_leagues WHERE tier="'.$league.'" AND division="'.$division.'"');
		if($total_summoners > 0)
		{
			$sanitized_elo = array();
			while($row = $result->fetch_array(MYSQLI_NUM))
			{
				array_push($sanitized_elo,$row[0]);
			}
			if($db->query('SELECT id FROM lol_mmr_average WHERE league="'.$formatted.'"')->num_rows > 0)
			{
				$db->query('UPDATE lol_mmr_average SET mmr="'.round((((array_sum($sanitized_elo))/$total_summoners)+$average)/2).'" WHERE league="'.$formatted.'"');
			}
			else
			{
				$db->query('INSERT INTO lol_mmr_average (mmr,league) VALUES ("'.round((((array_sum($sanitized_elo))/$total_summoners)+$average)/2).'","'.$formatted.'")') or die($db->error);
			}
		}	
		else
		{
			$db->query('UPDATE lol_mmr_average SET mmr="'.$average.'" WHERE league="'.$formatted.'"') or die($db->error);
		}
	}