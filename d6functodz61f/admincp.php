<?php

/*
	[Discuz!] (C)2001-2007 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: admincp.php 13628 2008-04-29 04:07:22Z liuqiang $
*/

define('IN_ADMINCP', TRUE);
define('NOROBOT', TRUE);
require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./admin/global.func.php';
require_once DISCUZ_ROOT.'./admin/cpanel.share.php';
require_once DISCUZ_ROOT.'./include/cache.func.php';

include language('admincp');

$discuz_action = 211;

$adminsession = new AdminSession($discuz_uid, $groupid, $adminid, $onlineip);
$cpaccess = $adminsession->cpaccess;
if($cpaccess == 0 || (!$discuz_secques && $admincp['forcesecques'])) {
	require_once DISCUZ_ROOT.'./admin/login.inc.php';
} elseif($cpaccess == 1) {
	if($admin_password != '') {

		if(checkuserpassword($discuz_user, $admin_password)) {
			$adminsession->errorcount = -1;
			$adminsession->update();
			dheader('Location: admincp.php?'.cpurl('url', array('sid')));
		} else {
			$adminsession->errorcount ++;
			$adminsession->update();
			writelog('cplog', dhtmlspecialchars("$timestamp\t$discuz_userss\t$adminid\t$onlineip\t$action\tAUTHENTIFICATION(PASSWORD)"));
		}
	}
	require_once DISCUZ_ROOT.'./admin/login.inc.php';
} else {

	$username = !empty($username) ? dhtmlspecialchars($username) : '';
	$action = !empty($action) && is_string($action) ? trim($action) : '';
	$operation = !empty($operation) && is_string($operation) ? trim($operation) : '';
	$page = isset($page) ? intval((max(1, $page))) : 0;

	if(!empty($action) && !in_array($action, array('main', 'logs'))) {
		switch($cpaccess) {
			case 1:
				$extralog = 'AUTHENTIFICATION(ERROR #'.intval($adminsession['errorcount']).')';
				break;
			case 3:
				$extralog = implodearray(array('GET' => $_GET, 'POST' => $_POST), array('formhash', 'submit', 'addsubmit', 'admin_password', 'sid', 'action'));
				break;
			default:
				$extralog = '';
		}
		$extralog = trim(str_replace(array('GET={};', 'POST={};'), '', $extralog));
		$extralog = (($action == 'home' && isset($securyservice)) || ($action == 'insenz' && in_array($operation, array('register', 'binding')))) ? '' : $extralog;
		writelog('cplog', implode("\t", clearlogstring(array($timestamp,$discuz_userss,$adminid,$onlineip,$action,$extralog))));
		unset($extralog);
	}

	$isfounder = $adminsession->isfounder = isfounder();
	if(empty($action) || isset($frames)) {
		$extra = cpurl('url');
		$extra = $extra && $action ? $extra : (!empty($runwizard) ? 'action=runwizard' : 'action=home');
		require_once DISCUZ_ROOT.'./admin/main.inc.php';
	} elseif($action == 'logout') {
		$adminsession ->destroy();
		dheader("Location: $indexname");
	} else {

		if($radminid != $groupid) {
			$dactionarray = ($dactionarray = unserialize($db->result_first("SELECT disabledactions FROM {$tablepre}adminactions WHERE admingid='$groupid'"))) ? $dactionarray : array();
			if(in_array($action, $dactionarray) || ($operation && in_array($action.'_'.$operation, $dactionarray))) {
				cpheader();
				cpmsg('action_noaccess');
			}
		}
		
		
		
		/*D6功能移植to6.1 by horseluke
		原语句
		
		if(in_array($action, array('home', 'settings', 'members', 'groups', 'forums', 'threadtypes', 'threads', 'moderate', 'attachments', 'smilies', 'recyclebin', 'prune', 'styles', 'plugins', 'magics', 'medals', 'google', 'qihoo', 'video', 'announcements', 'faq', 'ecommerce', 'tradelog', 'creditwizard', 'jswizard', 'project', 'counter', 'misc', 'advertisements', 'insenz', 'logs', 'tools', 'checktools', 'upgrade')) || ($isfounder && in_array($action, array('runwizard', 'templates', 'database')))) {
		
       */
		
		if(in_array($action, array('home', 'settings', 'members', 'groups', 'forums', 'threadtypes', 'threads', 'moderate', 'attachments', 'smilies', 'recyclebin', 'prune', 'styles', 'plugins', 'magics', 'medals', 'google', 'qihoo', 'video', 'announcements', 'faq', 'ecommerce', 'tradelog', 'creditwizard', 'jswizard', 'project', 'counter', 'misc', 'advertisements', 'insenz', 'logs', 'tools', 'checktools', 'upgrade','d6func')) || ($isfounder && in_array($action, array('runwizard', 'templates', 'database')))) {

		//D6功能移植to6.1 by horseluke
			
			require_once DISCUZ_ROOT.'./admin/'.$action.'.inc.php';
			$title = 'cplog_'.$action.($operation ? '_'.$operation : '');
			if(!in_array($action, array('home', 'custommenu')) && lang($title, false)) {
				admincustom($title, cpurl('url'));
			}
		} else {
			cpheader();
			cpmsg('noaccess');
		}
		cpfooter();

	}
}

?>