<?php
require('../core.php');
if(empty($_GET['search']))
{
	exit();
}
// limpio la palabra que se busca
$search= trim($_GET['search']);

// la busco 
$result= search($search);

// seteo la cabecera como json
header('Content-type: application/json; charset=utf-8');

echo json_encode($result);

function search($searchWord)
{
    $tmpArray=array();
    $data=getData();
    foreach($data as $word)
    {
        $searchWordSize=strlen($searchWord);
        $tmpWord=substr($word, 0,$searchWordSize);
        if (strtolower($tmpWord) == strtolower($searchWord))
        {
            $tmpArray[]=$word;
        }
    }
    return $tmpArray;
}
function getData()
{
	global $db;
    $result=array();
	$champs_data = $db->query('SELECT id, champname,champ_id,champ_keyname,lore, title,role_1 FROM lol_champs ORDER BY champname');

	while ($row = $champs_data->fetch_array(MYSQL_ASSOC)) {
		$result[]=$row['champname'].'/'.$row['champ_keyname'];
	}
    
    asort($result);
    return $result;
}