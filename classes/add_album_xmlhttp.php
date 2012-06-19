<?php
/** 
 * Add a new album 
 * and build a js array, sent back to the form
 * to add the new album references to the select list
 */
	$filename = "../libraries/config.xml";
	$xml = simplexml_load_file($filename);
	$SITE_URL = $xml->siteUrl;

require('Gallery3.php');

if (isset($_COOKIE['a_tkG3']) && $_COOKIE['a_tkG3'] != '' ){ 
	$auth = $_COOKIE['a_tkG3'];
}
else {
	header("Location: ../index.php"); 
	}
$parentAlbum = $_GET['parent'];
$root = Gallery3::factory("$SITE_URL/item/$parentAlbum", $auth);
$album = Gallery3::factory()
  ->set('type', 'album')
  ->set('name', $_GET['path'])
  ->set('title', $_GET['title'])
  ->create($root->url, $auth);
echo 'var arrXmlhttp = new Array(Array(\''. $album->url .'\',\''. $album->data->entity->title .'\'));';

?>
