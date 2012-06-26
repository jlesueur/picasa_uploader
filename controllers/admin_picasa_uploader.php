<?php defined("SYSPATH") or die("No direct script access.");/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2011 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
class Admin_picasa_uploader_Controller extends Admin_Controller {
  public function index() {
  // Generate a new admin page.
    print $this->_get_view();
  }
  
  public function handler() {
    access::verify_csrf();

    $form = $this->_get_form();
    if ($form->validate()) {
		$create_buton = Input::instance()->post("button_create");
		$intR = Input::instance()->post("intR");
		$intd = Input::instance()->post("intd");
		$resz = Input::instance()->post("resz");
			$id_but = $this->setGUID();
			$this->_write_pbf_file($id_but);
			$this->_write_pbz_file($id_but);
			$this->_write_cfg_file($intR,$intd,$resz);
			message::success("Button created succsesfully"); 
      url::redirect("admin/picasa_uploader");
    }

    print $this->_get_view($form);
  }

  private function _get_view($form=null) {
    $v = new Admin_View("admin.html");
    $v->content = new View("admin_picasa_uploader.html");
    $v->content->form = empty($form) ? $this->_get_form() : $form;
	$v->content->button = $this->_show_install();
	$v->content->help = $this->get_edit_form_help();
    return $v;
  }
  
  private function prerequisite_check($group, $id, $is_ok, $caption, $caption_ok, $caption_failed, $iswarning, $msg_error) {
    $confirmation_caption = ($is_ok)? $caption_ok : $caption_failed;
    $checkbox = $group->checkbox($id)
      ->label($caption . " " . $confirmation_caption)
      ->checked($is_ok)
      ->disabled(true);
    if ($is_ok):
      $checkbox->class("g-success");
    elseif ($iswarning):
      $checkbox->class("g-prerequisite g-warning")->error_messages("failed", $msg_error)->add_error("failed", 1);
    else:
      $checkbox->class("g-error")->error_messages("failed", $msg_error)->add_error("failed", 1);
    endif;
  }
  private function filexists($file)
	{
	  $ps = explode(":", ini_get('include_path'));
	  foreach($ps as $path)
	  {
		if(file_exists($path.'/'.$file)) return true;
	  }
	  if(file_exists($file)) return true;
	  return false;
	}
  private function _get_form() {
    $form = new Forge("admin/picasa_uploader/handler", "", "post",
                      array("id" => "g-adminForm"));
	    /* Prerequisites ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

    $group = $form->group("requirements")->label(t("Prerequisites"));
    $gallery_ver = module::get_version("gallery");

    $this->prerequisite_check($group, "rest", (module::is_active("rest") and module::info("rest")), 
        t("REST API Module"), t("Found"), t("Required"), FALSE, t("Check Failed. Module REST API is required to connect remotely."));
    
    $this->prerequisite_check($group, "mail", ($this->filexists("Mail.php")), 
        t("PEAR package Mail.php "), t("Found"), t("Required"), FALSE, t("Check Failed. PEAR package Mail.php is required."));
	
    $this->prerequisite_check($group, "mime", ($this->filexists("Mail/mime.php")), 
        t("PEAR package Mail/mime.php "), t("Found"), t("Required"), FALSE, t("Check Failed. PEAR package Mail/mime.php is required."));

	$this->prerequisite_check($group, "request", ($this->filexists("HTTP/Request.php")), 
        t("PEAR package HTTP/Request.php "), t("Found"), t("Required"), FALSE, t("Check Failed. PEAR package HTTP/Request.php is required."));

    $this->prerequisite_check($group, "library", (is_writable( MODPATH . "picasa_uploader/libraries/" )), 
        t("libraries directory (picasa_uploader/libraries/) "), t("is writable"), t("should be writable"), FALSE, t("Check Failed. libraries directory (picasa_uploader/libraries/) it is not writable please CHOWN to apache user or CHMOD 666."));
		
	$val_cfg = $this->_read_cfg_file();
    $group = $form->group("group")->label("module settings");
	$group->input("intR")->label(t("Root album id"))->value($val_cfg['intR']);
	$group->input("intd")->label(t("# of Albums to Display"))->value($val_cfg['intd']);
	$group->input("resz")->label(t("Resize"))->value($val_cfg['resz']);
	$picasaG3_buton = "picasa_uploader.pbz";
	(file_exists(MODPATH . "picasa_uploader/libraries/".$picasaG3_buton)) ? $new_bt = "new" : $new_bt ="";
		$group->input("button_create")->type("hidden")->value($picasaG3_buton);
		$group->submit("submit")->value(t("Create $new_bt Picasa - G3 Button"));
    return $form;
  }
	
/* 
* read config file or state default 
*/
private function _read_cfg_file() {
	$cfg_f = array();
	$cfg_f['site'] = "not yet configured";
	$cfg_f['intR'] = 1;
	$cfg_f['intd'] = 10;
	$cfg_f['resz'] = 1600;
	$filename = MODPATH . "picasa_uploader/libraries/config.xml";
	if(file_exists($filename)){
		$xml = simplexml_load_file($filename);
		$cfg_f['site'] = $xml->siteUrl;
		$cfg_f['intR'] = (int)$xml->intRootAlbum;
		$cfg_f['intd'] = (int)$xml->intAlbumDisplayed;
		$cfg_f['resz'] = (int)$xml->intSize;
		}
	return $cfg_f;
}

/*
*Create/update config file
*/

 private function _write_cfg_file($intR,$intd,$resz) {
	$cfg_file = "config.xml";
	$cfg_path = MODPATH . "picasa_uploader/libraries/";
	$Handle = fopen($cfg_path.$cfg_file, 'w');
	$cfg_text = '<?xml version="1.0" encoding="iso-8859-1"?>
<config>
	<siteUrl>' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://'.$_SERVER['SERVER_NAME'].url::site().'rest</siteUrl>
	<intRootAlbum>'.$intR.'</intRootAlbum>
	<intAlbumDisplayed>'.$intd.'</intAlbumDisplayed>
	<intSize>'.$resz.'</intSize>
</config>';
	fwrite($Handle,$cfg_text);
	fclose($Handle);
 }
 /*
 *generate GUID for button ID http://code.google.com/apis/picasa/docs/button_api.html#naming
 */
 function setGUID () {
	function guid(){
		if (function_exists('com_create_guid')){
			return com_create_guid();
		}else{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = chr(123)// "{"
					.substr($charid, 0, 8).$hyphen
					.substr($charid, 8, 4).$hyphen
					.substr($charid,12, 4).$hyphen
					.substr($charid,16, 4).$hyphen
					.substr($charid,20,12)
					.chr(125);// "}"
			return $uuid;
		}
	}
	$guID = strtolower(guid());
	return $guID;
	}
/*
*Create pbf file 
*/
 private function _write_pbf_file($pbfID) {
	$pbf_file = $pbfID.".pbf";
	$pbf_path = MODPATH . "picasa_uploader/libraries/";
    $Handle = fopen($pbf_path.$pbf_file, 'w');

	$pbf_text = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<buttons format=\"1\" version=\"7\">
\t<button id=\"".$pbfID."\" type=\"dynamic\">
\t\t<icon name=\"".$pbfID."/gallery3\" src=\"pbz\"/>
\t\t<label>Gallery3</label>
\t\t<tooltip>Add to Gallery3 on ".$_SERVER['SERVER_NAME']."</tooltip>
\t\t<action verb=\"hybrid\">
\t\t\t<param name=\"url\" value=\"" . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . "://".$_SERVER['SERVER_NAME'].str_replace('index.php/','',url::site())."modules/picasa_uploader/\" />
\t\t</action>
\t</button>
</buttons>";
	fwrite($Handle, $pbf_text );
    fclose($Handle);

  }
  
/*
* Create pbz button which is in fact a zip archive
*/
 private function _write_pbz_file($pbzID) {
	$zip = new ZipArchive;
	$pbf_path = MODPATH . "picasa_uploader/libraries/";
	$pbf_file = $pbzID.".pbf";
	
	$psd_path = MODPATH . "picasa_uploader/css/";
	$psd_file = "button.psd";
	copy($psd_path.$psd_file,$pbf_path.$pbzID.".psd");
	$psd_file = $pbzID.".psd";
	
	$pbz_file = "picasa_uploader_".$_SERVER['SERVER_NAME'].".pbz";
	if(file_exists($pbf_path.$pbz_file)){
		unlink($pbf_path.$pbz_file);
		}
	$zip->open($pbf_path.$pbz_file,ZIPARCHIVE::CREATE);
		$zip->addFile($pbf_path.$psd_file, $psd_file);
		$zip->addFile($pbf_path.$pbf_file, $pbf_file);
	$zip->close();
	unlink($pbf_path.$psd_file);
	unlink($pbf_path.$pbf_file);
 }
 
 private function _show_install(){
   if(file_exists(MODPATH . "picasa_uploader/libraries/picasa_uploader_".$_SERVER['SERVER_NAME'].".pbz")):
	$show_bt = "<a href=\"picasa://importbutton/?url=http://".$_SERVER['SERVER_NAME'].str_replace('index.php/','',url::site())."modules/picasa_uploader/libraries/picasa_uploader_".$_SERVER['SERVER_NAME'].".pbz\"> Install Picasa - G3 Button</a>";
   else:
    $show_bt = "First create a button";
   endif;
	return $show_bt;
	}
  protected function get_edit_form_help() {
	$val_cfg = $this->_read_cfg_file();
    $help = '<fieldset>';
    $help .= '<legend>Help</legend><ul>';
    $help .= '<li><h3>Module Settings</h3>
		<br />
		<p><i>INFO</i>: REST API current address (<i> for config.xml</i>): '.$val_cfg['site'].'</p>
		<p><i>INFO</i>: <a href="http://pear.php.net/" target="_blank">PEAR - PHP Extension and Application Repository</a> help</p>
      <p><b>Root album id</b> - <i>specify the root album ID, this can be the Gallery root or an special album within you can upload your files (eg an album hidden from guest so after upload you can move your files into another album </i><br />
      <br /><b># of Albums to Display</b> - <i>Number of albums to display. If 0 display all</i><br />
	  <br /><b>Resize</b> - <i>Size (pixel) to resize uploaded photo. If 0 keep original size (resizes will be managed by G3 either way)</i><br />
          </li>
	  <li>
	  <b>Note: </b> <i style=" color: red;" >Number of uploaded files depends on upload_max_filesize an post_max_size directive of php config file!</i>
	  <i style="font-size:xx-small;">
		  Files are usually POSTed to the webserver in a format known as \'multipart/form-data\'. The post_max_size sets the upper limit on the amount of data that a script can accept in this manner. Ideally this value should be larger than the value that you set for upload_max_filesize.
<br />
	It\'s important to realize that upload_max_filesize is the sum of the sizes of all the files that you are uploading. post_max_size is the upload_max_filesize plus the sum of the lengths of all the other fields in the form plus any mime headers that the encoder might include. Since these fields are typically small you can often approximate the upload max size to the post max size. Your server\'s post_max_size is ' . ini_get('post_max_size') . '
	</i>
      </li>';
    $help .= '</ul></fieldset>';
    return $help;
  }
}
