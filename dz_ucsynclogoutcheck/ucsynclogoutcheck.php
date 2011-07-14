<?php
/**
 * 检测uc同步登录是否正常（调用的是uc_user_synlogout接口）
 * 目前在DZ 7.2测试通过
 * @version $Id$
 * @author Horse Luke<horseluke@126.com>
 */
ob_start();
header("Content-type: text/html; charset=utf-8"); 

require_once './include/common.inc.php';

require_once DISCUZ_ROOT.'./uc_client/client.php';

//当开启此检查选项为true时，若ucenter和dz/dx装在同一个数据库，则同时进行uc模拟运行，以检测深层问题
define('UC_OPEN_SIMULATE_CHECK', false);

$data = array();

echo 'DZ config.inc.php中，UCenter地址（常量UC_API）: '. UC_API. '<br />';

echo 'DZ config.inc.php中，UCenter连接类型（常量UC_API_FUNC）: '. UC_API_FUNC. '<br />';

echo 'DZ config.inc.php中，DZ在UCenter的APPID（常量UC_APPID）: '. UC_APPID. '<br />';

echo '<hr />';

$ucapparray = uc_app_ls();
$uc_allowsynlogin = isset($ucapparray[UC_APPID]['synlogin']) ? $ucapparray[UC_APPID]['synlogin'] : -1;
$appsynlogins = 0;
foreach($ucapparray as $apparray) {
	if($apparray['appid'] != UC_APPID) {
		echo '挂载UCenter下的其它APP数据: APP ID: '. $apparray['appid']. '; APP TYPE: '. $apparray['type']. '; APP NAME: '. $apparray['name']. '; Sync Setting: '. (int)$apparray['synlogin']. '<br />';
		if(!empty($apparray['synlogin'])) {
			$appsynlogins = 1;
		}
	}else{
		echo '挂载UCenter下的Discuz数据: APP ID: '. $apparray['appid']. '; APP TYPE: '. $apparray['type']. '; APP NAME: '. $apparray['name']. '; Sync Setting: '. (int)$apparray['synlogin']. '<br />';
	}
}
$data['allowsynlogin'] = isset($ucapparray[UC_APPID]['synlogin']) ? $ucapparray[UC_APPID]['synlogin'] : 1;
$data['allowsynlogin'] = $data['allowsynlogin'] && $appsynlogins ? 1 : 0;

echo '<hr />';

echo 'UCenter中所挂载的DZ，其设置的同步登录值是: '. (int)$uc_allowsynlogin. '<br />';
echo 'UCenter中除了DZ以外，是否有任意一个APP设置了同步登录: '. (int)$appsynlogins. '<br />';
echo '<b>根据以上两个结果，DZ本地应该设定的同步登录为: '. (int)$data['allowsynlogin']. '</b><br />';
echo '<hr />';

echo '<b>实际在DZ本地的同步登录值(变量$allowsynlogin)为: '. (int)$allowsynlogin. '</b><br />';
echo '<hr />';

if($allowsynlogin){
	$data = uc_user_synlogout();
	echo '运行uc_user_synlogout()得到的script值: '. htmlspecialchars($data);;
	
}else{
	echo '由于变量$allowsynlogin不为true或者1，故不能运行uc_user_synlogout()，也即无法运行同步登录或者同步退出';
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