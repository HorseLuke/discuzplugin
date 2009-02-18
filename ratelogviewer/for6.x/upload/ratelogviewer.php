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
����Ϊ����������������һ��Ȩ�޲�����
�ò���Ϊ����鿴�������ּ�¼���û����ʹ�����ϡ�
0 ������ͨ�û���1 ������̳����Ա��2 ������������3������̳������
�뽫����Ҫ������ϲ�����array�����У���Ϻ����֮����Ҫ�ð�ǵġ�,��������˫���Ÿ�������
��Ͼ������array(1)����ֻ�������Ա�鿴���˵����ּ�¼���array(1,2)�����������Ա�ͳ���鿴......������ơ�
*/
$allowviewotheruserlist = array(1,2);

/*����Ϊ�������룬�����޸ģ�*/

function filtersql($filter = 'all') {              //ȷ��������ɸѡsql���
    if ($filter == 'plus'){
     return 'AND rl.score > 0';
    }elseif ($filter == 'decrease'){
     return 'AND rl.score < 0';
    }else{
     return '';
    }
}

function datelinesql($datefrom='0000-00-00',$dateto='0000-00-00'){               //ȷ��ʱ��ɸѡsql���
    if ( (!is_numeric(str_replace('-','',$datefrom)) && !empty($datefrom))     ||     (!is_numeric(str_replace('-','',$dateto)) && !empty($dateto))  ){    //����������Ч�ԺͰ�ȫ����֤
        showmessage('�Ƿ����ò������뷵�ء�');
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

/*����Ϊ���д��룬�����޸ģ�*/

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
	
	if(!empty($extcreditssubmit)) {                             //�Ի���ɸѡ������Ч����֤�����(�˶δ����������е�����Ϸǳ�������,�����Ǻ�)
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
        $username = '��';
	}else{
	    $user=array();
	    $user = $db->fetch_array($db->query("SELECT adminid,username FROM {$tablepre}members WHERE {$tablepre}members.uid=$uid"));
        if (empty($user)){
	        showmessage('���û������ڣ��뷵�ء�','ratelogviewer.php');
	    }elseif($user['adminid']>0 && ($adminid>$user['adminid'] || $adminid<1)){
	        showmessage('��������鿴����߼���Ĺ�����Ա�����ּ�¼���뷵�ء�','javascript:history.back()');
		}else{
            $username = $user['username'];
			unset ($user);

		}	
	}
	
	if($action == 'myrate'){
		$navtitle= $username.'�����ּ�¼';
	    $ratelogcount = $db->result($db->query("SELECT COUNT(*) FROM {$tablepre}ratelog rl WHERE rl.uid=$uid AND rl.extcredits IN ($ids) $filtersql $datelinesql"),0);
	    $ratelogcount =  empty($ratelogcount) ? 0 : $ratelogcount;
    }else{
		$navtitle= $username.'�ı�����¼';
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
            if ($ratelog['status']=='1'){                //���ӱ�����ʱ��Ĳ��������ӱ�ɾ�����������ּ�¼ʱ��Ĳ���ת�Ƶ�ģ����ɣ�
			    $ratelog['message']='<i>����������</i>';
			} else{              //��������״̬�µĲ���
			    $ratelog['message'] = empty($ratelog['message']) ? '<i>������</i>' : cutstr(dhtmlspecialchars($ratelog['message']), 84);
			}
			$ratelog['subject']= (empty($ratelog['subject']) && !empty($ratelog['authorid']))? '<font color="#999999">���ڻ�����Ϊ�ձ���</font>' : dhtmlspecialchars($ratelog['subject']);
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
    $navtitle='�û����ּ�¼���� - ';
    if (!($allowviewotheruser)){
		showmessage('����Ա��ֹ�����ڵ��û���鿴�������ּ�¼���뷵�ء�', NULL, 'NOPERM');
	}
	if ($searchkey == 'username'){
		$keyword = trim($keyword);
	    $guestexp = '\xA1\xA1|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
	    $censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($censoruser = trim($censoruser)), '/')).')$/i';
	    if(preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is", $keyword) || ($censoruser && @preg_match($censorexp, $keyword)) || empty($keyword)) {
	         showmessage('�Ƿ����ò������뷵�ء�');
     	}
		$user=array();
	    $user = $db->fetch_array($db->query("SELECT adminid,uid FROM {$tablepre}members WHERE username='$keyword'"));
		if (empty($user)){
	         showmessage('���û������ڣ��뷵�ء�');	
	    }elseif($user['adminid']>0 && ($adminid>$user['adminid'] || $adminid<1)){
	        showmessage('��������鿴����߼���Ĺ�����Ա�����ּ�¼���뷵�ء�');
		}else{	
		    showmessage('����ת���У����Ժ�','ratelogviewer.php?action='.$actiontype.'&uid='.$user['uid']);
		}
	}elseif ($searchkey == 'uid' && is_numeric($keyword)){
	    $keyword = intval($keyword);
		showmessage('����ת���У����Ժ�','ratelogviewer.php?action='.$actiontype.'&uid='.$keyword);
	}else{
	    showmessage('������󣬵��·Ƿ����ñ���ֹ���뷵�ء�');	
	}
}



?>