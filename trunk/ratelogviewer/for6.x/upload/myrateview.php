<?php

/*
By Horse Luke(20080813)
*/

define('NOROBOT', TRUE);
include './include/common.inc.php';

if(!$discuz_uid) {
	showmessage('not_loggedin', NULL, 'NOPERM');
}


$notfound = 0;              //����������Ϊ0��������Ϊ1����ʾ�޽��
$action = isset($action) ? trim($action) : 'myrate';


if(empty($action) || $action == 'myrate') {              //������������
	$actiontitle="�ҵ�����";
    $ratelogcount = $db->result_first("SELECT COUNT(*) FROM {$tablepre}ratelog WHERE {$tablepre}ratelog.uid=$discuz_uid");
} elseif($action == 'berated') {
	$actiontitle="�ҵı���";
    $ratelogcount = $db->result_first("SELECT COUNT(*) FROM {$tablepre}ratelog rl LEFT JOIN {$tablepre}posts p ON rl.pid=p.pid WHERE p.authorid=$discuz_uid");
} else {
	showmessage('undefined_action', NULL, 'HALTED');
}

$page = max(1, intval($page));
$page = $maxpages && $page > $maxpages ? 1 : $page;
$startlimit = ($page - 1) * $tpp;				  

if(!$ratelogcount) {
    $notfound = 1;
	//showmessage("{$actiontitle}�޼�¼���뷵�ء�",'javascript:history.back()');
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
			if (empty($ratelog['author'])){                 //���ӱ�ɾ��ʱ�����������ּ�¼ʱ��Ķ���
			    $ratelog['message']='***�����Ѿ�ɾ��***';
			    $ratelog['author']='***δ֪***';
			    $ratelog['authorid']='0';
			} elseif ($ratelog['status']=='1'){                //���ӱ�����ʱ��Ĳ���
			    $ratelog['message']='***����������***';
			} else{              //��������״̬�µĲ���
			    $ratelog['message'] = empty($ratelog['message']) ? '***������***' : cutstr(dhtmlspecialchars($ratelog['message']), 60);
			}
            $ratelog['reason'] = dhtmlspecialchars($ratelog['reason']);
			$ratelog['score'] = $ratelog['score'] > 0 ? '+'.$ratelog['score'] : $ratelog['score'];
            $ratelog['dateline'] = gmdate('y-n-j H:i', $ratelog['dateline'] + $timeoffset * 3600);
            $rateloglist[] = $ratelog;
    }
	$multipage = multi($ratelogcount, $tpp, $page, "myrateview.php?action=$action", $maxpages);
	//showmessage("{$actiontitle}��¼���гɹ��������");
}

include template('myrateview');

?>