<?php
/* Execute every 3600 seconds */
ini_set('max_execution_time', 0);
require('../core.php');
ob_start('ob_gzhandler');
// Surrender at 20
$surrenderAt20 = $db->query('SELECT url,lastRet FROM cron_news_xml WHERE keyName="surrenderat20"')->fetch_row();
$surrenderAt20Lang = 'en';
$ch = curl_init($surrenderAt20[0]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);	
$result = curl_exec($ch);
curl_close($ch);

$xml=json_decode(json_encode(simplexml_load_string($result)),true);
if($xml['updated'] != $surrenderAt20[1])
{
	foreach($xml['entry'] as $newsData)
	{
		$apiId = $newsData['id'];
		$published = strtotime($newsData['published']) * 1000;
		$updated = strtotime($newsData['updated']) * 1000;
		$category = addslashes($newsData['category']['@attributes']['term']);
		$title = addslashes($newsData['title']);
		$content = addslashes($newsData['content']);
		$author = addslashes($newsData['author']['name']);
		$xpath = new DOMXPath(@DOMDocument::loadHTML($newsData['content']));
		$image = $xpath->evaluate("string(//img/@src)");
		if($image == null)
		{
			$image = 'surrenderat20.png';
		}
		$isImageFull = true;
		if($db->query('SELECT id FROM web_news WHERE apiId="'.$apiId.'"')->num_rows == 0)
		{
			$db->query('INSERT INTO web_news (apiId,title,published,updated,image,imageFullUrl,content,cat,lang) VALUES ("'.$apiId.'","'.$title.'","'.$published.'","'.$updated.'","'.$image.'","'.$isImageFull.'","'.$content.'","'.$category.'","'.$surrenderAt20Lang.'")');
		}
		else
		{
			if($db->query('SELECT updated FROM web_news WHERE apiId="'.$apiId.'"')->fetch_row()[0] != $updated)
			{
				$db->query('UPDATE web_news SET title="'.$title.'",updated="'.$updated.'",image="'.$image.'",imageFullUrl="'.$isImageFull.'",content="'.$content.'",cat="'.$category.'" WHERE apiId="'.$apiId.'"');
			}
		}
	}
}
$db->query('UPDATE cron_news_xml SET lastRet="'.$core->time().'" WHERE keyName="surrenderat20"');
// Trasgo: Noticias lol
$trasgo = $db->query('SELECT url FROM cron_news_xml WHERE keyName="trasgo"')->fetch_row();
$trasgoLang = 'es';
$ch = curl_init($trasgo[0]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);	
$result = curl_exec($ch);
curl_close($ch);

$xml=json_decode(json_encode(simplexml_load_string($result)),true);
foreach($xml['channel']['item'] as $newsData)
{
	$apiId = $newsData['guid'];
	$published = strtotime($newsData['pubDate']) * 1000;
	$updated = strtotime($newsData['pubDate']) * 1000;
	$category = 'Trasgo';
	$title = addslashes($newsData['title']);
	$content = addslashes($newsData['description']);
	$author = 'Editores de Trasgo';
	$xpath = new DOMXPath(@DOMDocument::loadHTML($newsData['description']));
	$image = str_replace('files//styles','files/styles',$xpath->evaluate("string(//img/@src)"));
	if($image == null)
	{
		$image = 'trasgo.png';
	}
	else
	{
		$hisId = intval(preg_replace('/[^0-9]+/', '', $apiId), 10);
		$core->imgCompress($image,$config['web.basedir'].'style\images\news\trasgo/'.$hisId.'.jpg');
		$image = $config['web.url'].'/style/images/news/trasgo/'.$hisId.'.jpg';
	}
	$true = false;
	if($db->query('SELECT id FROM web_news WHERE apiId="'.$apiId.'"')->num_rows == 0)
	{
		$db->query('INSERT INTO web_news (apiId,title,published,updated,image,imageFullUrl,content,cat,lang) VALUES ("'.$apiId.'","'.$title.'","'.$published.'","'.$updated.'","'.$image.'","'.$isImageFull.'","'.$content.'","'.$category.'","'.$trasgoLang.'")');
	}
	else
	{
		if($db->query('SELECT updated FROM web_news WHERE apiId="'.$apiId.'"')->fetch_row()[0] != $updated)
		{
			$db->query('UPDATE web_news SET title="'.$title.'",updated="'.$updated.'",image="'.$image.'",imageFullUrl="'.$isImageFull.'",content="'.$content.'",cat="'.$category.'" WHERE apiId="'.$apiId.'"');
		}
	}
}
$db->query('UPDATE cron_news_xml SET lastRet="'.$core->time().'" WHERE keyName="trasgo"');
/* NyuPlay disabled 
//nyuplay 
$nyuplay = $db->query('SELECT url FROM cron_news_xml WHERE keyName="nyuplay"')->fetch_row();
$nyuplayLang = 'es';
$ch = curl_init($nyuplay[0]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);	
$result = curl_exec($ch);
curl_close($ch);
$xml=json_decode(json_encode(simplexml_load_string($result)),true);
foreach($xml['channel']['item'] as $newsData)
{
	$apiId = $newsData['post-id'];
	$published = strtotime($newsData['pubDate']) * 1000;
	$updated = strtotime($newsData['pubDate']) * 1000;
	$category = 'NyuPlay';
	$title = addslashes($newsData['title']);
	$content = null;
	$author = 'Editores de NyuPlay';
	$image = 'nyuplay.png';
	$isImageFull = false;
	if($db->query('SELECT id FROM web_news WHERE apiId="'.$apiId.'"')->num_rows == 0)
	{
		$db->query('INSERT INTO web_news (apiId,title,published,updated,image,imageFullUrl,content,cat,lang,url) VALUES ("'.$apiId.'","'.$title.'","'.$published.'","'.$updated.'","'.$image.'","'.$isImageFull.'","'.$content.'","'.$category.'","'.$nyuplayLang.'","'.$newsData['link'].'")');
	}
	else
	{
		if($db->query('SELECT updated FROM web_news WHERE apiId="'.$apiId.'"')->fetch_row()[0] != $updated)
		{
			$db->query('UPDATE web_news SET title="'.$title.'",updated="'.$updated.'",image="'.$image.'",imageFullUrl="'.$isImageFull.'",content="'.$content.'",cat="'.$category.'" WHERE apiId="'.$apiId.'"');
		}
	}
}
$db->query('UPDATE cron_news_xml SET lastRet="'.$core->time().'" WHERE keyName="nyuplay"'); */
// RSS Oficial League Of Legends
$official = $db->query('SELECT url FROM cron_news_xml WHERE keyName="official"')->fetch_row();
foreach(explode(',',$config['langs']) as $keyLang)
{
	$officialLang = $keyLang;
	$ch = curl_init(str_replace('{{lang}}',$keyLang,$official[0]));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Gecko/20070219 Firefox/2.0.0.2');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_COOKIE, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 	
	$result = curl_exec($ch);
	$resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	$xml=json_decode(json_encode(simplexml_load_string($result)),true);
	foreach($xml['channel']['item'] as $newsData)
	{
		$lang = json_decode(utf8_encode(file_get_contents($config['web.basedir'].'/kernel/langs/'.$keyLang.'.json')),true);
		$apiId = $newsData['guid'];
		$published = strtotime($newsData['pubDate']) * 1000;
		$updated = strtotime($newsData['pubDate']) * 1000;
		$category = $lang['news.official.lol'];
		$title = addslashes($newsData['title']);
		$content = addslashes($newsData['description']);
		$author = $lang['news.official.lol.editor'];
		$xpath = new DOMXPath(@DOMDocument::loadHTML($newsData['description']));
		$image = 'http://euw.leagueoflegends.com'.$xpath->evaluate("string(//img/@src)");
		if($image == null)
		{
			$image = 'lol.jpg';
		}
		$isImageFull = true;
		if($db->query('SELECT id FROM web_news WHERE apiId="'.$apiId.'"')->num_rows == 0)
		{
			$db->query('INSERT INTO web_news (apiId,title,published,updated,image,imageFullUrl,content,cat,lang,url) VALUES ("'.$apiId.'","'.$title.'","'.$published.'","'.$updated.'","'.$image.'","'.$isImageFull.'","'.$content.'","'.$category.'","'.$officialLang.'","'.$newsData['link'].'")');
		}
		else
		{
			if($db->query('SELECT updated FROM web_news WHERE apiId="'.$apiId.'"')->fetch_row()[0] != $updated)
			{
				$db->query('UPDATE web_news SET title="'.$title.'",updated="'.$updated.'",image="'.$image.'",imageFullUrl="'.$isImageFull.'",content="'.$content.'",cat="'.$category.'" WHERE apiId="'.$apiId.'"');
			}
		}
	}
}
$db->query('UPDATE cron_news_xml SET lastRet="'.$core->time().'" WHERE keyName="official"');
?>