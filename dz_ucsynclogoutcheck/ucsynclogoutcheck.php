<?php
/**
 * 检测uc同步登录是否正常（调用的是uc_user_synlogout接口）
 * 目前在DZ 7.2测试通过
 * @version $Id$
 * @author Horse Luke<horseluke@126.com>
 */
require_once './include/common.inc.php';

require_once DISCUZ_ROOT.'./uc_client/client.php';

//当开启此检查选项为true时，若ucenter和dz/dx装在同一个数据库，则同时进行uc模拟运行，以检测深层问题
define('UC_OPEN_SIMULATE_CHECK', false);

$data = array();

echo 'UC CONNECT TYPE: '. UC_API_FUNC. '<br />';

echo 'The Dz UC_APPID is: '. UC_APPID. '<br />';

echo '<hr />';

$ucapparray = uc_app_ls();
$uc_allowsynlogin = isset($ucapparray[UC_APPID]['synlogin']) ? $ucapparray[UC_APPID]['synlogin'] : -1;
$appsynlogins = 0;
foreach($ucapparray as $apparray) {
	if($apparray['appid'] != UC_APPID) {
		echo 'OTHER APP DATA: APP ID: '. $apparray['appid']. '; APP TYPE: '. $apparray['type']. '; APP NAME: '. $apparray['name']. '; Sync Setting: '. (int)$apparray['synlogin']. '<br />';
		if(!empty($apparray['synlogin'])) {
			$appsynlogins = 1;
		}
	}else{
		echo 'DZ APP DATA: APP ID: '. $apparray['appid']. '; APP TYPE: '. $apparray['type']. '; APP NAME: '. $apparray['name']. '; Sync Setting: '. (int)$apparray['synlogin']. '<br />';
	}
}
$data['allowsynlogin'] = isset($ucapparray[UC_APPID]['synlogin']) ? $ucapparray[UC_APPID]['synlogin'] : 1;
$data['allowsynlogin'] = $data['allowsynlogin'] && $appsynlogins ? 1 : 0;

echo '<hr />';

echo 'Sync setting of Dz in Ucenter is: '. (int)$uc_allowsynlogin. '<br />';
echo 'detect sync setting of other apps in Ucenter is: '. (int)$appsynlogins. '<br />';
echo 'Calculate the Sync setting in Dz Local is: '. (int)$data['allowsynlogin']. '<br />';
echo '<hr />';

echo 'Sync setting in Dz Local (variable $allowsynlogin) is: '. (int)$allowsynlogin. '<br />';
echo '<hr />';

if($allowsynlogin){
	$data = uc_user_synlogout();
	echo 'the uc_user_synlogout() data is: '. htmlspecialchars($data);;
	
}else{
	echo 'Can not run uc_user_synlogout() because variable $allowsynlogin in dz is not true or 1';
}

echo '<hr />';
if(UC_API_FUNC == 'uc_api_mysql' && UC_OPEN_SIMULATE_CHECK == true){
	error_reporting(E_ALL);
	echo 'TRY TO FETCH uc_app_ls by UCENTER SIMULATE:<br />';
	include_once UC_ROOT.'./lib/db.class.php';
	include_once UC_ROOT.'./model/base.php';
	include_once UC_ROOT."./control/app.php";
	$ctrApp = new appcontrol();
	$args = uc_addslashes(array(), 1, TRUE);
	$action = 'onls';
	$ctrApp->input = $args;
	echo nl2br(var_export($ctrApp->$action($args), true));
	
	echo '<hr />';
	
	echo 'TRY TO FETCH uc_app_ls by UCENTER DATABASE:<br />';
	$dbInst = new ucclient_db();
	$dbInst->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, '', UC_DBCHARSET, UC_DBCONNECT, UC_DBTABLEPRE);
	$sql = "SELECT * FROM ".UC_DBTABLEPRE."applications";
	echo $sql. '<br />';
	$arr = $dbInst->fetch_all($sql);
	foreach($arr as $k => $v) {
		isset($v['extra']) && !empty($v['extra']) && $v['extra'] = unserialize($v['extra']);
		unset($v['authkey']);
		$arr[$k] = $v;
	}
	echo nl2br(var_export($arr, true));
	
	echo '<br />END OF NATIVE SIMULATION';
}