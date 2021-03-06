<?php

/*
  My Attachments Viewer For Discuz! 7.1, Version 0.0.5 Build 20091108 Rev 78, Process-oriented approach

  Copyright 2009 Horse Luke（竹节虚）.

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

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if(!$discuz_uid) {
    showmessage('not_loggedin', NULL, 'NOPERM');
}
require_once DISCUZ_ROOT.'./include/attachment.func.php';

$filetypes = array('alltype','onlyimage','notimage');

$filetype = ($filetype && in_array($filetype,$filetypes)) ? $filetype : 'alltype';

$extrawhere='';
switch ($filetype){
    case 'onlyimage':
        $extrawhere .= " AND attach.isimage IN ('1','-1') ";
        break;
    case 'notimage':
        $extrawhere .= " AND attach.isimage = '0' ";
        break;
    case 'alltype':
        break;
    case 'default':
        break;
}

$multipage='';
$attachmentscount = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}attachments attach WHERE attach.uid=$discuz_uid $extrawhere"),0);

if ($attachmentscount){  
    $page = max(1, intval($page));
    $page = ( (int)($attachmentscount/$tpp + 1) < $page ) ? 1 : $page;
    $startlimit = ($page - 1) * $tpp;
    
    $query = $db->query("SELECT attach.*, attachfield.description, t.subject
                              FROM {$tablepre}attachments attach 
                              LEFT JOIN {$tablepre}attachmentfields attachfield ON attach.aid=attachfield.aid
                              LEFT JOIN {$tablepre}threads t ON attach.tid=t.tid
                              WHERE attach.uid = '$discuz_uid' $extrawhere
                              ORDER BY attach.dateline DESC LIMIT $startlimit, $tpp");
    $attachmentslist = array();
    while($attachments = $db->fetch_array($query)) {
        $attachments['aidDownload']=aidencode($attachments['aid']);
        $attachments['description'] = dhtmlspecialchars($attachments['description']);
        $attachments['dateline'] = gmdate('y-n-j H:i', $attachments['dateline'] + $timeoffset * 3600);
        $attachments['filesize'] = sizecount($attachments['filesize']);
        $extension = strtolower(fileext($attachments['filename']));
        $attachments['filetype'] = attachtype($extension."\t".$attachments['filetype']);
        $attachmentslist[] = $attachments;
    }
    $multipage = multi($attachmentscount, $tpp, $page, "plugin.php?id=iirs_myattachments:myattachments&amp;filetype=$filetype", $maxpages);
}

include plugintemplate('my_attachments');