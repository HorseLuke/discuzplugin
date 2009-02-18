<?php

/*
By Horse Luke(20080813)
*/

define('NOROBOT', TRUE);
include './include/common.inc.php';

if(!$discuz_uid) {
	showmessage('not_loggedin', NULL, 'NOPERM');
}


$notfound = 0;              //若评分条数为0，则设置为1，显示无结果
$action = isset($action) ? trim($action) : 'myrate';


if(empty($action) || $action == 'myrate') {              //计算评分条数
	$actiontitle="我的评分";
    $ratelogcount = $db->result_first("SELECT COUNT(*) FROM {$tablepre}ratelog WHERE {$tablepre}ratelog.uid=$discuz_uid");
} elseif($action == 'berated') {
	$actiontitle="我的被评";
    $ratelogcount = $db->result_first("SELECT COUNT(*) FROM {$tablepre}ratelog rl LEFT JOIN {$tablepre}posts p ON rl.pid=p.pid WHERE p.authorid=$discuz_uid");
} else {
	showmessage('undefined_action', NULL, 'HALTED');
}

$page = max(1, intval($page));
$page = $maxpages && $page > $maxpages ? 1 : $page;
$startlimit = ($page - 1) * $tpp;				  

if(!$ratelogcount) {
    $notfound = 1;
	//showmessage("{$actiontitle}无记录，请返回。",'javascript:history.back()');
}else {
    if($action == 'myrate') {
    	$query = $db->query("SELECT rl.*, p.message, p.author, p.authorid, p.status FROM {$tablepre}ratelog rl
                              LEFT JOIN {$tablepre}posts p ON rl.pid=p.pid
                              WHERE rl.uid=$discuz_uid
                              ORDER BY rl.dateline DESC LIMIT $startlimit, $tpp");
    } elseif($action == 'berated') {
	    $query = $db->query("SELECT rl.*, p.message, p.author, p.authorid, p.status FROM {$tablepre}ratelog rl
                              LEFT JOIN {$tablepre}posts p ON rl.pid=p.pid
                              WHERE p.authorid=$discuz_uid
                              ORDER BY rl.dateline DESC LIMIT $startlimit, $tpp");
    }
	$rateloglist = array();
	while($ratelog = $db->fetch_array($query)) {
			if (empty($ratelog['author'])){                 //帖子被删除时，但仍有评分记录时候的动作
			    $ratelog['message']='***内容已经删除***';
			    $ratelog['author']='***未知***';
			    $ratelog['authorid']='0';
			} elseif ($ratelog['status']=='1'){                //帖子被屏蔽时候的操作
			    $ratelog['message']='***被屏蔽内容***';
			} else{              //帖子正常状态下的操作
			    $ratelog['message'] = empty($ratelog['message']) ? '***无内容***' : cutstr(dhtmlspecialchars($ratelog['message']), 60);
			}
            $ratelog['reason'] = dhtmlspecialchars($ratelog['reason']);
			$ratelog['score'] = $ratelog['score'] > 0 ? '+'.$ratelog['score'] : $ratelog['score'];
            $ratelog['dateline'] = gmdate('y-n-j H:i', $ratelog['dateline'] + $timeoffset * 3600);
            $rateloglist[] = $ratelog;
    }
	$multipage = multi($ratelogcount, $tpp, $page, "myrateview.php?action=$action", $maxpages);
	//showmessage("{$actiontitle}记录运行成功，请继续");
}

include template('myrateview');

?>