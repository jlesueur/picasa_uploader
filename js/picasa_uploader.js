    function sf() { 
	  //frmLogin_err();
      document.getElementById('album').focus();     
    }
    
	function frmLogin_err() {
		if (document.getElementById('login_err').value == '1'){
			document.forms["relogin"].submit();
			//alert(document.forms['relogin'].name);
			}
		}
    
	function eraseCookie() {
		//alert(name);
		expires = -1;
		document.cookie = "a_tkG3= ; "+expires;
		//createCookie(name,"",-1);
	}
	function frmPicasa_display_new() {
      var n = document.getElementById('new');
      n.style.display = n.style.display == 'none' ? 'block' : 'none';
    }
    
    function frmPicasa_xhr_new() {
      albumTitle = document.getElementById('albumTitle');
      //e = document.getElementById('album');
	  

      if (albumTitle.value == '') {
        alert('Provide an album title.');
        return false;
      }
      var xmlhttp = false;      
      /*@cc_on @*/
      /*@if (@_jscript_version >= 5)
      // JScript gives us Conditional compilation, we can cope with old IE versions.
      // and security blocked creation of the objects.
      try {
        xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
      }
      catch(e) {
        try {
          xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
        } 
        catch(e) {
          xmlhttp = false;
        }
      }
      @end @*/    
      
      if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
        xmlhttp = new XMLHttpRequest();
      }
      
      var element = document.getElementById('album');
	  albumParent = element.options[element.selectedIndex].id;

      xmlhttp.open('GET', 'classes/add_album_xmlhttp.php?title=' + albumTitle.value + '&parent=' + albumParent);
      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {  
          eval(xmlhttp.responseText);
            o = new Option(arrXmlhttp[0][1], arrXmlhttp[0][0]);
            element.options[element.options.length] = o;
            element.selectedIndex = element.options.length - 1;
            frmPicasa_display_new();
			albumTitle.value = '';
        }
      }
      xmlhttp.send(null);    
    }
	function test_root() {
		var element = document.getElementById('album');
		albumToUp = element.options[element.selectedIndex].id;
		if(albumToUp == 1){
			alert('You cannot upload files to Gallery Root');
			return false;}
		}
		
	document.onkeydown = function(){
	//this disable F5 in Picasa minibrowser (based on IE) 
		if(window.event && window.event.keyCode == 116)
				{ // Capture and remap F5
			window.event.keyCode = 505;
			  }
		if(window.event && window.event.keyCode == 505)
				{ // New action for F5
			return false;
				// Must return false or the browser will refresh anyway
			}
	}
