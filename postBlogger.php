<?php
	$filename = "libraries/config.xml";
	$xml = simplexml_load_file($filename);
	$SITE_URL = $xml->siteUrl;
	$intRootAlbum = (int)$xml->intRootAlbum;
	$intAlbumDisplayed = (int)$xml->intAlbumDisplayed;
	$intSize = (int)$xml->intSize;
require('classes/Gallery3.php');
require('classes/xmlHandler.class');

	$googleToken = $_GET['googleToken'];
	$ids = explode(',', $_GET['ids']);
	foreach($ids as $id)
	{
		assert(is_int($id));
	}
	//do the authentication
	if (isset($_COOKIE['a_tkG3']) && $_COOKIE['a_tkG3'] != '' ){ 
		$auth = $_COOKIE['a_tkG3'];
	}
	else {
		if(!isset($_POST['utilizator']) || !isset($_POST['parola'])){
			echo "<form action='views/login.html.php?er=0&action=blog' method='post' name='relogin'>
			<input type='hidden' name='googleToken' value='$googleToken' />
			<input type='hidden' name='ids' value='{$_GET['ids']}' />
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
				echo "<form action='views/login.html.php?er=1&action=blog' method='post' id='relogin'>
				<input type='hidden' name='googleToken' id='googleToken' value='$googleToken' />
				<input type='hidden' name='ids' value='{$_GET['ids']}' />
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
	//build a blog post
	foreach($ids as  $id)
	{
		$itemUrls[] = urlencode("$SITE_URL/item/$id");
	}
	try {
		$photos = Gallery3::factory("$SITE_URL/items?urls=".json_encode($itemUrls), $auth);
		foreach($photos->data as $photo)
		{
			$blog .= '<div style="text-align:center"><a href="'.$photo->entity->file_url_public.'"><img src="'.$photo->entity->resize_url_public.'" /></a></div><br/><br/>';
		}
	}
	catch(Gallery3_Exception $e)
	{
		var_dump($e);
		die();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="js/picasa_uploader.js"></script>
	<script src="https://apis.google.com/js/auth.js?onload=prepAuthorization"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>
function googleAuthorized()
{
	//post the google post, and redirect to the edit page.
	token = gapi.auth.getToken();
	publishDate = new Date();
	publishDate.setDate(publishDate.getDate() + 1);
	if ('XDomainRequest' in window && window.XDomainRequest !== null) {
		alert('Posting directly to blogger is not supported in Internet Explorer. Please copy and paste the html into your blog for editing.');
		return false;
		// override default jQuery transport
		jQuery.ajaxSettings.xhr = function() {
			try { return new XDomainRequest(); }
			catch(e) { }
		};
 
		// also, override the support check
		jQuery.support.cors = true;
        }
	$.ajax({
		type: "post",
		url: "https://www.googleapis.com/blogger/v3/blogs/<?php echo $_GET['blogId']; ?>/posts/?access_token=" + encodeURIComponent(token.access_token),
		data: JSON.stringify({
			"kind": "blogger#post", 
			"blog": {
				"id": "<?php echo $_GET['blogId']; ?>"
			},
			"published": publishDate,
			"title": new Date(),
			"content": "<?php echo addslashes($blog); ?>"
		}),
		success: function(postData) {
			window.location.href = postData.url;
		},
		dataType: 'json',
		beforeSend: function(jqXHR, settings) {
			jqXHR.setRequestHeader('Authorization', 'Bearer ' + token.access_token);
			jqXHR.setRequestHeader('Content-Type', 'application/json');
		}
	});
	
}
</script>
</head>
<body>
<?php echo htmlentities($blog); ?>
</body>
</html>
