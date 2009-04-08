<?php

/*
PIC DISPLAY Ver 0.0.3 Build 20090408 Rev 48 For Discuz! 7

  Copyright 2009 Horse Luke������飩.

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

require_once './include/common.inc.php';

$show_num_inpage = 5;    //ÿҳ��ͼƬ����
$cache_refresh_time = 3;         //����ˢ�µ�ʱ�䣬ʱ��ΪСʱ
$attach_dir = "attachments";         //����Ŀ¼
$fid_ignore_list='';          //��������ʾ��������ظ����İ��fid���������ֺͰ�Ƿ��ŷָ���磨������˫���ţ���12������12,55����

$page = empty($page) ? 1 : intval($page);
$page = max(1,$page);
$no_nextpage = 0;
$piclist = array();
$prepage = ($page == 1) ? 1 : ($page - 1) ;
$nextpage = $page + 1;
$cachetime = @filemtime("./forumdata/cache/cache_picdisplay.php");
@include_once ("./forumdata/cache/cache_picdisplay.php");
$image_tid_totalnum = empty($image_tid_totalnum) ? 0 : intval($image_tid_totalnum);

$sqlfilter =  '';
if ($fid_ignore_list){
	$sqlfilter = " AND t.fid NOT IN ({$fid_ignore_list}) ";
}


if ($page * $show_num_inpage < 51){
    if (!$cache_refresh_time || !file_exists("./forumdata/cache/cache_picdisplay.php") || ($timestamp - $cachetime > $cache_refresh_time * 1)) {
		if ($fid_ignore_list){
            $image_tid_totalnum = $db->result_first("SELECT count(DISTINCT t.tid) FROM cdb_attachments a LEFT JOIN cdb_threads t ON t.tid=a.tid WHERE isimage = 1 {$sqlfilter}");
			
		}else{
            $image_tid_totalnum = $db->result_first("SELECT count(DISTINCT tid) FROM {$tablepre}attachments WHERE isimage = 1");
		}
		$piclist = array();
		if($image_tid_totalnum > 0){
            $query = $db->query("SELECT a.aid, a.tid , a.readperm, a.price, a.attachment,
			                 a.thumb, a.isimage, a.remote, t.subject, t.fid
                             FROM {$tablepre}attachments a
                             LEFT JOIN {$tablepre}threads t ON t.tid = a.tid
                             WHERE a.isimage = 1
							 {$sqlfilter}
                             GROUP BY a.tid
                             ORDER BY a.dateline DESC 
                             LIMIT 0 , 50");
		    $i = 0;
		    while($pic = $db->fetch_array($query)) {
				if($pic['readperm'] > 0 || $pic['price'] > 0 || $pic['remote'] > 0){
					$pic['attachment'] = "./images/tasks/gift.gif";
				}elseif($pic['thumb'] > 0){
					$pic['attachment'] = "./{$attach_dir}/{$pic[attachment]}.thumb.jpg";
				}else{
					$pic['attachment'] = "./{$attach_dir}/{$pic[attachment]}";
				}
				$pic['subject'] = dhtmlspecialchars($pic['subject']);
		    	$j=(int)($i / $show_num_inpage);
		        $piclist[$j][] = $pic;
				$i++;
	        }
		}
		if($cache_refresh_time > 0){
		    $cachedata = "\$image_tid_totalnum=".$image_tid_totalnum.";\n\n\$piclist=".var_export($piclist,true).';';
		    require_once './include/cache.func.php';
		    writetocache('picdisplay', '', $cachedata);
    	    //echo 'cache created!';
	    }
	}
	//echo 'use cache';
	$i = $page - 1;
	$piclist = (!empty($piclist[$i]) && is_array($piclist[$i])) ? $piclist[$i] :array();
	
}else{
		$piclist = array();
	    $startlimit = ($page - 1) * $show_num_inpage;
        $query = $db->query("SELECT a.aid, a.tid, a.readperm, a.price, 
	    					 a.attachment, a.thumb, a.isimage, a.remote, t.subject, t.fid
                             FROM {$tablepre}attachments a
                             LEFT JOIN {$tablepre}threads t ON t.tid=a.tid
							 WHERE a.isimage=1
							 {$sqlfilter}	 
                             GROUP BY a.tid
                             ORDER BY a.dateline DESC
		    				 LIMIT $startlimit , $show_num_inpage");	
		while($pic = $db->fetch_array($query)) {
		    if($pic['readperm'] > 0 || $pic['price'] > 0 || $pic['remote'] > 0){
				$pic['attachment'] = "./images/tasks/gift.gif";
			}elseif($pic['thumb'] > 0){
				$pic['attachment'] = "./{$attach_dir}/{$pic[attachment]}.thumb.jpg";
			}else{
				$pic['attachment'] = "./{$attach_dir}/{$pic[attachment]}";
			}
			$pic['subject'] = dhtmlspecialchars($pic['subject']);
		    $piclist[] = $pic;
	    }

}

if(($page * $show_num_inpage > $image_tid_totalnum) || empty($piclist) ){
	$no_nextpage = 1;
}

//var_export($piclist);
include template('picdisplay');


?>