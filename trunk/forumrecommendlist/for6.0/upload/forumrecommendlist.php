<?php

/*
 * ��ʾǰ25�Ű����Ƽ��������ĵ���ҳ��For DZ 6.0
 * Author: Horse Luke���������ƣ������
 * ҵ��ʱ����벢��дһδ֪��������̳��ҳHome�й��ڰ����Ƽ������롣
 * �����Ѿ������ߵĲ��ԣ����Դ��ڷ��գ�����������ʧ�����˲��е��κ����Ρ����ڹ���Ͱ�װǰ���ؿ��ǡ�
 * ��֪���⣺ֻ����ʾǰ25����������ռ�÷�������Դ�ϴ�
 */

require_once './include/common.inc.php';

$page = max(1, intval($page));
$page = $threadmaxpages && $page > $threadmaxpages ? 1 : $page;
$startlimit = ($page - 1) * $tpp;

//������̳�����Ƽ���������Ŀ
$query = $db->query("SELECT COUNT(*) FROM {$tablepre}forumrecommend");
$threadcount = $db->result($query, 0);

if(!$threadcount) {
	showmessage('û���κΰ����Ƽ����ӣ��뷵�ء�');
}

if(empty($order) || !in_array($order, array('t.dateline', 't.lastpost', 'fr.subject', 'fr.fid', 'fr.displayorder'))) {
	$order = 't.dateline';
}
$ordercheck = array($order => 'selected="selected"');

//debug ������̳ȡ�õİ����Ƽ�������
$hack_cut_str = 80; //���ñ�����ʾ������
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

$navigation="�������Ƽ���ʲô�����ӣ�";      //���øð�����
include template('forumrecommendlist');
?>