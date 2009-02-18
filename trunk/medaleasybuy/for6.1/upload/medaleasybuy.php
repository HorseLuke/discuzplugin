<?php

/*
Medal EasyBuy Ver 0.0.2 Build 20090119 For Discuz! 6.1/6.1F - Frontpage

  Copyright 2008 Horse Luke（竹节虚）.

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
	showmessage('not_loggedin', NULL, 'NOPERM');
}
@include_once './forumdata/cache/cache_medaleasybuy.php';
$open=empty($medaleasybuy_basicsettings['open']) ? 0 : $medaleasybuy_basicsettings['open'];
$medalcanbuyextcreditsid=empty($medaleasybuy_basicsettings['buyextcreditsid']) ? 2 : $medaleasybuy_basicsettings['buyextcreditsid'];
$medalcanbuyid = empty($medaleasybuy_medallist['medalcanbuylistidcache']) ? array() : $medaleasybuy_medallist['medalcanbuylistidcache'];
//$medalcanbuyprice=10;         //统一购买价格

$navtitle = "勋章购买易 - ";

if((!$open) && ($adminid != 1)){
	showmessage('插件关闭中，稍候再来吧 ^_^');
}

if ((!file_exists('./forumdata/cache/cache_medaleasybuy.php'))  &&  $adminid == 1 ) {
      echo '<script>alert(\'插件的缓存文件丢失，请进入进入后台重新刷新缓存！\n（当前登录权限：管理员）\');</script> ';
}


if(empty($action)) {
	$medallist = array();
	$query = $db->query("SELECT * FROM {$tablepre}medals WHERE available='1' ORDER BY displayorder");
	while($medal = $db->fetch_array($query)) {
	  if(in_array($medal['medalid'],$medalcanbuyid)){
		$medal['permission'] = formulaperm($medal['permission'], 2);
		$medal['medalcanbuyprice'] = empty($medaleasybuy_medallist['medalcanbuylist'][$medal['medalid']]['moneyamount']) ? 0 : $medaleasybuy_medallist['medalcanbuylist'][$medal['medalid']]['moneyamount'];
		if ($medal['medalcanbuyprice'] > 0){
	    	$medal['noticesentense']='购买需花费'.$extcredits[$medalcanbuyextcreditsid]['title'].$medal['medalcanbuyprice'].'。';
		}else{
		    $medal['noticesentense']='在免费获取此勋章同时，将赠送'.$extcredits[$medalcanbuyextcreditsid]['title'].-($medal['medalcanbuyprice']).'。';
		}
		$medallist[$medal['medalid']] = $medal;
	  }
	}
	

} elseif($action == 'log') {

	$page = max(1, intval($page));
	$start_limit = ($page - 1) * $tpp;
	@include_once DISCUZ_ROOT.'./forumdata/cache/cache_medals.php';
	$logstotalnum = $db->result_first("SELECT COUNT(*) FROM {$tablepre}medaleasybuylog WHERE uid='$discuz_uid'");
	$multipage = multi($logstotalnum, $tpp, $page, "medaleasybuy.php?action=log");
	$query = $db->query("SELECT mebuy.*, m.image FROM {$tablepre}medaleasybuylog mebuy
		LEFT JOIN {$tablepre}medals m USING (medalid)
		WHERE mebuy.uid='$discuz_uid' ORDER BY mebuy.buytime DESC LIMIT $start_limit,$tpp");
	$medallogs = array();
	while($medallog = $db->fetch_array($query)) {
		$medallog['name'] = $_DCACHE['medals'][$medallog['medalid']]['name'];
		$medallog['buytime'] = gmdate("$dateformat $timeformat", $medallog['buytime'] + $timeoffset * 3600);
		$medallog['expiration'] = !empty($medallog['expiration']) ? gmdate("$dateformat $timeformat", $medallog['expiration'] + $timeoffset * 3600) : '';
		$medallogs[] = $medallog;
	}

} elseif($action == 'apply') {
	$medalid = intval($medalid);
	$formulamessage = '';
	$medal = $db->fetch_first("SELECT * FROM {$tablepre}medals WHERE medalid='$medalid' AND available='1'");
	if($medal['type'] || !in_array($medal['medalid'],$medalcanbuyid)) {
		showmessage('这个勋章是不能购买的 ^_^','javascript:history.back()');
	}
	formulaperm($medal['permission'], 1) && $medal['permission'] = formulaperm($medal['permission'], 2);
	$medalcanbuyprice = empty($medaleasybuy_medallist['medalcanbuylist'][$medal['medalid']]['moneyamount']) ? 0 : $medaleasybuy_medallist['medalcanbuylist'][$medal['medalid']]['moneyamount'];
	if ($medalcanbuyprice > 0){
	    	$noticesentense='购买需花费'.$extcredits[$medalcanbuyextcreditsid]['title'].$medalcanbuyprice.'。点击开始。';
	}else{
		    $noticesentense='在免费获取此勋章同时，将增送'.$extcredits[$medalcanbuyextcreditsid]['title'].-($medalcanbuyprice).'。点击开始。';
	}
	//$noticesentense='一旦点击，将扣除'.$extcredits[$medalcanbuyextcreditsid]['title'].$medalcanbuyprice.'。点击开始。';
	if(submitcheck('medalsubmit')) {
	    if ( ($medalcanbuyprice > 0) && ($GLOBALS['extcredits'.$medalcanbuyextcreditsid] < $medalcanbuyprice)){
		    showmessage('不够银子了，先去提款机那里吧 ^_^','javascript:history.back()');
		}
		$medaldetail = $db->fetch_first("SELECT medalid FROM {$tablepre}medallog WHERE uid='$discuz_uid' AND medalid='$medalid' AND type ='2'");    //type==2,审核中，type==3，审核不通过，type==1，审核通过。type==0，人工授予。
		$hasmedal = $db->result_first("SELECT medals FROM {$tablepre}memberfields WHERE uid='$discuz_uid'");
		$hasmedalarray = explode("\t",$hasmedal);
		if(in_array($medalid,$hasmedalarray)){
			showmessage('你已经拥有这个勋章啦！', 'medaleasybuy.php');
		}
		elseif($medaldetail['medalid']) {
			showmessage('你曾经申请过该勋章，请等待管理员后台审核。', 'medaleasybuy.php');
		} else {		
			$userip=dhtmlspecialchars($_SERVER['REMOTE_ADDR']);
			$expiration = empty($medal['expiration'])? 0 : $timestamp + $medal['expiration'] * 86400;
			$status= empty($medal['expiration'])? 0 : 1;
			$db->query("INSERT INTO {$tablepre}medaleasybuylog (uid,medalid,buytime,expiration,buyip,moneyamount,extcreditsid) VALUES ('$discuz_uid', '$medalid', '$timestamp', '$expiration', '$userip','$medalcanbuyprice', '$medalcanbuyextcreditsid')");
			$db->query("INSERT INTO {$tablepre}medallog (uid, medalid, type, dateline, expiration, status) VALUES ('$discuz_uid', '$medalid', '1', '$timestamp', '$expiration', '$status')");
            $db->query("UPDATE {$tablepre}members SET extcredits{$medalcanbuyextcreditsid}=extcredits{$medalcanbuyextcreditsid}-($medalcanbuyprice) WHERE uid='$discuz_uid'");
			$usermedallist = $db->result_first("SELECT medals FROM {$tablepre}memberfields WHERE uid='$discuz_uid'");
			$newmedal = empty($medal['expiration']) ? $medalid : $medalid.'|'.$medal['expiration'];
			$medalsnew= empty($usermedallist) ? $newmedal : $usermedallist."\t".$newmedal;
            $db->query("UPDATE {$tablepre}memberfields SET medals='$medalsnew' WHERE uid='$discuz_uid'");			
			showmessage('购买成功！', 'medaleasybuy.php');	
		}
	}

} else {
	showmessage('undefined_action', NULL, 'HALTED');
}

include template('medaleasybuy');

?>