<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="../css/reset.css" type="text/css" media="screen">
<link rel="stylesheet" href="../css/inv.css" type="text/css" media="screen">
<link rel="stylesheet" href="../css/picasa_uploader.css" type="text/css" media="screen" />
<script type="text/javascript">
function verifythis(){
	if (document.getElementById('ut').value == ''){
		alert('Username please!');
		return false;
		}
	if (document.getElementById('ps').value == ''){
		alert('Password Please');
		return false;
		}
	else { 
		return true;
		}
	}
</script>
</head>
<?php 
$er = (isset($_GET['er'])) ? $_GET['er'] : 0;
$action = (isset($_GET['action'])) ? $_GET['action'] : 'upload';
?>
<body id="login">
<div id="header">
    <img id="logo" src="../css/picasa_upl_logo.png" alt="" class="left">
</div>
  <div id="login-content">
   <?php if($er == 0){?>
	      <div class="notification attention">
			<div>Username and Password Case sensitive!. </div>
		  </div>
   <?php } else { ?>
 	      <div class="notification error">
			<div>Username or Password Incorrect!. </div>
		  </div>
   <?php } ?>
	   <form action="<?php if($action == 'upload') { ?>../index.php<?php } else { ?>../postBlogger.php<?php } ?>" method="post" onSubmit="return verifythis()">
	   <?php 
	   $rss = $_POST['rss'];
	   echo "<input type='hidden' name='rss' value='$rss' />";
	   ?>
      <p>
        <label>Username</label>
        <input class="text-input" name="utilizator" id="ut" type="text">
      </p>
      <div class="clear"></div>
      <p>
        <label>Password</label>
        <input class="text-input" name="parola" id="ps" type="password">
      </p>
      <div class="clear"></div>
      <p id="remember-password">
         Remember me <input type="checkbox" name="rmb"></p>
      <div class="clear"></div>
      <p class="button-line">
        <input class="button" value="Login" type="submit"><!-- &nbsp;<input class="button" type="button" value="Cancel" onclick="location.href='minibrowser:close'"> -->
      </p>
    </form>
      </div>

</body></html>
