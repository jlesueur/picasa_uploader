<?php
$filename = "../libraries/config.xml";
$xml = simplexml_load_file($filename);
$SITE_URL = $xml->siteUrl;
//find index.php, trim everything after it.
if(strpos($SITE_URL, 'index.php') !== false)
	$SITE_URL = substr($SITE_URL, 0, strpos($SITE_URL, 'index.php'));
//find rest, trim everything after it.
if(strpos($SITE_URL, 'rest') !== false)
	$SITE_URL = substr($SITE_URL, 0, strpos($SITE_URL, 'rest'));
require('Gallery3.php');

if (isset($_COOKIE['a_tkG3']) && $_COOKIE['a_tkG3'] != '' ){ 
	$auth = $_COOKIE['a_tkG3'];
}
else {
	header("Location: ../index.php"); 
}

	$album = Gallery3::factory($_POST['album'], $auth);

if($_FILES) {
    $blog = '';
    foreach($_FILES as $key => $file) {    
        if(!empty($file)) {
          $tmpfile  = $file['tmp_name'];
          $fname    = str_replace(".JPG", ".jpg", $file['name']);

          $photo = Gallery3::factory()
            ->set('type', 'photo')
            ->set('name', $fname)
            ->set('title', str_replace(array('.jpg', '_'), array('', ' '), $fname))
            ->set_file($tmpfile)
            ->create($album->url, $auth);
          if(isset($_POST['blogThis'])) {
            $blogPhotos[] = $photo->data->entity->id;
          }
        }
    }
    if(isset($_POST['blogThis']))
    {
	echo $SITE_URL . "modules/picasa_uploader/postBlogger.php?googleToken=".urlencode($_POST['googleToken']).'&ids='.urlencode(implode(',', $blogPhotos)).'&blogId='.urlencode($_POST['blogList']);
        //trigger_error("blog looks like: " . var_export($blog,true));
    }
}
else if(isset($_POST['blogThis']))
    echo $SITE_URL . "modules/picasa_uploader/postBlogger.php?googleToken=".urlencode($_POST['googleToken']).'&blogId='.urlencode($_POST['blogList']).'&ids='.urlencode(implode(',',array(1)));
?>
