<?php

/*
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

define('DISCUZ_ROOT', './');
require './include/common.inc.php';


if ($adminid != 1){
	showmessage('你不是管理员，无法进行设置操作', NULL, 'NOPERM');
}

$pid = isset($pid) ? intval($pid) : '0';
if (empty($pid)){
     showmessage('不存在该帖子,无法修改！', NULL, 'NOPERM');
}


$postdetail=array();
$postdetail=$db->fetch_first("SELECT pid,tid,first,author,authorid,useip FROM {$tablepre}posts WHERE pid='$pid' LIMIT 1");
if (empty($postdetail)){
     showmessage('不存在该帖子,无法修改！', NULL, 'NOPERM');
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
	         showmessage('非法调用参数或者含有系统屏蔽的词语，无法修改，请返回。',$referer);
     	}
	    $postdetailnew['uid']=$db->result_first("SELECT uid FROM {$tablepre}members WHERE username='{$postdetailnew['username']}'");
		if (empty($postdetailnew['uid'])){
	         showmessage('该用户不存在，无法修改帖子。',$referer);	
	    }
	}elseif ($searchkey == 'uid' && is_numeric($keyword)){
		$postdetailnew['uid'] = intval($keyword);
	    $postdetailnew['username'] = $db->result_first("SELECT username FROM {$tablepre}members WHERE uid='{$postdetailnew['uid']}'");
		if (empty($postdetailnew['username'])){
	         showmessage('该用户不存在，无法修改帖子。',$referer);	
	    }
    }else{
	         showmessage('非法调用参数，无法修改帖子。',$referer);
	}
	if ($postdetailnew['uid']==$postdetail['authorid']){
	         showmessage('原发贴人就是你要更改的用户，更改失败！',$referer);	
	}
	$db->query("UPDATE {$tablepre}posts SET author='{$postdetailnew['username']}',authorid='{$postdetailnew['uid']}' WHERE pid='{$pid}' LIMIT 1");
	$db->query("UPDATE {$tablepre}members SET posts=posts-1 WHERE uid='{$postdetail['authorid']}' LIMIT 1");
	$db->query("UPDATE {$tablepre}members SET posts=posts+1 WHERE uid='{$postdetailnew['uid']}' LIMIT 1");
	if ($postdetail['first']==1){		
	     $db->query("UPDATE {$tablepre}threads SET author='{$postdetailnew['username']}',authorid='{$postdetailnew['uid']}' WHERE tid='{$postdetail['tid']}' LIMIT 1");	
	}
	showmessage('成功更改发帖人为：'.$postdetailnew['username'].'。',$referer);

}

?>