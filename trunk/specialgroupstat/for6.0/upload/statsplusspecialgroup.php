<?php

/*
Statistic Plus! Module 1 For Discuz 6.0.0 Build 20080912 Main PHP File 1 
Copyright (C) 2008 Horse Luke

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

define('NOROBOT', TRUE);
require_once './include/common.inc.php';

$whoallowview=3;    //谁允许查看这些记录。0表示即使是游客也能查看；1表示需要用户登录才能查看；2表示跟随用户组中允许查看统计数据的设置而设定；3表示仅允许论坛管理员查看；4表示该模块关闭，任何人都不能查看。


switch($whoallowview) {
	case '0':
		break;
	case '1':
        if(!$discuz_uid) {
			showmessage('not_loggedin', NULL, 'NOPERM');
		}
		break;
	case '2':
        if(!$allowviewstats) {
        	showmessage('group_nopermission', NULL, 'NOPERM');
        }
		break;
	case '3':
        if($adminid != 1) {
        	showmessage('group_nopermission', NULL, 'NOPERM');
        }
		break;
	case '4':
        showmessage('本模块已经关闭，正在返回首页中......','index.php');
		break;
}


$moduleversion='Module 1(Speical Group Posts Statistic)<br />Ver 0.1.1 Build 080912';
$moduleversioncheck='http://www.freediscuz.net/bbs/thread-4218-1-1.html';
$statscachelife = $statscachelife * 60;
$navtitle='论坛统计Plus! - ';

$action= (isset($action) && in_array($action,array('index','stats','managecache'))) ? trim($action) : 'index';
$searchgroupid = (isset($searchgroupid) && is_numeric($searchgroupid)) ? intval($searchgroupid) : 0;

if ($action=='index'){    //读取特殊用户组列表，由于采取AJAX方式显示统计结果，因此仅需读取一次,降低服务器负担，并且拥有更好的用户体验和安全性。
	  $grouplist = array();
	  $query = $db->query("SELECT groupid, grouptitle FROM {$tablepre}usergroups WHERE type='special'");
	  while($group = $db->fetch_array($query)) {
		    $grouplist['special'] .= '<li><a href="statsplusspecialgroup.php?action=stats&amp;searchgroupid='.$group['groupid'].'" onclick="ajaxget(this.href, \'specialgroupresult\');doane(event);">'.$group['grouptitle'].'</a></li>';
	  }
	  include template('statsplus_specialgroupframe');


}elseif ($action=='stats'){      //以下代码来源于Disucz! 6.0中stats.php关于{$type == 'team'}时候的操作
	  $query = $db->query("SELECT u.groupid,u.radminid,u.grouptitle,u.stars FROM {$tablepre}usergroups u WHERE u.groupid='$searchgroupid' LIMIT 1");
	  if(!$group = $db->fetch_array($query)) {
		  showmessage('usergroups_nonexistence', NULL, 'HALTED');
	  }
 
	  $searchkey='groupid'.$searchgroupid;
	  $statvars = array();
	  $query = $db->query("SELECT * FROM {$tablepre}statvars WHERE type='$searchkey'");
	  while($variable = $db->fetch_array($query)) {
		   $statvars[$variable['variable']] = $variable['value'];
	  }
	  
	  if($timestamp - $statvars['lastupdate'] > $statscachelife) {
		   $statvars = array('lastupdate' => $timestamp);
		   $newstatvars[] = "'$searchkey', 'lastupdate', '$timestamp'";
	  }

	  $groupresult = array();


	  if(isset($statvars["{$searchkey}"])) {
		   $groupresult = unserialize($statvars["{$searchkey}"]);
      } else {
	       $members = array();
		   $uids=0;
		   
		   if($oltimespan) {
			    $oltimeadd1 = ', o.thismonth AS thismonthol, o.total AS totalol';
			    $oltimeadd2 = "LEFT JOIN {$tablepre}onlinetime o ON o.uid=m.uid";
		   } else {
			    $oltimeadd1 = $oltimeadd2 = '';
		   }
		   
		   $totalposts = $totaloffdays = $totalol = $totalthismonthol = 0;
		   $query = $db->query("SELECT m.uid, m.username, m.adminid, m.lastactivity, m.credits, m.posts $oltimeadd1
			                    FROM {$tablepre}members m $oltimeadd2
			                    WHERE m.groupid={$searchgroupid}");
		   while($member = $db->fetch_array($query)) {
		       $member['offdays'] = intval(($timestamp - $member['lastactivity']) / 86400);
		       $totaloffdays += $member['offdays'];
			   $totalposts += $member['posts'];
		       if($oltimespan) {
				    $member['totalol'] = round($member['totalol'] / 60, 2);
				    $member['thismonthol'] = gmdate('Yn', $member['lastactivity']) == gmdate('Yn', $timestamp) ? round($member['thismonthol'] / 60, 2) : 0;
				    $totalol += $member['totalol'];
				    $totalthismonthol += $member['thismonthol']; 
			   }
			   $members[$member['uid']] = $member;
			   $uids .= ','.$member['uid'];
		    }

			
			$totalthismonthposts = 0;
			$query = $db->query("SELECT authorid, COUNT(*) AS posts FROM {$tablepre}posts
			                     WHERE dateline>=$timestamp-86400*30 AND authorid IN ($uids) AND invisible='0' GROUP BY authorid");
		    while($post = $db->fetch_array($query)) {
			    $members[$post['authorid']]['thismonthposts'] = $post['posts'];
			    $totalthismonthposts += $post['posts'];
		    }
			
			$memberscount=empty($members)? 0 :count($members);
			$groupresult = array(
				'members' => $members,
				'avgoffdays' => @intval($totaloffdays / $memberscount),
				'avgposts' =>@intval($totalposts / $memberscount),
				'avgthismonthposts' => @intval($totalthismonthposts / $memberscount),
				'avgtotalol' => @round(($totalol / $memberscount),2),
				'avgthismonthol' => @round(($totalthismonthol / $memberscount),2),
				'memberscount' => @$memberscount,
			);
			$newstatvars[] = "'{$searchkey}', '{$searchkey}', '".addslashes(serialize($groupresult))."'";		
	  }
	  
	  if(is_array($groupresult)) {
		    foreach($groupresult['members'] as $uid => $member) {
			   @$member['thismonthposts'] = intval($member['thismonthposts']);
		       @$groupresult['members'][$uid]['offdays'] = $member['offdays'] > $groupresult['avgoffdays'] ? '<b><i>'.$member['offdays'].'</i></b>' : $member['offdays'];
			   @$groupresult['members'][$uid]['thismonthposts'] = $member['thismonthposts'] < $groupresult['avgthismonthposts'] / 2 ? '<b><i>'.$member['thismonthposts'].'</i></b>' : $member['thismonthposts'];
			   @$groupresult['members'][$uid]['lastactivity'] = gmdate("$dateformat $timeformat", $member['lastactivity'] + $timeoffset * 3600);
			   @$groupresult['members'][$uid]['thismonthol'] = $member['thismonthol'] < $groupresult['avgthismonthol'] / 2 ? '<b><i>'.$member['thismonthol'].'</i></b>' : $member['thismonthol'];
			   @$groupresult['members'][$uid]['totalol'] = $member['totalol'] < $groupresult['avgtotalol'] / 2 ? '<b><i>'.$member['totalol'].'</i></b>' : $member['totalol'];
		    }
	  }

	  $lastupdate = gmdate("$dateformat $timeformat", $statvars['lastupdate'] + $timeoffset * 3600);
	  $nextupdate = gmdate("$dateformat $timeformat", $statvars['lastupdate'] + $statscachelife + $timeoffset * 3600);
	  updatenewstatvars();

/*
	  echo '<br />$groupresult is <br />';	  
	  var_export($groupresult);
	  echo '<br />$statvars is <br />';
	  var_export ($statvars);
	  $iscached = isset($statvars["{$searchkey}"]) ? 1 :0;
	  echo '<br />'.$iscached;
*/	  

	  include template('statsplus_specialgroupresult');
	  

}elseif ($action=='managecache'){      //缓存管理
      if($adminid != 1) {
        	showmessage('您不是管理员，无权管理缓存。', NULL, 'HALTED');
      }
	  
	  if ($actcase=='delallcache'){
	      $query = $db->query("DELETE FROM {$tablepre}statvars WHERE type LIKE 'groupid%'");
	      showmessage('缓存清空完毕。', 'javascript:history.back()');
	  } 
	  include template('statsplus_specialgroupmanagecache');

}






function updatenewstatvars() {
	global $newstatvars, $db, $tablepre;
	if($newstatvars && $newdata = @implode('),(', $newstatvars)) {
		$db->query("REPLACE INTO {$tablepre}statvars (type, variable, value) VALUES ($newdata)");
	}
	$newstatvars = array();
}


?>