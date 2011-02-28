<?php
/**
 * 检测uc同步登录是否正常（调用的是uc_user_synlogout接口）
 * 目前在DZ 7.2测试通过
 * @version $Id$
 * @author Horse Luke<horseluke@126.com>
 */
require_once './include/common.inc.php';

require_once DISCUZ_ROOT.'./uc_client/client.php';

$data = array();

echo 'The Dz UC_APPID is: '. UC_APPID. '<br />';

$ucapparray = uc_app_ls();
$uc_allowsynlogin = isset($ucapparray[UC_APPID]['synlogin']) ? $ucapparray[UC_APPID]['synlogin'] : -1;
$appsynlogins = 0;
foreach($ucapparray as $apparray) {
	if($apparray['appid'] != UC_APPID) {
		if(!empty($apparray['synlogin'])) {
			$appsynlogins = 1;
		}
	}
}
$data['allowsynlogin'] = isset($ucapparray[UC_APPID]['synlogin']) ? $ucapparray[UC_APPID]['synlogin'] : 1;
$data['allowsynlogin'] = $data['allowsynlogin'] && $appsynlogins ? 1 : 0;

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