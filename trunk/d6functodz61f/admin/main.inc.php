<?php

/*
	[Discuz!] (C)2001-2007 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: main.inc.php 12715 2008-03-08 05:06:03Z monkey $
*/

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
        exit('Access Denied');
}

echo <<<EOT

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title>Discuz! Administrator's Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=$charset">
<meta content="Comsenz Inc." name="Copyright" />
<link rel="stylesheet" href="images/admincp/admincp.css" type="text/css" media="all" />
<script src="include/javascript/common.js" type="text/javascript"></script>
</head>
<body style="margin: 0px" scroll="no">
<div id="append_parent"></div>
<table cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr>
<td colspan="2" height="90">
<div class="mainhd">
<div class="logo">Discuz! Administrator's Control Panel</div>
<div class="uinfo">
<p>$lang[header_welcome], <em>$discuz_user</em> [ <a href="admincp.php?action=logout&sid=$sid" target="_top">$lang[header_logout]</a> ]</p>
<p class="btnlink"><a href="$indexname" target="_blank">$lang[header_bbs]</a></p>
</div>
<div class="navbg"></div>
<div class="nav">
<ul id="topmenu">

EOT;

showheader('index', 'home');
showheader('global', 'settings&operation=basic');
showheader('forum', 'forums');
showheader('user', 'members');
showheader('topic', 'moderate&operation=threads');
showheader('extended', 'plugins');
showheader('misc', 'announcements');
showheader('adv', 'advertisements');
showheader('tools', $isfounder && checkpermission('dbimport', 0) ? 'database&operation=export' : 'counter');






//D6功能移植to6.1f by horseluke 
$lang['header_d6func']='D6功能移植';
showheader('d6func', 'd6func&operation=intro');
//D6功能移植to6.1f by horseluke 






if($isfounder) {
	//echo '<li><em><a href="#" class="diffcolor" onclick="window.open(\''.UC_API.'\')">'.$lang['header_uc'].'</a></em></li>';
}

echo <<<EOT

</ul>
<div class="currentloca">
<p id="admincpnav"></p>
</div>
<div class="navbd"></div>
<div class="sitemapbtn">
	<span id="add2custom"></span>
	<a href="###" id="cpmap" onclick="showMap();return false;"><img src="images/admincp/btn_map.gif" title="$lang[admincp_sitemap]" width="72" height="18" /></a>
</div>
</div>
</div>
</td>
</tr>
<tr>
<td valign="top" width="160" class="menutd">
<div id="leftmenu" class="menu">

EOT;

require_once DISCUZ_ROOT.'./admin/menu.inc.php';

echo <<<EOT

</div>
</td>
<td valign="top" width="100%"class="mask"><iframe src="admincp.php?$extra&sid=$sid" name="main" width="100%" height="100%" frameborder="0"scrolling="yes" style="overflow: visible;"></iframe></td>
</tr>
</table>

<div class="copyright">
	<p>Powered by <a href="http://www.discuz.net/" target="_blank">Discuz!</a> $version</p>
	<p>&copy; 2001-2008, <a href="http://www.comsenz.com/" target="_blank">Comsenz Inc.</a></p>
</div>

<div id="cpmap_menu" class="custom" style="display:none">
	<div class="cside">
		<h3><span class="ctitle1">$lang[custommenu]</span><a href="#" onclick="toggleMenu('misc', 'misc&operation=custommenu');hideMenu();" class="cadmin">$lang[admin]</a></h3>
		<ul class="cslist" id="custommenu"></ul>
	</div>
	<div class="cmain" id="cmain"></div>
	<div class="cfixbd"></div>
</div>

<script type="text/JavaScript">


    <!--D6功能移植to6.1f by horseluke--> 
	var headers = new Array('index', 'global', 'forum', 'user', 'topic', 'extended', 'misc', 'tools', 'adv','d6func');
	<!--D6功能移植to6.1f by horseluke-->
	
	function toggleMenu(key, url) {
		if(key == 'index' && url == 'home') {
			parent.location.href = 'admincp.php?frames=yes';
			return false;
		}
		for(var k in headers) {
			if($('menu_' + headers[k])) {
				$('menu_' + headers[k]).style.display = headers[k] == key ? '' : 'none';
			}
		}
		var lis = $('topmenu').getElementsByTagName('li');
		for(var i = 0; i < lis.length; i++) {
			if(lis[i].className == 'navon') lis[i].className = '';
		}
		$('header_' + key).parentNode.parentNode.className = 'navon';
		if(url) {
			parent.main.location = 'admincp.php?action=' + url;
			var hrefs = $('menu_' + key).getElementsByTagName('a');
			for(var j = 0; j < hrefs.length; j++) {
				hrefs[j].className = hrefs[j].href.substr(hrefs[j].href.indexOf('admincp.php?action=') + 19) == url ? 'tabon' : (hrefs[j].className == 'tabon' ? '' : hrefs[j].className);
			}
		}
		return false;
	}
	function initCpMenus(menuContainerid) {
		var key = '';
		var hrefs = $(menuContainerid).getElementsByTagName('a');
		for(var i = 0; i < hrefs.length; i++) {
			if(menuContainerid == 'leftmenu' && !key && hrefs[i].href.substr(hrefs[i].href.indexOf('admincp.php?action=') + 12) == '$extra') {
				key = hrefs[i].parentNode.parentNode.id.substr(5);
				hrefs[i].className = 'tabon';
			}
			if(!hrefs[i].getAttribute('ajaxtarget')) hrefs[i].onclick = function() {
				if(menuContainerid != 'custommenu') {
					var lis = $(menuContainerid).getElementsByTagName('li');
					for(var k = 0; k < lis.length; k++) {
						if(lis[k].firstChild.className != 'menulink') lis[k].firstChild.className = '';
					}
					if(this.className == '') this.className = menuContainerid == 'leftmenu' ? 'tabon' : 'bold';
				}
				if(menuContainerid != 'leftmenu') {
					var hk, currentkey;
					var leftmenus = $('leftmenu').getElementsByTagName('a');
					for(var j = 0; j < leftmenus.length; j++) {
						hk = leftmenus[j].parentNode.parentNode.id.substr(5);
						if(this.href.indexOf(leftmenus[j].href) != -1) {
							leftmenus[j].className = 'tabon';
							if(hk != 'index') currentkey = hk;
						} else {
							leftmenus[j].className = '';
						}
					}
					if(currentkey) toggleMenu(currentkey);
					hideMenu();
				}
			}
		}
		return key;
	}
	var header_key = initCpMenus('leftmenu');
	toggleMenu(header_key ? header_key : 'index');
	function initCpMap() {
		var ul, hrefs, s;
		s = '<ul class="cnote"><li><img src="images/admincp/btn_map.gif" /></li><li> $lang[custommenu_tips]</li></ul><table class="cmlist" id="mapmenu"><tr>';

		for(var k in headers) {
			if(headers[k] != 'index') {
				s += '<td valign="top"><ul class="cmblock"><li><h4>' + $('header_' + headers[k]).innerHTML + '</h4></li>';
				ul = $('menu_' + headers[k]);
				hrefs = ul.getElementsByTagName('a');
				for(var i = 0; i < hrefs.length; i++) {
					s += '<li><a href="' + hrefs[i].href + '" target="' + hrefs[i].target + '" k="' + headers[k] + '">' + hrefs[i].innerHTML + '</a></li>';
				}
				s += '</ul></td>';
			}
		}
		s += '</tr></table>';
		return s;
	}
	$('cmain').innerHTML = initCpMap();
	initCpMenus('mapmenu');
	var cmcache = false;
	function showMap() {
		showMenu('cpmap', true, 3, 3);
		if(!cmcache) ajaxget('admincp.php?action=misc&operation=custommenu&' + Math.random(), 'custommenu', '');
	}
	function resetEscAndF5(e) {
		e = e ? e : window.event;
		actualCode = e.keyCode ? e.keyCode : e.charCode;
		if(actualCode == 27) {
			if($('cpmap_menu').style.display == 'none') {
				showMap();
			} else {
				hideMenu();
			}
		}
		if(actualCode == 116 && parent.main) {
			parent.main.location.reload();
			if(document.all) {
				e.keyCode = 0;
				e.returnValue = false;
			} else {
				e.cancelBubble = true;
				e.preventDefault();
			}
		}
	}
	_attachEvent(document.documentElement, 'keydown', resetEscAndF5);
</script>

</body>
</html>

EOT;

?>