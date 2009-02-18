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

$step=empty($step)? 1 :$step;
$thisfilename='001_081224_upgradeto002_090119.php';

if($step==1){

  die("本程序将把“勋章购买易”插件从ver 0.0.1 hotfix Build 2008011224升级为ver 0.0.2 Build 20090119<br />。若确定请<a href=\"{$thisfilename}?step=2\">继续按此</a>");


}elseif($step==2){



$db->query("DROP TABLE IF EXISTS `{$tablepre}medaleasybuymedals`");
$db->query("CREATE TABLE `{$tablepre}medaleasybuymedals` (
                `medalid` smallint(6) unsigned NOT NULL DEFAULT '0' PRIMARY KEY,
                `moneyamount` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=gbk;");
$medaleasybuy_basicsettings=array(
						     'open'=> 0,
							 'buyextcreditsid'=> 2,
							 );
$medaleasybuy_medallist=array(
						     'medalcanbuylistidcache'=> array(),
							 'medalcanbuylist'=> array(),
							 );
$db->query("REPLACE INTO `{$tablepre}caches` (`cachename` ,`type` ,`dateline` ,`expiration` ,`data` )VALUES ('medaleasybuy_basicsettings', '1', '$timestamp', '0', '\$medaleasybuy_basicsettings=".daddslashes(var_export($medaleasybuy_basicsettings,true))."');");
$db->query("REPLACE INTO `{$tablepre}caches` (`cachename` ,`type` ,`dateline` ,`expiration` ,`data` )VALUES ('medaleasybuy_medallist', '1', '$timestamp', '0', '\$medaleasybuy_medallist=".daddslashes(var_export($medaleasybuy_medallist,true))."');");

die("<b>升级成功,请立刻删除该文件（{$thisfilename}）。</b>");

}

?>