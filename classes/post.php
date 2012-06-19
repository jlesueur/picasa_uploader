<?php
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
            //build a blog entry...
            $blog .= '<div><a href="'.$photo->data->entity->file_url_public.'"><img src="'.$photo->data->entity->resize_url_public.'" /></a></div>';
          }
        }
    }
    if(isset($_POST['blogThis']))
    {
        trigger_error("blog looks like: " . var_export($blog,true));
    }
}
?>
