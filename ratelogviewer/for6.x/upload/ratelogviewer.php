<?php

/*
RatelogVIEWER Main PHP File 1 For Discuz 6.0.0 Build 20080918
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
include './include/common.inc.php';

/*
以下为设置区，仅需设置一个权限参数。
该参数为允许查看别人评分记录的用户类型代码组合。
0 代表普通用户；1 代表论坛管理员；2 代表超级版主；3代表论坛版主。
请将您需要的其组合并填入array数组中，组合和组合之间需要用半角的“,”（不含双引号隔开）。
组合举例：填“array(1)”，只允许管理员查看别人的评分记录；填“array(1,2)”，允许管理员和超版查看......如此类推。
*/
$allowviewotheruserlist = array(1,2);

/*以下为函数代码，请勿修改！*/

function filtersql($filter = 'all') {              //确定正负分筛选sql语句
    if ($filter == 'plus'){
     return 'AND rl.score > 0';
    }elseif ($filter == 'decrease'){
     return 'AND rl.score < 0';
    }else{
     return '';
    }
}

function datelinesql($datefrom='0000-00-00',$dateto='0000-00-00'){               //确定时间筛选sql语句
    if ( (!is_numeric(str_replace('-','',$datefrom)) && !empty($datefrom))     ||     (!is_numeric(str_replace('-','',$dateto)) && !empty($dateto))  ){    //输入数据有效性和安全性验证
        showmessage('非法调用参数，请返回。');
    }
    $datefrom=strtotime("$datefrom");
    $dateto=strtotime("$dateto");
    if($datefrom<$dateto && (!empty($datefrom) && !empty($dateto))){
        return "AND rl.dateline > {$datefrom} AND rl.dateline < {$dateto}";
    }elseif(($datefrom>=$dateto && $datefrom!=0 && $dateto!=0) || (!empty($datefrom) && empty($dateto))){
        return "AND rl.dateline > {$datefrom}";
    }elseif(empty($datefrom) && !empty($dateto)){
        return "AND rl.dateline < {$dateto}";
    }else{
        return "";
    }
}

/*以下为运行代码，请勿修改！*/

if(!$discuz_uid) {
	showmessage('not_loggedin', NULL, 'NOPERM');
}

$allowviewotheruser=in_array($adminid,$allowviewotheruserlist) ? 1 : 0;
unset ($allowviewotheruserlist);
$action = (isset($action) && in_array($action,array('myrate','berated','search'))) ? trim($action) : 'myrate';


if ($action == 'myrate' || $action == 'berated'){

	$filter = (isset($filter) && in_array($filter,array('plus','decrease','all'))) ? $filter : 'all';
	$uid = (isset($uid) && is_numeric($uid) && $allowviewotheruser) ? intval($uid) : $discuz_uid;
	$datefrom = isset($datefrom) ? $datefrom : '0000-00-00';
	$dateto = isset($dateto) ? $dateto : '0000-00-00';
	$filtersql=filtersql($filter);
	$datelinesql=datelinesql($datefrom,$dateto);
	
	if(!empty($extcreditssubmit)) {                             //对积分筛选进行有效性验证和组合(此段代码虽能运行但设计上非常有问题,留作记号)
    	foreach((is_array($extcreditssubmit) ? $extcreditssubmit : explode('_', $extcreditssubmit)) as $credit) {
	  	  if($credit = intval(trim($credit))) {
	    	$extcreditsarray[] = $credit;
	  	  }
        }
     }
	$ids = '0';
	$extcreditslist = $extcreditscheck = array();
	foreach($extcredits as $id => $credit) {
   	   $extcreditslist[] = array('id' => $id, 'title' => $credit['title']);
	   if(!$extcreditsarray || in_array($id, $extcreditsarray)) {
   	      $ids .= ','.$id;
	      $extcreditscheck[$id] = 'checked="checked"';
       }
	}
	
	if ($uid == $discuz_uid){
        $username = '我';
	}else{
	    $user=array();
	    $user = $db->fetch_array($db->query("SELECT adminid,username FROM {$tablepre}members WHERE {$tablepre}members.uid=$uid"));
        if (empty($user)){
	        showmessage('该用户不存在，请返回。','ratelogviewer.php');
	    }elseif($user['adminid']>0 && ($adminid>$user['adminid'] || $adminid<1)){
	        showmessage('您不允许查看比你高级别的管理人员的评分记录，请返回。','javascript:history.back()');
		}else{
            $username = $user['username'];
			unset ($user);

		}	
	}
	
	if($action == 'myrate'){
		$navtitle= $username.'的评分记录';
	    $ratelogcount = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}ratelog rl WHERE rl.uid=$uid AND rl.extcredits IN ($ids) $filtersql $datelinesql"),0);
	    $ratelogcount =  empty($ratelogcount) ? 0 : $ratelogcount;
    }else{
		$navtitle= $username.'的被评记录';
    	$ratelogcount = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}ratelog rl LEFT JOIN {$tablepre}posts p ON rl.pid=p.pid WHERE p.authorid=$uid AND rl.extcredits IN ($ids) $filtersql $datelinesql"),0);
	    $ratelogcount =  empty($ratelogcount) ? 0 : $ratelogcount;
	}
	
    if ($ratelogcount>0){
	    $page = max(1, intval($page));
        $page = $maxpages && $page > $maxpages ? 1 : $page;
        $startlimit = ($page - 1) * $tpp;
		if ($action == 'myrate'){
    	    $query = $db->query("SELECT rl.*, p.fid, p.tid, p.subject, p.message, p.author, p.authorid, p.status FROM {$tablepre}ratelog rl
                                 LEFT JOIN {$tablepre}posts p ON rl.pid=p.pid
                                 WHERE rl.uid=$uid AND rl.extcredits IN ($ids) $filtersql $datelinesql
                                 ORDER BY rl.dateline DESC LIMIT $startlimit, $tpp");
		}elseif($action == 'berated'){			 
		    $query = $db->query("SELECT rl.*, p.fid, p.tid, p.subject, p.message, p.author, p.authorid, p.status FROM {$tablepre}ratelog rl
                                 LEFT JOIN {$tablepre}posts p ON rl.pid=p.pid
                                 WHERE p.authorid=$uid AND rl.extcredits IN ($ids) $filtersql $datelinesql
                                 ORDER BY rl.dateline DESC LIMIT $startlimit, $tpp");
		}
	    $rateloglist = array();
	    while($ratelog = $db->fetch_array($query)) {
            if ($ratelog['status']=='1'){                //帖子被屏蔽时候的操作（帖子被删除但仍有评分记录时候的操作转移到模版完成）
			    $ratelog['message']='<i>被屏蔽内容</i>';
			} else{              //帖子正常状态下的操作
			    $ratelog['message'] = empty($ratelog['message']) ? '<i>无内容</i>' : cutstr(dhtmlspecialchars($ratelog['message']), 84);
			}
			$ratelog['subject']= (empty($ratelog['subject']) && !empty($ratelog['authorid']))? '<font color="#999999">属于回帖或为空标题</font>' : dhtmlspecialchars($ratelog['subject']);
            $ratelog['reason'] = dhtmlspecialchars($ratelog['reason']);
			$ratelog['score'] = $ratelog['score'] > 0 ? '+'.$ratelog['score'] : $ratelog['score'];
            $ratelog['dateline'] = gmdate('y-n-j H:i', $ratelog['dateline'] + $timeoffset * 3600);
            $rateloglist[] = $ratelog;
        }
	    $multipage = multi($ratelogcount, $tpp, $page, "ratelogviewer.php?action=$action&uid=$uid&filter=$filter&datefrom=$datefrom&dateto=$dateto&extcreditssubmit=".str_replace(',', '_', $ids), $maxpages);
	}
    @include './forumdata/cache/cache_forums.php';
    include template('ratelogviewer');
}

elseif ($action == 'search'){
    $navtitle='用户评分记录搜索 - ';
    if (!($allowviewotheruser)){
		showmessage('管理员禁止你所在的用户组查看别人评分记录，请返回。', NULL, 'NOPERM');
	}
	if ($searchkey == 'username'){
		$keyword = trim($keyword);
	    $guestexp = '\xA1\xA1|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
	    $censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($censoruser = trim($censoruser)), '/')).')$/i';
	    if(preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is", $keyword) || ($censoruser && @preg_match($censorexp, $keyword)) || empty($keyword)) {
	         showmessage('非法调用参数，请返回。');
     	}
		$user=array();
	    $user = $db->fetch_array($db->query("SELECT adminid,uid FROM {$tablepre}members WHERE username='$keyword'"));
		if (empty($user)){
	         showmessage('该用户不存在，请返回。');	
	    }elseif($user['adminid']>0 && ($adminid>$user['adminid'] || $adminid<1)){
	        showmessage('您不允许查看比你高级别的管理人员的评分记录，请返回。');
		}else{	
		    showmessage('正在转向中，请稍后。','ratelogviewer.php?action='.$actiontype.'&uid='.$user['uid']);
		}
	}elseif ($searchkey == 'uid' && is_numeric($keyword)){
	    $keyword = intval($keyword);
		showmessage('正在转向中，请稍后。','ratelogviewer.php?action='.$actiontype.'&uid='.$keyword);
	}else{
	    showmessage('输入错误，导致非法调用被终止。请返回。');	
	}
}



?>