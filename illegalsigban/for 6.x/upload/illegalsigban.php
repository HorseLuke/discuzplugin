<?php

/*
  Copyright 2008 Horse Luke������飩.

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.

*/

require_once './include/common.inc.php';


if(!$discuz_uid) {
	showmessage('not_loggedin', NULL, 'HALTED');
}

$navtitle = "����Υ��ǩ�� - ";

$illegalsigbancache = "./forumdata/cache/cache_illegalsigbanuidlist.php";
if (!file_exists($illegalsigbancache)) {
         $illegalsigbanuidlistnew = $db->result_first("SELECT data FROM {$tablepre}caches WHERE cachename = 'cacheillegalsigbanuidlist' LIMIT 1");
		 $illegalsigbanuidlistnew = empty($illegalsigbanuidlistnew) ? '0' : $illegalsigbanuidlistnew;
	     simplecacherefresh('illegalsigbanuidlist',$illegalsigbanuidlistnew);
		 showmessage("����ˢ�²������ɹ��������²�����");
}


if(empty($action) || $action == 'admin') {
    if($adminid != 1) {
	    showmessage('������ԽȨʹ�ã��뷵�ء�', NULL, 'HALTED');
    }
	if(submitcheck('cacherefreshsubmit')) {
		 $illegalsigbanuidlistnew = '0';
		 $query = $db->query("SELECT banuid FROM {$tablepre}illegalsigbanlog WHERE banstatus=1");
		 while($illegalsigbanuid = $db->fetch_array($query)) {
               $illegalsigbanuidlistnew = $illegalsigbanuidlistnew.','.$illegalsigbanuid['banuid'];
         }
         $db->query("UPDATE {$tablepre}caches SET data='$illegalsigbanuidlistnew' WHERE cachename = 'cacheillegalsigbanuidlist' LIMIT 1");
	     simplecacherefresh('illegalsigbanuidlist',$illegalsigbanuidlistnew);
		 showmessage("����ˢ�²������ɹ�����ת�У����Ժ�","illegalsigban.php?action=admin");
	}elseif(submitcheck('unbanallsubmit')) {
	     $unbaninfo = $discuz_userss.'|'.$timestamp;
	     $db->query("UPDATE {$tablepre}illegalsigbanlog SET banstatus='0',unbaninfo='$unbaninfo' WHERE banstatus='1'");
         $db->query("UPDATE {$tablepre}caches SET data='0' WHERE cachename = 'cacheillegalsigbanuidlist' LIMIT 1");
	     simplecacherefresh('illegalsigbanuidlist','0');
		 showmessage("�������ǩ������״̬�ɹ�����ת�У����Ժ�","illegalsigban.php?action=admin");
	}else{
    	include_once './forumdata/cache/cache_illegalsigbanuidlist.php';
	    $illegalsigbanuidlist2 = array();
		$illegalsigbanuidlist2 = explode(',', $illegalsigbanuidlist);
		unset($illegalsigbanuidlist2['0']);
	    include template('illegalsigban_admin');
	}


} elseif($action == 'ban') {


    if($adminid==0 || empty($uid)) {
	    showmessage('����Ȩִ������ǩ���������������ȷ���뷵�ء�', NULL, 'HALTED');
    }
	
	$uid = intval($uid);
    $username = $db->result_first("SELECT username FROM {$tablepre}members WHERE {$tablepre}members.uid='$uid' LIMIT 1");
    if (empty($username)){
        showmessage('���û������ڣ��뷵�ء�', dreferer());
    }
	$banstatus = $db->result_first("SELECT banstatus FROM {$tablepre}illegalsigbanlog WHERE banuid='$uid' AND banstatus='1' LIMIT 1");
	$banstatus = empty($banstatus) ? 0 : 1;
	if(submitcheck('bansubmit')) {
         if($banstatus==$banstatusnew) {
	         showmessage('����������߷�����ͻ���뷵���޸ġ�', 'javascript:history.back()');
		 }
		 if($banstatusnew==1) {
		     $banreasonnew=empty($banreasonnew) ? '' :dhtmlspecialchars($banreasonnew);
		     $db->query("INSERT INTO {$tablepre}illegalsigbanlog (banstatus,banuid,operateruid,operater,bandatetime,banreason) VALUES ('$banstatusnew', '$uid', '$discuz_uid', '$discuz_userss','$timestamp', '$banreasonnew')");
		     include_once './forumdata/cache/cache_illegalsigbanuidlist.php';
		     $illegalsigbanuidlist=$illegalsigbanuidlist.','.$uid;
			 $db->query("UPDATE {$tablepre}caches SET data='$illegalsigbanuidlist' WHERE cachename = 'cacheillegalsigbanuidlist' LIMIT 1");
		     simplecacherefresh('illegalsigbanuidlist',$illegalsigbanuidlist);
		     unset($illegalsigbanuidlist);
			 if($sendreasonpm=='1'){
			       $subject='���ĸ���ǩ��������Υ���������Ա���Σ��뾡���޸ġ�';
				   $message='���ĸ���ǩ��������Υ���������Ա���Ρ�����ϸ�Ķ��������£��������޸ĸ���ǩ�������β������ɣ�'.$banreasonnew; 
			       sendpm($uid, $subject, $message, $discuz_uid, $discuz_userss);
			 }
	         showmessage("�ɹ�����{$username}��ǩ����","space.php?action=viewpro&uid={$uid}");
		 }elseif($banstatusnew==0){
		     $unbaninfo = $discuz_userss.'|'.$timestamp;
		     $db->query("UPDATE {$tablepre}illegalsigbanlog SET banstatus='$banstatusnew',unbaninfo='$unbaninfo' WHERE banuid='$uid'");
			 
		 	 include_once './forumdata/cache/cache_illegalsigbanuidlist.php';
	         $illegalsigbanuidlist2 = array();
		     $illegalsigbanuidlist2 = explode(',', $illegalsigbanuidlist);
			 $unbanuidkey = array_search($uid,$illegalsigbanuidlist2);
			 unset($illegalsigbanuidlist2[$unbanuidkey]);
			 $illegalsigbanuidlistnew=implode(',',$illegalsigbanuidlist2);
			 $db->query("UPDATE {$tablepre}caches SET data='$illegalsigbanuidlistnew' WHERE cachename = 'cacheillegalsigbanuidlist' LIMIT 1");
		     simplecacherefresh('illegalsigbanuidlist',$illegalsigbanuidlistnew);
		     unset($illegalsigbanuidlist,$illegalsigbanuidlist2,$illegalsigbanuidlistnew);
			 
	         showmessage("�ɹ��������{$username}��ǩ����", "space.php?action=viewpro&uid={$uid}");
		 }else{
	         showmessage('undefined_action', NULL, 'HALTED');
		 }
	}
	
    include template('illegalsigban_ban');

/*��ѯ���ܣ���δ����
} elseif($action == 'log'  && $adminid>0) {
    $extrasql='';
    $view=(empty($view)|| !in_array($view,array('person','all')))? 'person' : $view;
	if($view=='person'){
	    $uid = empty($uid) ? $discuz_uid : intval($uid);
		$extrasql=' banid='.$uid;
        

	}elseif($view=='all' && $adminid==1){
	    $illegalsigbanuidlist2 = array();
		$illegalsigbanuidlist2 = explode(',', $illegalsigbanuidlist);
		unset($illegalsigbanuidlist2['0']);
	}


	
    include template('illegalsigban_log');

*/

} else {
	showmessage('undefined_action', NULL, 'HALTED');
}








function simplecacherefresh($cachename = '',$cachevalue = '0') {

    if (empty($cachename)) {
	   exit('Syntax Error or Illegal. Check the code of using simplecacherefresh function.');
    }
    $prefix = 'cache_';
    $script = $cachename;
    $dir = './forumdata/cache/';
    if(!is_dir($dir)) {
         @mkdir($dir, 0777);
    }
    if($fp = @fopen("$dir$prefix$script.php", 'wb')) {
		fwrite($fp, "<?php\n".
		    "//Discuz! cache file, DO NOT modify me!\n".
                     "if(!defined('IN_DISCUZ')) {\n".
                     "exit('Access Denied');\n".
                     "}\n".
			"\n\$".$cachename.
                        "='".$cachevalue.
			"';\n?>");
		fclose($fp);
    } else {
		exit('Can not write to cache files, please check directory ./forumdata/ and ./forumdata/cache/ .');
    }

}




?>