function prepAuthorization(token) {
	//alert('prepAuthorization');
	var clientId = '367339197840.apps.googleusercontent.com';
	var scopes = 'https://www.googleapis.com/auth/blogger';
	var apiKey = 'AIzaSyABDIlFipmDN1FpEEthYF9zbTOKqh6cf24';
	//gapi.client.setApiKey(apiKey);
	window.setTimeout(function () {
		//alert('authorize');
		gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: true}, function (token) {
			//alert('calling back..');
			if (token && !token.error) {
				$('#googleLogin').hide();
				//populate the blog select
				googleAuthorized();
			}
			else {
				$('#googleLogin').show();
				$('#googleLogin').click(function () {
					gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: false}, function (token) {
						if(token && !token.error) {
							$('#googleLogin').hide();
							//populate the blog select
							googleAuthorized();
						}
					});
						});
			}
		})
	}, 1);
}

function grabBlogs()
{
	//alert('grabBlogs');
	var oauthToken = gapi.auth.getToken();
	$('#googleToken').val(oauthToken.access_token);
	/*
	if ('XDomainRequest' in window && window.XDomainRequest !== null) {
 
		// override default jQuery transport
		jQuery.ajaxSettings.xhr = function() {
			try { return new XDomainRequest(); }
			catch(e) { }
		};
 
		// also, override the support check
		jQuery.support.cors = true;
	}

	settings = {
		method: 'get',
		url: 'https://www.googleapis.com/blogger/v3/users/self/blogs?access_token='+encodeURIComponent(oauthToken.access_token),
		success: function(blogs) {
			for(i in blogs.items)
			{
				$('#blogList').append('<option value="'+blogs.items[i].id+'">'+blogs.items[i].name+'</option>');
			}
			$('#blogDiv').show();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert(textStatus);
			alert(errorThrown);
			alert(typeof jqXHR);
		},
		dataType: 'json'
	};
	$.ajax(settings);
	//*/
	//*
	if(typeof XDomainRequest != 'undefined')
	{
		var xhr = new XDomainRequest();
		//alert('onload');
		xhr.onload = function (e) {
			blogs = $.parseJSON(xhr.responseText);
			for(i in blogs.items)
                        {
				$('#blogList').append('<option value="'+blogs.items[i].id+'">'+blogs.items[i].name+'</option>');
			}
			$('#blogDiv').show();
		};
	}
	else
	{
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function (e) {
			//alert(xhr);
			if(xhr.readyState == 4)
			{
				if(xhr.status == 200)
				{
					blogs = $.parseJSON(xhr.responseText);
					for(i in blogs.items)
					{
						$('#blogList').append('<option value="'+blogs.items[i].id+'">'+blogs.items[i].name+'</option>');
					}
					$('#blogDiv').show();
				}
				else
				{
					alert('status: ' + xhr.status);
				}
			}
		};
	}
	//xhr.withCredentials = true;
	xhr.open('GET',
		'https://www.googleapis.com/blogger/v3/users/self/blogs' +
		'?access_token=' + encodeURIComponent(oauthToken.access_token));
	xhr.send();
	//*/
/*
	blogReq = gapi.client.blogger.blogs.listByUser('self');
	alert('blogReq');
	
	blogReq.execute(function(response) {
		alert('hi');
		alert(response);
		for(i in response)
			alert(i + ': ' + response[i]);
	});
	//for(i in blogs)
	//	alert(i + ': ' + blogs[i]);
*/
}

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
	
	function validateGoogleToken() {
		/**
		var myService = new google.gdata.blogger.BloggerService('picasa-gallery3-uploader');
		scope = 'http://www.blogger.com/feeds/';
		if(!google.accounts.user.checkLogin(scope))
		{
			$('#googleLogin').bind('click', function() {
				token = google.accounts.user.login(scope);
			});
		}
		else
		{
			myService.getBlogFeed(scope, function(feedRoot) {
				var feed = feedRoot.feed;
				var entries = feed.entry;
				for(i in entries)
				{
					alert(i + ': ' + entry[i]);
				}
			}, function (error) {
				alert(error);
			});
			$('#googleToken').val(token);
			$('#googleLogin').hide();
		}
		*/
		/**
		 * This version of the api doesn't yet support writing to the blog. In the near future, it will, but we can't use it yet.
		 * 
		var config = {
			'client_id': '367339197840.apps.googleusercontent.com',
			'scope': 'https://www.googleapis.com/auth/blogger'
		};
		gapi.auth.authorize(config, function() {
			token = gapi.auth.getToken();
			$('#googleToken').val(token);
			$('#googleLogin').hide();
			req = gapi.client.request({
				path: '/blogger/v2/users/self/blogs'
			});
			req.execute(function (data) {
				for(i in data.items)
				{
					$('#blogList').append('<option value="'+data.items[i].id+'">'+data.items[i].name+'</option>');
				}
				$('#blogList').show();
			});
		});
		*/
	}

