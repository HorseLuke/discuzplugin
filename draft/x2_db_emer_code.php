<?php
/**
 * 紧急情况下运行db的文件示例
 * X2测试过，X2.5不知道了
 * 注意：
 * 如果要运行复杂的SQL，比如union等，需要关闭DX的SQL防御机制才能正常运行:
 * config/config_global.php，$_config['security']['querysafe']['status']设为0;
 * 若有更改，运行结束后，务必记得要重新设为1！
 * 
 * @version $Id$
 */


//记得更改运行时间
set_time_limit(300);

define('APPTYPEID', 0);
define('CURSCRIPT', 'testsadf');

require './source/class/class_core.php';

$discuz = & discuz_core::instance();

define('CURMODULE', 'asdfdasf');

$discuz->init();

//db搞定了
$db = DB::object();


//剩下的自由发挥了，以下为示例
$res = $db->fetch_first('SELECT * FROM '. DB::table('common_member'). ' WHERE `uid` = \'1\'');

var_export($res);