<?php
require('global_func.php');
$url = (isset($_GET['url']) && is_string($_GET['url'])) ? stripslashes($_GET['url']) : '';
$image = (@isImage($url));
if ($image)
{
	$size=getimagesize($url);
	@header("Content-Type: {$size['mime']}");
	if (strpos($url, 'https://') !== false)
	{
		$url=parseImage($url);
	}
	else
	{
		$url=removeFrontTag($url);
		$url=parseImage($url);
	}
	header('Location: '.$url);
	exit;
}