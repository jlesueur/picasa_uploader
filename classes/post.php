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
        }
    }
}
?>
