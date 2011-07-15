<?php
/**
 * 检测uc同步登录是否正常（调用的是uc_user_synlogout接口）
 * 目前在DZ 7.2测试通过
 * @version $Id$
 * @author Horse Luke<horseluke@126.com>
 */
ob_start();
header("Content-type: text/html; charset=utf-8"); 

define('APPTYPEID', 999999);
define('CURSCRIPT', 'homedsfaasdfsda');

require_once dirname(__FILE__).'/source/class/class_core.php';
$discuz = & discuz_core::instance();
$discuz->init();


loaducenter();


//当开启此检查选项为true时，若ucenter和dz/dx装在同一个数据库，则同时进行uc模拟运行，以检测深层问题
define('UC_OPEN_SIMULATE_CHECK', true);

$data = array();

echo 'DX config/config_ucenter.php中，UCenter地址（常量UC_API）: '. UC_API. '<br />';

echo 'DX config/config_ucenter.php中，UCenter连接类型（常量UC_API_FUNC）: '. UC_API_FUNC. '<br />';

echo 'DX config/config_ucenter.php中，DX在UCenter的APPID（常量UC_APPID）: '. UC_APPID. '<br />';

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
		echo '挂载UCenter下的DiscuzX数据: APP ID: '. $apparray['appid']. '; APP TYPE: '. $apparray['type']. '; APP NAME: '. $apparray['name']. '; Sync Setting: '. (int)$apparray['synlogin']. '<br />';
	}
}
$data['allowsynlogin'] = isset($ucapparray[UC_APPID]['synlogin']) ? $ucapparray[UC_APPID]['synlogin'] : 1;
$data['allowsynlogin'] = $data['allowsynlogin'] && $appsynlogins ? 1 : 0;

echo '<hr />';

echo 'UCenter中所挂载的DX，其设置的同步登录值是: '. (int)$uc_allowsynlogin. '<br />';
echo 'UCenter中除了DX以外，是否有任意一个APP设置了同步登录: '. (int)$appsynlogins. '<br />';
echo '<b>根据以上两个结果，DX本地应该设定的同步登录为: '. (int)$data['allowsynlogin']. '</b><br />';
echo '<hr />';

echo '<b>实际在DX本地的同步登录值(变量$_G[\'setting\'][\'allowsynlogin\'])为: '. (int)$_G['setting']['allowsynlogin']. '</b><br />';
echo '<hr />';

if($_G['setting']['allowsynlogin']){
	$data = uc_user_synlogout();
	echo '运行uc_user_synlogout()得到的script值: '. htmlspecialchars($data);;
	
}else{
	echo '由于变量$_G[\'setting\'][\'allowsynlogin\']不为true或者1，故不能运行uc_user_synlogout()，也即无法运行同步登录或者同步退出';
}

echo '<hr />';

//读取缓存文件
if(isset($_GET['showcache']) && 1 == $_GET['showcache']){
	echo '<hr />';
	$cachefile = DISCUZ_ROOT.'./uc_client/data/cache/apps.php';
	if(is_file($cachefile)){
		echo '存储在DX的同步登录缓存文件在：'. $cachefile. '。缓存内容为：<br />';
		echo file_get_contents($cachefile);
	}else{
		echo $cachefile. '文件不存在';
	}
	echo '<hr />';
}


if(UC_API_FUNC == 'uc_api_mysql' && UC_OPEN_SIMULATE_CHECK == true){
	//error_reporting(E_ALL);
	echo '直接操作UCenter结果（等同于调用函数uc_app_ls）:<br />';
	require_once UC_ROOT.'lib/db.class.php';
	require_once UC_ROOT.'model/base.php';
	require_once UC_ROOT.'control/app.php';
	
	$ctrApp = new appcontrol();
	$args = uc_addslashes(array(), 1, TRUE);
	$action = 'onls';
	$ctrApp->input = $args;
	echo nl2br(var_export($ctrApp->$action($args), true));
	
	echo '<hr />';
	
	echo '直接连接数据库操作模拟uc_app_ls结果:<br />';
	if(class_exists('ucclient_db')){
		$dbInst = new ucclient_db();
	}elseif(class_exists('db')){
		$dbInst = new db();
	}else{;
		exit('无法连接数据库');
	}

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
	
}