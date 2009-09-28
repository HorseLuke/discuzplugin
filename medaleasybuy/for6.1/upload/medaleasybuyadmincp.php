<?php

/*
Medal EasyBuy Ver 0.0.2 Build 20090607 For Discuz! 6.1/6.1F - Settingpage

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

require_once './include/common.inc.php';

if ($adminid != 1){
    showmessage('�㲻�ǹ���Ա���޷��������ò���', NULL, 'NOPERM');
}

$navtitle = "ѫ�¹����� - ";

if($action == 'alllogs') {

    $page = max(1, intval($page));
    $start_limit = ($page - 1) * $tpp;

    if(!empty($uid)){
        $uid=intval($uid);
        $username = $db->result_first("SELECT username FROM {$tablepre}members WHERE {$tablepre}members.uid='$uid'");
        if (empty($username)){
            showmessage('�����ڸ��û����뷵�ء�','medaleasybuyadmincp.php?action=alllogs');
        }
        $extrasql=" WHERE mebuy.uid='$uid' ";
        $logstotalnum = $db->result_first("SELECT COUNT(*) FROM {$tablepre}medaleasybuylog WHERE uid='$uid'");
        $multipage = multi($logstotalnum, $tpp, $page, "medaleasybuyadmincp.php?action=alllogs&uid=".$uid);

    }elseif(!empty($medalid)){
        $medalid=intval($medalid);
        $medalname = $db->result_first("SELECT name FROM {$tablepre}medals WHERE medalid='$medalid'");
        if (empty($medalname)){
            showmessage('�����ڸ�ѫ�£��뷵�ء�','medaleasybuyadmincp.php?action=alllogs');
        }
        $extrasql=" WHERE m.medalid='$medalid' ";
        $logstotalnum = $db->result_first("SELECT COUNT(*) FROM {$tablepre}medaleasybuylog WHERE medalid='$medalid'");
        $multipage = multi($logstotalnum, $tpp, $page, "medaleasybuyadmincp.php?action=alllogs&medalid=".$medalid);


    }else{
        $logstotalnum = $db->result_first("SELECT COUNT(*) FROM {$tablepre}medaleasybuylog");
        $multipage = multi($logstotalnum, $tpp, $page, "medaleasybuyadmincp.php?action=alllogs");
        $extrasql='';
    }

    if ($logstotalnum){
        $medallogs = array();
        $query = $db->query("SELECT mebuy.*, mb.username, m.image FROM {$tablepre}medaleasybuylog mebuy
		LEFT JOIN {$tablepre}medals m USING (medalid)
		LEFT JOIN {$tablepre}members mb ON mebuy.uid=mb.uid
		".$extrasql."ORDER BY mebuy.buytime DESC LIMIT $start_limit,$tpp");
        @include_once DISCUZ_ROOT.'./forumdata/cache/cache_medals.php';
        while($medallog = $db->fetch_array($query)) {
            $medallog['name'] = $_DCACHE['medals'][$medallog['medalid']]['name'];
            $medallog['buytime'] = gmdate("$dateformat $timeformat", $medallog['buytime'] + $timeoffset * 3600);
            $medallog['expiration'] = !empty($medallog['expiration']) ? gmdate("$dateformat $timeformat", $medallog['expiration'] + $timeoffset * 3600) : '';
            $medallogs[] = $medallog;
        }
    }
    include template('medaleasybuyalllogs');

} elseif($action == 'set') {


    if($op=='basicsettings'){
        if(!submitcheck('basicsettingssubmit')) {
            @include_once './forumdata/cache/cache_medaleasybuy.php';
            $medaleasybuy_basicsettings['open'] = empty($medaleasybuy_basicsettings['open']) ? 0 : $medaleasybuy_basicsettings['open'];
            $medaleasybuy_basicsettings['buyextcreditsid'] = empty($medaleasybuy_basicsettings['buyextcreditsid']) ? 2 : $medaleasybuy_basicsettings['buyextcreditsid'];
            include template('medaleasybuyadmincp_basicsettings');
        }else{
            $medaleasybuy_basicsettings = array();
            $medaleasybuy_basicsettings['open']=(empty($opennew) || !in_array($opennew,array('0','1'))) ? 0 :intval($opennew);
            $medaleasybuy_basicsettings['buyextcreditsid']=(empty($medalcanbuyextcreditsidnew) || !in_array($medalcanbuyextcreditsidnew,array('1','2','3','4','5','6','7','8'))) ? 2 : intval($medalcanbuyextcreditsidnew);
            //д�����ݿ�
            $medaleasybuy_basicsettings = daddslashes(serialize($medaleasybuy_basicsettings));
            $db->query("REPLACE INTO `{$tablepre}caches` (`cachename` ,`type` ,`dateline` ,`expiration` ,`data` )VALUES ('medaleasybuy_basicsettings', '1', '{$timestamp}', '0', '{$medaleasybuy_basicsettings}');");
            showmessage('�������óɹ���׼��ˢ�»���......<br /><b>�����ڴ�ʱ�ر���������Ա��ó��������һ�����裡</b>', 'medaleasybuyadmincp.php?action=set&op=refreshcache');
        }



    }elseif ($op=='medalcanbuysettings'){
        if ($set=='list'){
            $medallist = array();
            $query = $db->query("SELECT mbuy.medalid, mbuy.moneyamount, m.medalid as medalidcheck, m.name, m.available, m.image, m.type, m.description, m.expiration, m.permission FROM {$tablepre}medaleasybuymedals mbuy
			                     LEFT JOIN {$tablepre}medals m ON mbuy.medalid=m.medalid
			                     ORDER BY m.displayorder");
            while($medal = $db->fetch_array($query)) {
                $medal['permission'] = formulaperm($medal['permission'], 2);
                $medallist[] = $medal;
            }
            include template('medaleasybuyadmincp_medallist');;



        }elseif($set=='add'){
            if(!submitcheck('addmedalsubmit')) {
                $medallist = array();
                $query = $db->query("SELECT * FROM {$tablepre}medals WHERE available='1' AND type='0' ORDER BY displayorder");
                while($medal = $db->fetch_array($query)) {
                    $medal['permission'] = formulaperm($medal['permission'], 2);
                    $medallist[$medal['medalid']] = $medal;
                }
                if ($medallist){
                    $query = $db->query("SELECT medalid FROM {$tablepre}medaleasybuymedals");
                    while($hasbeensetmedal = $db->fetch_array($query)) {
                        if (array_key_exists($hasbeensetmedal['medalid'],$medallist)){
                            unset($medallist[$hasbeensetmedal['medalid']]);
                        }
                    }
                    include template('medaleasybuyadmincp_add');

                }else{
                    showmessage('û��ѫ�¿�����ӽ������б���ȷ����Ҫ��ӵ�ѫ���Ѿ����ڲ�������Ϊ���ֶ����š���','medaleasybuyadmincp.php?action=set&op=medalcanbuysettings&set=list');
                }


            }else{
                $medalidnew=intval($medalidnew);
                $medalcheck = $db->result_first("SELECT medalid FROM {$tablepre}medals WHERE medalid='$medalidnew' AND available='1' AND type='0'");
                if (!$medalcheck){
                    showmessage('���ʧ�ܣ������ڸ�ѫ�»��߸�ѫ��û������Ϊ���ֶ����š���','medaleasybuyadmincp.php?action=set&op=medalcanbuysettings&set=add');
                }
                $moneyamountnew = $moneyamountnew ? intval($moneyamountnew) : 0;
                $db->query("REPLACE INTO {$tablepre}medaleasybuymedals VALUES ('$medalidnew','$moneyamountnew')");
                showmessage('���ѫ�µ��ɹ����б�ɹ���׼��ˢ�»���......<br /><b>�����ڴ�ʱ�ر���������Ա��ó��������һ�����裡</b>', 'medaleasybuyadmincp.php?action=set&op=refreshcache');


            }

        }elseif($set=='modify' && submitcheck('modifymoneyamount')){
            $medalid = intval($medalid);
            $delmedal = $delmedal ? intval($delmedal) : 0;
            if ($delmedal){
                $db->query("DELETE FROM {$tablepre}medaleasybuymedals WHERE medalid='$delmedal'");
                showmessage('ɾ���ɹ���׼��ˢ�»���......<br /><b>�����ڴ�ʱ�ر���������Ա��ó��������һ�����裡</b>', 'medaleasybuyadmincp.php?action=set&op=refreshcache');
            }
            $moneyamountnew = $moneyamountnew ? intval($moneyamountnew) : 0;
            $db->query("UPDATE {$tablepre}medaleasybuymedals SET moneyamount='$moneyamountnew' WHERE medalid='$medalid'");
            showmessage('�޸ĳɹ���׼��ˢ�»���......<br /><b>�����ڴ�ʱ�ر���������Ա��ó��������һ�����裡</b>', 'medaleasybuyadmincp.php?action=set&op=refreshcache');

        }else {
            showmessage('undefined_action', NULL, 'HALTED');
        }


    }elseif ($op=='refreshcache'){
        //�������õ����ݿ�У����
        $medaleasybuy_basicsettings_default=array(
            'open'=> 0,
            'buyextcreditsid'=> 2,
        );
        $medaleasybuy_basicsettings = unserialize($db->result_first("SELECT data FROM {$tablepre}caches WHERE cachename='medaleasybuy_basicsettings'"));
        if (empty($medaleasybuy_basicsettings) || !is_array($medaleasybuy_basicsettings)){
            $medaleasybuy_basicsettings = daddslashes(serialize($medaleasybuy_basicsettings_default));
            $db->query("REPLACE INTO `{$tablepre}caches` (`cachename` ,`type` ,`dateline` ,`expiration` ,`data` )VALUES ('medaleasybuy_basicsettings', '1', '{$timestamp}', '0', '{$medaleasybuy_basicsettings}');");
            showmessage('���ݿ�洢�Ļ������ó��ִ��������޸��ɹ���׼������ˢ��......<br /><b>�����ڴ�ʱ�ر���������Ա��ó��������һ�����裡</b>', 'medaleasybuyadmincp.php?action=set&op=refreshcache');
            exit();
        }
        $medaleasybuy_basicsettings = array_merge($medaleasybuy_basicsettings_default,$medaleasybuy_basicsettings);
        unset($medaleasybuy_basicsettings_default);
        
        $medaleasybuy_medallist=array(
        'medalcanbuylistidcache'=> array(),
        'medalcanbuylist'=> array(),
        );
        $query = $db->query("SELECT mbuy.medalid, mbuy.moneyamount FROM {$tablepre}medaleasybuymedals mbuy
			                     LEFT JOIN {$tablepre}medals m ON mbuy.medalid=m.medalid
			                     WHERE m.available='1' ORDER BY m.displayorder");
        while($medal = $db->fetch_array($query)) {
            $medaleasybuy_medallist['medalcanbuylist'][$medal['medalid']] = $medal;
            $medaleasybuy_medallist['medalcanbuylistidcache'][] = $medal['medalid'];
        }
        $cachedata = '$medaleasybuy_basicsettings='.var_export($medaleasybuy_basicsettings,true).';'."\n\n\$medaleasybuy_medallist=".var_export($medaleasybuy_medallist,true).';';
        require_once './include/cache.func.php';
        writetocache('medaleasybuy', '', $cachedata);

        showmessage('����ˢ�³ɹ������ڷ���������ҳ......', 'medaleasybuyadmincp.php?action=set');

    }else{
        include template('medaleasybuyadmincp_index');
    }

} else {
    showmessage('undefined_action', NULL, 'HALTED');
}


?>