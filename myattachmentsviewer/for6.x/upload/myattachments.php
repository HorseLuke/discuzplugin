<?php

/*
  我的附件查询for discuz7.0 Beta测试开发版

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

define('NOROBOT', TRUE);

include './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/attachment.func.php';

if(!$discuz_uid) {
	showmessage('not_loggedin', NULL, 'NOPERM');
}

$navtitle = "我的附件 - ";

$filetypes = array('alltype','onlyimage','notimage');
$actions = array('list','delete');

$filetype = in_array($filetype,$filetypes) ? $filetype : 'alltype';
$action = in_array($action,$actions) ? $action : 'list';



if ($action=='list'){
    $extrawhere='';
	$multipage='当前没有分页信息。';
	switch ($filetype){
	    case 'onlyimage':
		    $extrawhere .= ' AND attach.isimage=1 ';
			break;
	    case 'notimage':
		    $extrawhere .= ' AND attach.isimage=0 ';
			break;			
	    case 'alltype':
			break;		
	    case 'default':
			break;		
	}
	$attachmentscount = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}attachments attach WHERE attach.uid=$discuz_uid $extrawhere"),0);
	if ($attachmentscount){
	
        $page = max(1, intval($page));
        $page = $maxpages && $page > $maxpages ? 1 : $page;
        $startlimit = ($page - 1) * $tpp;
	    $query = $db->query("SELECT attach.*, t.subject FROM {$tablepre}attachments attach
                              LEFT JOIN {$tablepre}threads t ON attach.tid=t.tid
                              WHERE attach.uid=$discuz_uid $extrawhere
                              ORDER BY attach.dateline DESC LIMIT $startlimit, $tpp");
		$attachmentslist = array();
		while($attachments = $db->fetch_array($query)) {
            $attachments['description'] = dhtmlspecialchars($attachments['description']);
            $attachments['dateline'] = gmdate('y-n-j H:i', $attachments['dateline'] + $timeoffset * 3600);
            $attachments['filesize'] = sizecount($attachments['filesize']);
			$extension = strtolower(fileext($attachments['filename']));
		    $attachments['filetype'] = attachtype($extension."\t".$attachments['filetype']);			
            $attachmentslist[] = $attachments;
		}
		$multipage = multi($attachmentscount, $tpp, $page, "myattachments.php?action=$action&amp;filetype=$filetype", $maxpages);
	}
	//var_export($attachmentslist);
	include template('my_attachments');

}elseif($action=='delete'){
    exit('暂未开发该功能！');
}else{
    exit('Access Denied!');
}




?>
