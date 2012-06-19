<?php 
ob_start();
$rss = $_POST['rss'];
	$filename = "libraries/config.xml";
	$xml = simplexml_load_file($filename);
	$SITE_URL = $xml->siteUrl;
	$intRootAlbum = (int)$xml->intRootAlbum;
	$intAlbumDisplayed = (int)$xml->intAlbumDisplayed;
	$intSize = (int)$xml->intSize;
require('classes/Gallery3.php');
require('classes/xmlHandler.class');

//before doing anything, catch the google auth token.
/*
$url = $_SERVER['REQUEST_URI'];
$fragment = parse_url($url, PHP_URL_FRAGMENT);
preg_match_all('/([^&=]+)=([^&]*)/gm', $fragment, $matches = array(), PREG_SET_ORDER);
foreach($matches[0] as $keyPos => $key)
{
	$params[urldecode($key)] = urldecode($matches[1][$keyPos]);
}
if(isset($params['access_token'])) {
	$token = $params['access_token'];
	setcookie("googleToken", $token, time() + $params['expires_in']);
}
elseif(isset($_COOKIE['googleToken'])) {
	$token = $_COOKIE['googleToken'];
}
*/

if (isset($_COOKIE['a_tkG3']) && $_COOKIE['a_tkG3'] != '' ){ 
	$auth = $_COOKIE['a_tkG3'];
}
else {
	if(!isset($_POST['utilizator']) || !isset($_POST['parola'])){
		echo "<form action='views/login.html.php?er=0' method='post' name='relogin'>
		<input type='hidden' name='rss' value='$rss' />
		</form>
		<script language=\"JavaScript\">
			document.forms['relogin'].submit();
		</script>";
	}
	elseif(isset($_POST['utilizator']) && isset($_POST['parola'])) {
		$USER = $_POST['utilizator'];
		$PASSWORD = $_POST['parola'];
		$RMB = $_POST['rmb'];
		try {
			$auth = Gallery3::login($SITE_URL, $USER, $PASSWORD);
		}
		catch (Gallery3_Forbidden_Exception $e) {
			echo "<form action='views/login.html.php?er=1' method='post' id='relogin'>
			<input type='hidden' name='rss' id='rss' value='$rss' />
			<input type='hidden' id='login_err' value='1' />
			</form>
			<script language=\"JavaScript\">
				document.forms['relogin'].submit();
			</script>";
		}
		$expire=time()+60*60*24*360;
		($RMB == 'on')? setcookie("a_tkG3", $auth, $expire):setcookie("a_tkG3", $auth, "-1");
	}
}
$root = Gallery3::factory("$SITE_URL/item/$intRootAlbum?type=album&scope=all", $auth);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="css/reset.css" type="text/css" media="screen">
<!-- Invalid Stylesheet. This makes stuff look pretty. Remove it if you want the CSS completely valid -->
<link rel="stylesheet" href="css/inv.css" type="text/css" media="screen">
<!-- Main Stylesheet -->
<link rel="stylesheet" href="css/picasa_uploader.css" type="text/css" media="screen" />

  <script type="text/javascript" src="https://apis.google.com/js/client.js"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script type="text/javascript" src="js/picasa_uploader.js"></script>
  
</head>
<body onload="javscript:sf()">
<div id="header">
    <img id="logo" src="css/picasa_upl_logo.png" alt="" class="left">
</div>
<div id="content">
<h2> Upload your photos from Picasa </h2>
<form method="post" action="classes/post.php" id="postfiles" enctype="multipart/form-data" >
	<input type="hidden" name="googleToken" id="googleToken"/>
<fieldset>
<label class="title">Choose album to upload to</label>
<br /><br />
<?php 
function get_albums($acest_album,$auth_k,$k) {
	$ret_album = ""; //atentie la recursive ... 
	foreach($acest_album->data->members as $a_album){
		$as_album = Gallery3::factory($a_album, $auth_k);
		if ($as_album->data->entity->type == "album"){
			$ret_album .= '<option value="'. $as_album->url .'" id="'. $as_album->data->entity->id .'">';
			for($i=0;$i<$k+1;$i++){
				$ret_album .= '&nbsp;';
				}
			$ret_album .= '&#187; '. $as_album->data->entity->title . "</option>\r\n";
//			$ret_album .= get_albums($as_album,$auth_k,1);
			}
		}
	return $ret_album;
}
?>
      <select name="album" id="album">
<?php
	echo '<option value="'. $root->url .'" id="'. $root->data->entity->id .'">'. $root->data->entity->title ."</option>\r\n";
	echo get_albums($root,$auth,0);
?>
      </select>
	&nbsp;&nbsp;&nbsp;<input type="button" onclick="frmPicasa_display_new(); return false" class="button" value="New Album" />
	      <div id="new" style="display:none">
<?php /*        <label for="albumPath">Path name</label> <input type="text" id="albumPath" name="albumPath" /> <em>e.g. : october-2007</em><br /> */ ?>
        <label for="albumTitle">Title</label> <input type="text" id="albumTitle" name="albumTitle" value="<?php date('Y-m-d'); ?>"/> <em>e.g. : October 2007</em><br />
        <input type="button" onclick="frmPicasa_xhr_new(); return false" class="button" value="Create" />
      </div>
      <div>
		<input id="googleLogin" type="button" onclick="validateGoogleToken(); return false;" class="button" value="Publish To Blogger" />
		<select style="display:none" name="blogList" id="blogList"></select>
      </div>
</fieldset>
<h3>Selected Images</h3>
<input type='hidden' name='rss' id='rss' value='<?php echo $rss ?>' />
    <div>
<?

if ($_POST['rss']) {
    $xh = new xmlHandler();
    $nodeNames = array('PHOTO:THUMBNAIL', 'PHOTO:IMGSRC', 'TITLE');
    $xh->setElementNames($nodeNames);
    $xh->setStartTag('ITEM');
    $xh->setVarsDefault();
    $xh->setXmlParser();
    $xh->setXmlData(stripslashes($_POST['rss']));
    $pData = $xh->xmlParse();
	($intSize == 0)? $resize = "" : $resize = "?size=".$intSize;

    // Preview "tray": draw shadowed square thumbnails of size 48x48
	$it = 0;
    foreach ($pData as $e) {
        echo '<img src="'. $e['photo:thumbnail'] ."?size=-48\">\r\n";
    // Image request queue: add image requests for base image & clickthrough
        $large = $e['photo:imgsrc'] .$resize;//'?size='. $intSize;
        $it++;
        echo '<input id="id_"'.$it.' class="file_field" type="hidden" name="'.$large."\">\r\n";
    }

	if($it > 5){
		echo '<div class="notification attention png_bg">
			<div>Number of uploaded files depends on upload_max_filesize an post_max_size directive of php config file!. </div>
		  </div>';
	}
} 
else {
	 echo '<div class="notification attention png_bg">
			<div>No images received!. </div>
		  </div>';
} 
?>
    </div>
<h3>&nbsp;</h3>
<p><input class="button" type="submit" value="Publish" id="post_f" onClick="return test_root(this);">&nbsp;<input class="button" type="button" value="Cancel" onclick="location.href='minibrowser:close'"> </p>
</form>
</div>
<div id="logout"><input type="button" class="button" value="Logout" onclick="eraseCookie(); location.href='minibrowser:close'" />
</body>
</html>
