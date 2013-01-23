<?php

include(__DIR__ . '/lib/base.php');

$siteUrl = 'http://www.worldtravelguide.net';

$fileParsed = '/var/www/parser/files-parsed/articles.html';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $siteUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.224 Safari/534.10');
curl_setopt($ch, CURLOPT_REFERER, "http://google.com");
curl_setopt($ch, CURLOPT_TIMEOUT, 400);

$data = curl_exec($ch);
file_put_contents($fileParsed, $data);
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

	$imgName = basename($imgSrc);

	saveImage($imgSrc, $imgName);

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

function saveImage($imgUrl, $name) {

	$fileName = '/var/www/parser/images-saved/' . $name;

	$ch = curl_init();

	$headers[] = 'Accept: image/pjg, image/png, image/gif, image/x-bitmap, image/jpeg';

	curl_setopt($ch, CURLOPT_URL, $imgUrl);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.224 Safari/534.10');
	curl_setopt($ch, CURLOPT_REFERER, "http://google.com");
	curl_setopt($ch, CURLOPT_TIMEOUT, 400);

	$image = curl_exec($ch);

	if(!file_exists($fileName)) {
		$fp = fopen($fileName,'x');
		fwrite($fp, $image);
	    fclose($fp);
	    curl_close($ch);
	} else {
		echo "The file {$fileName} already exists\n";
	}
    
}