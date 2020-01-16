<?php
require('../core/core.php');
session_start();
if(@$_GET['key'] == 'eltete453434')
{
	$_SESSION['logged'] = true;
}
elseif(empty($_SESSION['logged']))
{
	echo 'Connection rejected';
}
if(!empty($_SESSION['logged']))
{
	$skins = $db->query('SELECT * FROM lol_skins');
	if(!empty($_POST['updatecost']))
	{
		$db->query('UPDATE lol_skins SET price="'.$_POST['updatecost'].'" WHERE champname="'.$_POST['champ'].'" AND skin_num="'.$_POST['skinnum'].'"');
		echo 'Actualizado correctamente<br><br>';
		
	}
	
	while($row = $skins->fetch_array())
	{
		if($row['skin_num'] != 0 && $row['price'] == 0)
		{
			echo $row['skin_name'];
			echo '<form action="" method="post">';
			if($row['price'] == 0)
			{
				$price = null;
			}
			else
			{
				$price = $row['price'];
			}
			echo '<input type="number" name="updatecost" value="'.$price.'" style="width:5%; margin-left:1%;" placeholder="Coste">';
			echo '<input type="hidden" name="champ" value="'.$row['champname'].'" style="width:5%; margin-left:1%;" placeholder="Coste">';
			echo '<input type="hidden" name="skinnum" value="'.$row['skin_num'].'" style="width:5%; margin-left:1%;" placeholder="Coste">';
			echo '<input type="submit" value="Actualizar">';
			
			echo '</form>';
			echo '<br>';
			echo '<br>';
			echo '<br>';
		}
	}
}
?>