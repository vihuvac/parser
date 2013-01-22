<?php

include(__DIR__ . '/lib/base.php');

$ch = curl_init();

$fileName = '/var/www/parser/parsed-files/articles.html';

curl_setopt($ch, CURLOPT_URL, 'http://www.worldtravelguide.net');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.224 Safari/534.10');
curl_setopt($ch, CURLOPT_REFERER, "http://google.com");
curl_setopt($ch, CURLOPT_TIMEOUT, 400);

$data = curl_exec($ch);

file_put_contents($fileName, $data);

curl_close($ch);

$dom = new DOMDocument();
@$dom->loadHTML($data);
 
$xpath = new DOMXPath($dom);

$articleTitle = null;
$articleDesc = null;
$imgSrc = null;

$tags = $xpath->query('//div[@class="columnwrapper"]/div');

foreach ($tags as $tag)
{
	$tagHtml = $tag->ownerDocument->saveXML($tag);
	$tagDom = new DOMDocument();
	@$tagDom->loadHTML($tagHtml);

	$tagXpath = new DOMXPath($tagDom);
	
	$titles = $tagXpath->query('//a[@class="topgap"] | //div[@class="block-link-title"]');

	foreach ($titles as $title)
	{
	    $articleTitle = $title->nodeValue;
	}

	$descs = $tagXpath->query('//p[@class="notop"]');

	foreach ($descs as $desc)
	{
	    $articleDesc = $desc->nodeValue;
	}

	$imgs= $tagXpath->query('//a[@class="external"]/img | //a[@class="block-link"]/img');

	foreach ($imgs as $img)
	{
	    $imgSrc = $img->attributes->getNamedItem("src")->nodeValue;
	}

	echo $tag->nodeValue . "<br />";
    
    /*
     * Storing records into my DB
     *
     */
	if (isset($articleTitle, $articleDesc, $imgSrc)) {

		$stmt->execute();

	} else {
		var_dump($articleTitle, $articleDesc, $imgSrc);
	}
	/* End storing records into my DB */

	$articleTitle = null;
	$articleDesc = null;
	$imgSrc = null;
}