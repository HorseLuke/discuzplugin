<?php

/*
“勋章购买易”插件升级程序：ver 0.0.1 hotfix Build 2008011224升级为ver 0.0.2 Build 20090116
*/

require_once './include/common.inc.php';

if(!$discuz_uid) {
	showmessage('not_loggedin', NULL, 'HALTED');
}

if($adminid != 1){
	showmessage('请以管理员身份登录.');
}

$step=empty($step)? 1 : intval($step);
$thisfilename='002_090119_upgradeto002_090928.php';

if($step==1){

  die("本程序将把“勋章购买易”插件从ver 0.0.2 Build 20090119升级为ver 0.0.2 Build 20090928。<br />升级完成后，请重新设置插件的基本设置，以及重新刷新缓存。<br />若确定请<a href=\"{$thisfilename}?step=2\">继续按此</a>");


}elseif($step==2){

	      $medaleasybuy_basicsettings=array(
						     'open'=> 0,
							 'buyextcreditsid'=> 2,
		  );
	      $medaleasybuy_basicsettings=serialize($medaleasybuy_basicsettings);
	      $db->query("REPLACE INTO `{$tablepre}caches` (`cachename` ,`type` ,`dateline` ,`expiration` ,`data` )VALUES ('medaleasybuy_basicsettings', '1', '$timestamp', '0', '{$medaleasybuy_basicsettings}');");
	      $db->query("DELETE FROM `{$tablepre}caches` WHERE cachename='medaleasybuy_medallist'");
	      $db->query("ALTER TABLE `{$tablepre}medaleasybuylog` CHANGE `moneyamount` `moneyamount` int(10) NOT NULL DEFAULT '0'");	      

	      die("<b>升级成功,请立刻删除该文件（{$thisfilename}），以及重新设置插件的基本设置，和重新刷新缓存。</b>");

}


?>