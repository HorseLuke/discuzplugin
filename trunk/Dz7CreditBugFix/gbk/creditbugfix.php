<?php

/*
  Copyright 2008 Horse Luke������飩.

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
$limit_once_process=1000;            //һ�δ��������
require_once './include/common.inc.php';


if($adminid != 1) {
	showmessage('���Թ���Ա��ݵ�¼��', NULL, 'HALTED');
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
	         exit("�����ݿ��޸�������Ҫ��ʾͨ��disucz 7��ˢ��©�����������з�����fidΪ0���޷���ʾ����������<br />
			 ©�����鼰�޸�������<a href=\"http://www.alan888.com/Discuz/thread-162824-1-1.html\">��ע����(alan888����)</a>������<a href=\"http://www.discuz.net/thread-1211501-1-1.html\">���Discuz!���ӣ���</a><br />
			 ���޸�����汾��0.0.1 BUILD 20090213 FIX 1�����ߣ�Horse Luke������飩��<br /><br />
			 <b>��ע�⣺�ó���Ϊ�ǹٷ��������Ȱ����������ӵ��޸������޸�Discuz!��Ȼ���̨���ݺ����ݿ⣬Ȼ�������У����߲��е�һ�����Σ�</b><br /><br />
			 ��ȷ����<a href=\"{$thisfilename}?step=2\">���˿�ʼ����Ƿ����Ҫ���޸���</a>");
			 break;

    case 2:
	         $in_threads_table_num = $db->result_first("SELECT count(*) FROM {$tablepre}threads WHERE fid = 0");
			 $in_posts_table_num =  $db->result_first("SELECT count(*) FROM {$tablepre}posts WHERE fid = 0");
			 if ($in_posts_table_num == 0){
                 exit("û���ҵ��κ�fidΪ0���������ӣ������޸���");
			 }elseif($in_threads_table_num == $in_posts_table_num){
			     dsetcookie('allfixnum', $in_threads_table_num, 172800 , 1, true);
				 dsetcookie('nofixnum', $in_threads_table_num, 172800 , 1, true);
			     exit("�����̳����ʹ�ø��޸������޸�<br />ȷ����<a href=\"{$thisfilename}?step=3\">���˼�����</a>");
			 }else{
			     exit("��鲻ͨ�����޷�ʹ�ø��޸������޸���");			 
			 }
	         //exit("ȷ����<a href=\"{$thisfilename}?step=3\">���˼�����</a>");
			 break;

	case 3:
	        $db->query("INSERT INTO {$tablepre}forums (type, name, status, displayorder,alloweditpost)
					    VALUES ('group', '����ˢ��©��ˢ�ִ�����', '1', '99', '0')");
			$newfid_fup = $db->insert_id();
			$db->query("INSERT INTO {$tablepre}forumfields (fid)
					    VALUES ('$newfid_fup')");
			$db->query("INSERT INTO {$tablepre}forums (fup , type, name, status, displayorder, alloweditpost)
					    VALUES ('$newfid_fup','forum', '����ˢ��©��ˢ�ִ�����', '1', '99', '0')");			
			$newfid = $db->insert_id();
			$db->query("INSERT INTO {$tablepre}forumfields (fid)
					    VALUES ('$newfid')");
			dsetcookie('newfid', $newfid, 172800 , 1, true);		
			exit("�ɹ�����������ڴ���ˢ��©����<br />ȷ����<a href=\"{$thisfilename}?step=4\">���˼�����</a>");			

	case 4:
	        if (empty($_DCOOKIE['allfixnum']) || empty($_DCOOKIE['newfid'])){
			    exit('�޸�����������⣬��ȷ�������֧�ֲ��Ҵ���Cookies��<a href=\"{$thisfilename}?step=1\">����������г���</a>');
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
				      ʣ��{$_DCOOKIE['nofixnum']}������û�д���ÿ�δ���{$limit_once_process}�������ڴ����Զ���ת�У����Ժ�
					  <br /><a href=\"{$thisfilename}?step=4\">�޷���ת��������......</a>");		
			}else{
			    header("Location:{$thisfilename}?step=5");
			}
	case 5:
	         $newfid = intval($_DCOOKIE['newfid']);
			 $newpost =  intval($_DCOOKIE['allfixnum']);
	         $db->query("UPDATE {$tablepre}forums SET threads='$newpost', posts='$newpost', todayposts='$newpost' WHERE fid='$newfid'", 'UNBUFFERED');
	         exit("�޸���ɣ�������ɾ�����ļ�������������������<a href=\"forumdisplay.php?fid={$_DCOOKIE['newfid']}\">�����ð����к�������</a>");			
	default :
	         exit('��������');

}


?>