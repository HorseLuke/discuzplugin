<?php

/*
 * 显示前25张版主推荐主题帖的单独页面For DZ 6.0
 * Author: Horse Luke（中文名称：大菜鸟）
 * 业余时间分离并改写一未知出处的论坛首页Home中关于版主推荐板块代码。
 * 代码已经过作者的测试，但仍存在风险；如对您造成损失，本人不承担任何责任。请在购买和安装前慎重考虑。
 * 已知问题：只能显示前25张主题帖；占用服务器资源较大
 */

require_once './include/common.inc.php';

$page = max(1, intval($page));
$page = $threadmaxpages && $page > $threadmaxpages ? 1 : $page;
$startlimit = ($page - 1) * $tpp;

//计算论坛版主推荐的帖子数目
$query = $db->query("SELECT COUNT(*) FROM {$tablepre}forumrecommend");
$threadcount = $db->result($query, 0);

if(!$threadcount) {
	showmessage('没有任何版主推荐帖子，请返回。');
}

if(empty($order) || !in_array($order, array('t.dateline', 't.lastpost', 'fr.subject', 'fr.fid', 'fr.displayorder'))) {
	$order = 't.dateline';
}
$ordercheck = array($order => 'selected="selected"');

//debug 根据论坛取得的版主推荐的帖子
$hack_cut_str = 80; //设置标题显示的字数
$recommendlist = array();
$query = $db->query("SELECT fr.*, f.name, t.dateline, t.lastpost, t.lastposter FROM {$tablepre}forumrecommend fr
                     LEFT JOIN {$tablepre}forums f ON f.fid = fr.fid
                     LEFT JOIN {$tablepre}threads t ON t.tid = fr.tid
                     ORDER BY $order DESC LIMIT $startlimit, $tpp");

while($recommend = $db->fetch_array($query)) {
	if(($recommend['expiration'] && $recommend['expiration'] > $timestamp) || !$recommend['expiration']) {
                $recommend['subject'] = cutstr($recommend['subject'],$hack_cut_str);
                $recommend['dateline'] = gmdate("$dateformat $timeformat", $recommend['dateline'] + $timeoffset * 3600);
                $recommend['lastpost'] = gmdate("$dateformat $timeformat", $recommend['lastpost'] + $timeoffset * 3600);
	}
		$recommendlist[] = $recommend;
}

$multipage = multi($threadcount, $tpp, $page, "forumrecommendlist.php?order=$order", $threadmaxpages);

$navigation="版主们推荐了什么好帖子？";      //设置该版名称
include template('forumrecommendlist');
?>