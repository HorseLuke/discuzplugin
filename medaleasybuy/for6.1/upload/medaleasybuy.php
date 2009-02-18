<?php

/*
medaleasybuy��Ҫ�������ͬ�۸�����ѫ�µĹ��ܡ����ܽ�Ϊ�򵥡�
for dz6.1/6.1f

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

$closed=1;                    //�Ƿ�رղ����0Ϊ�񡣹���Ա��������
$medalcanbuyid=array(1,7,8,9);     //�ɹ����medalid
$medalcanbuyprice=10;         //ͳһ����۸�
$medalcanbuyextcreditsid=1;        //������ֵ�λid


require_once './include/common.inc.php';

if(!$discuz_uid) {
	showmessage('not_loggedin', NULL, 'HALTED');
}

if($closed && ($adminid != 1)){
	showmessage('����ر��У��Ժ������� ^_^');
}


if(empty($action)) {
	$medallist = array();
	$query = $db->query("SELECT * FROM {$tablepre}medals WHERE available='1' ORDER BY displayorder");
	while($medal = $db->fetch_array($query)) {
	  if(in_array($medal['medalid'],$medalcanbuyid)){
		$medal['permission'] = formulaperm($medal['permission'], 2);
		$medallist[] = $medal;
	  }
	}
	$noticesentense='�����軨��'.$extcredits[$medalcanbuyextcreditsid]['title'].$medalcanbuyprice.'��';

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
		showmessage('���ѫ���ǲ��ܹ���� ^_^','javascript:history.back()');
	}
	formulaperm($medal['permission'], 1) && $medal['permission'] = formulaperm($medal['permission'], 2);
	$noticesentense='һ����������۳�'.$extcredits[$medalcanbuyextcreditsid]['title'].$medalcanbuyprice.'�������ʼ��';
	if(submitcheck('medalsubmit')) {
	    if ($GLOBALS['extcredits'.$medalcanbuyextcreditsid] < $medalcanbuyprice){
		    showmessage('���������ˣ���ȥ��������� ^_^','javascript:history.back()');
		}
		$medaldetail = $db->fetch_first("SELECT medalid FROM {$tablepre}medallog WHERE uid='$discuz_uid' AND medalid='$medalid' AND type!='3'");
		if($medaldetail['medalid']) {
			showmessage('medal_apply_existence', 'medaleasybuy.php');
		} else {		
			$userip=dhtmlspecialchars($_SERVER['REMOTE_ADDR']);
			$db->query("INSERT INTO {$tablepre}medaleasybuylog (uid,medalid,buytime,expiration,buyip,moneyamount,extcreditsid) VALUES ('$discuz_uid', '$medalid', '$timestamp', '$expiration', '$userip','$medalcanbuyprice', '$medalcanbuyextcreditsid')");
			$expiration = empty($medal['expiration'])? 0 : $timestamp + $medal['expiration'] * 86400;
			$db->query("INSERT INTO {$tablepre}medallog (uid, medalid, type, dateline, expiration, status) VALUES ('$discuz_uid', '$medalid', '1', '$timestamp', '$expiration', '1')");
            $db->query("UPDATE {$tablepre}members SET extcredits{$medalcanbuyextcreditsid}=extcredits{$medalcanbuyextcreditsid}-$medalcanbuyprice WHERE uid='$discuz_uid'");
			$usermedallist = $db->result_first("SELECT medals FROM {$tablepre}memberfields WHERE uid='$discuz_uid'");
			$newmedal = empty($medal['expiration']) ? $medalid : $medalid.'|'.$medal['expiration'];
			$medalsnew= $usermedallist.'\t'.$newmedal;
            $db->query("UPDATE {$tablepre}memberfields SET medals='$medalsnew' WHERE uid='$discuz_uid'");			
			showmessage('����ɹ���', 'medaleasybuy.php');	
		}
	}

} else {
	showmessage('undefined_action', NULL, 'HALTED');
}

include template('medaleasybuy');

?>