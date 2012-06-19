<?php defined("SYSPATH") or die("No direct script access.") ?>  
<style>
#g-header                    { margin-bottom: 10px; }
#g-admin-picasauploader      { position: relative; font-size: 0.9em; }

.g-admin-left                { float: left; width: 53%; }
.g-admin-left .bt_div 		 { padding: 15px 15px 15px 15px; border: 1px solid #CCC;}
.g-admin-right               { float: left; width: 46%; margin-left: 1%; margin-top: 1em; }
.g-admin-right h3            { border-bottom: #a2bdbf 1px solid; margin-top: 0.3em; margin-bottom: 0.3em; }

#gd-admin-head               { position: relative; height: auto; clear: both; display: block; overflow: auto; font-size: 11px; padding: 0.4em 0.8em; background-color: #b7c9d6; border: #a2bdbf 1px solid; }
#gd-admin-title              { float: left; color: #333v42; font-weight: bold; font-size: 1.6em; text-shadow: #deeefa 0 1px 0; }
#gd-admin-hlinks ul          { float: right; margin-top: 0.4em; font-size: 11px; }
#gd-admin-hlinks li          { list-style-type: none; float: left; color: #618299; display: inline; }
#gd-admin-hlinks a           { font-weight: bold; font-size: 13px; }

.textbox 					{ width:50px !important; }
#gd-admin form              { border: none; }
#gd-admin fieldset          { border: #ccc 1px solid; }
#gd-admin input.g-error     { padding-left: 30px; border: none; }
#gd-admin input.g-success   { background-color: transparent; }
#gd-admin input.g-warning   { background-color: transparent; border: none; }
#gd-admin p.g-error         { padding-left: 30px; border: none; margin-bottom: 0; background-image: none; }

#g-content                  { padding: 0 1em; width: 97%; font-size: 1em; }
#g-content form ul li input  { display: inline; float: left; margin-right: 0.8em; } 
#g-content form ul li select { display: inline; float: left; margin-right: 0.8em; width: 50.6%; padding: 0 0 0 .2em; }
#g-content form ul li input[type='text'] { width: 50%; text-align:right; }
#g-content form input[type="submit"] { border: #5b86ab 2px solid; padding: 0.3em; color: #fff; background: url(/themes/greydragon/images/button-grad-vs.png) #5580a6 repeat-x left top; }
#g-content form input[type="submit"]:hover,
input.ui-state-hover { background-image: url(/themes/greydragon/images/button-grad-active-vs.png); border-color: #2e5475; color: #eaf2fa !important; }

.bt_div a { color:#FFF !important; border: #5b86ab 2px solid; padding: 0.3em; color: #fff; background: url(/themes/greydragon/images/button-grad-vs.png) #5580a6 repeat-x left top; }
.bt_div a:hover { border-color: #2e5475; color: #eaf2fa !important; text-decoration:none; }
</style>
<div id="g-header"><h2><?= t("Picasa&#0153; uploader Adminstration") ?>  </h2></div>
<div id="g-admin-picasauploader">
  <div class="g-admin-left">
   <div id="g-content">
	<?= $form ?>
	<div class="bt_div">
	<?= $button	?>
	</div>
   </div>
  </div>
  
  <div class="g-admin-right">
	<?= $help ?>
  </div>
</div>
