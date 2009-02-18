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

define('DISCUZ_ROOT', './');
require './include/common.inc.php';


if ($adminid != 1){
	showmessage('�㲻�ǹ���Ա���޷��������ò���', NULL, 'NOPERM');
}

$pid = isset($pid) ? intval($pid) : '0';
if (empty($pid)){
     showmessage('�����ڸ�����,�޷��޸ģ�', NULL, 'NOPERM');
}


$postdetail=array();
$postdetail=$db->fetch_first("SELECT pid,tid,first,author,authorid,useip FROM {$tablepre}posts WHERE pid='$pid' LIMIT 1");
if (empty($postdetail)){
     showmessage('�����ڸ�����,�޷��޸ģ�', NULL, 'NOPERM');
}

$referer = $boardurl.'viewthread.php?tid='.$postdetail['tid'].'#pid'.$postdetail['pid'];

if (!submitcheck('changepostersubmit')){
      include template('changeposter');
}else{
    $postdetailnew=array();
	if ($searchkey == 'username'){
		$postdetailnew['username'] = trim($keyword);
	    $guestexp = '\xA1\xA1|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
	    $censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($censoruser = trim($censoruser)), '/')).')$/i';
	    if(preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is", $postdetailnew['username']) || ($censoruser && @preg_match($censorexp, $postdetailnew['username'])) || empty($postdetailnew['username'])) {
	         showmessage('�Ƿ����ò������ߺ���ϵͳ���εĴ���޷��޸ģ��뷵�ء�',$referer);
     	}
	    $postdetailnew['uid']=$db->result_first("SELECT uid FROM {$tablepre}members WHERE username='{$postdetailnew['username']}'");
		if (empty($postdetailnew['uid'])){
	         showmessage('���û������ڣ��޷��޸����ӡ�',$referer);	
	    }
	}elseif ($searchkey == 'uid' && is_numeric($keyword)){
		$postdetailnew['uid'] = intval($keyword);
	    $postdetailnew['username'] = $db->result_first("SELECT username FROM {$tablepre}members WHERE uid='{$postdetailnew['uid']}'");
		if (empty($postdetailnew['username'])){
	         showmessage('���û������ڣ��޷��޸����ӡ�',$referer);	
	    }
    }else{
	         showmessage('�Ƿ����ò������޷��޸����ӡ�',$referer);
	}
	if ($postdetailnew['uid']==$postdetail['authorid']){
	         showmessage('ԭ�����˾�����Ҫ���ĵ��û�������ʧ�ܣ�',$referer);	
	}
	$db->query("UPDATE {$tablepre}posts SET author='{$postdetailnew['username']}',authorid='{$postdetailnew['uid']}' WHERE pid='{$pid}' LIMIT 1");
	$db->query("UPDATE {$tablepre}members SET posts=posts-1 WHERE uid='{$postdetail['authorid']}' LIMIT 1");
	$db->query("UPDATE {$tablepre}members SET posts=posts+1 WHERE uid='{$postdetailnew['uid']}' LIMIT 1");
	if ($postdetail['first']==1){		
	     $db->query("UPDATE {$tablepre}threads SET author='{$postdetailnew['username']}',authorid='{$postdetailnew['uid']}' WHERE tid='{$postdetail['tid']}' LIMIT 1");	
	}
	showmessage('�ɹ����ķ�����Ϊ��'.$postdetailnew['username'].'��',$referer);

}

?>