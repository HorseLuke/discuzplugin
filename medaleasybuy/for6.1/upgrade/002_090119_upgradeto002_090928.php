<?php

/*
��ѫ�¹����ס������������ver 0.0.1 hotfix Build 2008011224����Ϊver 0.0.2 Build 20090116
*/

require_once './include/common.inc.php';

if(!$discuz_uid) {
	showmessage('not_loggedin', NULL, 'HALTED');
}

if($adminid != 1){
	showmessage('���Թ���Ա��ݵ�¼.');
}

$step=empty($step)? 1 : intval($step);
$thisfilename='002_090119_upgradeto002_090928.php';

if($step==1){

  die("�����򽫰ѡ�ѫ�¹����ס������ver 0.0.2 Build 20090119����Ϊver 0.0.2 Build 20090928��<br />������ɺ����������ò���Ļ������ã��Լ�����ˢ�»��档<br />��ȷ����<a href=\"{$thisfilename}?step=2\">��������</a>");


}elseif($step==2){

	      $medaleasybuy_basicsettings=array(
						     'open'=> 0,
							 'buyextcreditsid'=> 2,
		  );
	      $medaleasybuy_basicsettings=serialize($medaleasybuy_basicsettings);
	      $db->query("REPLACE INTO `{$tablepre}caches` (`cachename` ,`type` ,`dateline` ,`expiration` ,`data` )VALUES ('medaleasybuy_basicsettings', '1', '$timestamp', '0', '{$medaleasybuy_basicsettings}');");
	      $db->query("DELETE FROM `{$tablepre}caches` WHERE cachename='medaleasybuy_medallist'");
	      $db->query("ALTER TABLE `{$tablepre}medaleasybuylog` CHANGE `moneyamount` `moneyamount` int(10) NOT NULL DEFAULT '0'");	      

	      die("<b>�����ɹ�,������ɾ�����ļ���{$thisfilename}�����Լ��������ò���Ļ������ã�������ˢ�»��档</b>");

}


?>