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
define('NOROBOT', TRUE);
$thisfilename='creditbugfix.php';
$limit_once_process=1000;            //一次处理的条数
require_once './include/common.inc.php';


if($adminid != 1) {
	showmessage('请以管理员身份登录。', NULL, 'HALTED');
}

if (empty($_DCOOKIE['STEP_NEXT'])){
	$step=1;
    dsetcookie('STEP_NEXT', 2, 172800 , 1, true);
}else{
    $_DCOOKIE['STEP_NEXT']=intval($_DCOOKIE['STEP_NEXT']);
    $step = (isset($step) && $_DCOOKIE['STEP_NEXT']  >= $step) ? intval($step) : 1;
    $step_next = $step + 1;
	dsetcookie('STEP_NEXT', $step_next, 172800 , 1, true);
}

	
switch ($step){
    case 1:
	         exit("该数据库修复程序主要显示通过disucz 7的刷分漏洞而导致所有发表在fid为0的无法显示的主题帖。<br />
			 漏洞详情及修复方法请<a href=\"http://www.alan888.com/Discuz/thread-162824-1-1.html\">关注这里(alan888帖子)</a>，或者<a href=\"http://www.discuz.net/thread-1211501-1-1.html\">这里（Discuz!帖子）。</a><br />
			 本修复程序版本：0.0.1 BUILD 20090213 FIX 1。作者：Horse Luke（竹节虚）。<br /><br />
			 <b>请注意：该程序为非官方程序，请先按照上面帖子的修复方法修复Discuz!，然后后台备份好数据库，然后再运行！作者不承担一切责任！</b><br /><br />
			 若确定请<a href=\"{$thisfilename}?step=2\">按此开始检测是否符合要求修复。</a>");
			 break;

    case 2:
	         $in_threads_table_num = $db->result_first("SELECT count(*) FROM {$tablepre}threads WHERE fid = 0");
			 $in_posts_table_num =  $db->result_first("SELECT count(*) FROM {$tablepre}posts WHERE fid = 0");
			 if ($in_posts_table_num == 0){
                 exit("没有找到任何fid为0的隐藏帖子，无需修复。");
			 }elseif($in_threads_table_num == $in_posts_table_num){
			     dsetcookie('allfixnum', $in_threads_table_num, 172800 , 1, true);
				 dsetcookie('nofixnum', $in_threads_table_num, 172800 , 1, true);
			     exit("你的论坛可以使用该修复程序修复<br />确定请<a href=\"{$thisfilename}?step=3\">按此继续。</a>");
			 }else{
			     exit("检查不通过，无法使用该修复程序修复。");			 
			 }
	         //exit("确定请<a href=\"{$thisfilename}?step=3\">按此继续。</a>");
			 break;

	case 3:
	        $db->query("INSERT INTO {$tablepre}forums (type, name, status, displayorder,alloweditpost)
					    VALUES ('group', '利用刷分漏洞刷分处理区', '1', '99', '0')");
			$newfid_fup = $db->insert_id();
			$db->query("INSERT INTO {$tablepre}forumfields (fid)
					    VALUES ('$newfid_fup')");
			$db->query("INSERT INTO {$tablepre}forums (fup , type, name, status, displayorder, alloweditpost)
					    VALUES ('$newfid_fup','forum', '利用刷分漏洞刷分处理区', '1', '99', '0')");			
			$newfid = $db->insert_id();
			$db->query("INSERT INTO {$tablepre}forumfields (fid)
					    VALUES ('$newfid')");
			dsetcookie('newfid', $newfid, 172800 , 1, true);		
			exit("成功建立板块用于处理刷分漏洞区<br />确定请<a href=\"{$thisfilename}?step=4\">按此继续。</a>");			

	case 4:
	        if (empty($_DCOOKIE['allfixnum']) || empty($_DCOOKIE['newfid'])){
			    exit('修复程序出现问题，请确认浏览器支持并且打开了Cookies！<a href=\"{$thisfilename}?step=1\">点此重新运行程序。</a>');
			}
			if ($_DCOOKIE['nofixnum'] > 0){
			    $newfid =intval($_DCOOKIE['newfid']);
			    $db->query("UPDATE {$tablepre}threads SET fid='$newfid'
				            WHERE fid = 0 LIMIT $limit_once_process");
			    $db->query("UPDATE {$tablepre}posts SET fid='$newfid'
				            WHERE fid = 0 LIMIT $limit_once_process");
				$nofixnum=$_DCOOKIE['nofixnum']-$limit_once_process;
				dsetcookie('nofixnum', $nofixnum, 172800 , 1, true);
				exit("<meta http-equiv=\"refresh\" content=\"3\" />
				      剩余{$_DCOOKIE['nofixnum']}条数据没有处理，每次处理{$limit_once_process}条；正在处理并自动跳转中，请稍候。
					  <br /><a href=\"{$thisfilename}?step=4\">无法跳转请点击这里......</a>");		
			}else{
			    header("Location:{$thisfilename}?step=5");
			}
	case 5:
	         $newfid = intval($_DCOOKIE['newfid']);
			 $newpost =  intval($_DCOOKIE['allfixnum']);
	         $db->query("UPDATE {$tablepre}forums SET threads='$newpost', posts='$newpost', todayposts='$newpost' WHERE fid='$newfid'", 'UNBUFFERED');
	         exit("修复完成，请立刻删除本文件！！！！！！！！。<a href=\"forumdisplay.php?fid={$_DCOOKIE['newfid']}\">并到该板块进行后续处理</a>");			
	default :
	         exit('参数错误！');

}


?>