<?php
/**
 * 
 * 基于Discuz!7.1架构下的微型MVC架构——初始化载入文件
 * 本文件为基于Discuz!架构下的微型MVC架构插件框架一部分，主要负责其初始化的载入工作（采取基于URL传值的简单Dispather方式）
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: frontLoader.inc.php 95 2009-11-28 13:45:00 horseluke $
 * @package iirs_userPostList_Discuz_7.1
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

define('USE_RUNTIME', 1);

$identifier = 'iirs_digg';

//dz非ajax安全简易修正
$handlekey = ($handlekey && is_string($handlekey)) ? $handlekey : 'default_handlekey';

//包含必要的dz文件及相关缓存文件，不需要在controller和action中指定。


if( FALSE == @include_once(DISCUZ_ROOT.'./forumdata/cache/plugin_iirs_digg.php') ){
    showmessage( '设置丢失！请重新在后台设定！' , NULL,  'HALTED');
}

if( !isset($_DPLUGIN['iirs_digg']['vars']) || empty($_DPLUGIN['iirs_digg']['vars']) ){
    showmessage( '设置丢失！请重新在后台设定！' , NULL,  'HALTED');
}

//包含微型MVC框架，并指定插件App的目录（考虑是否使用runtime）
if( !defined('USE_RUNTIME') ){
    require_once(dirname(__FILE__).'/Lib/mini/Controller.php');
    require_once(dirname(__FILE__).'/Lib/mini/Model.php');
    require_once(dirname(__FILE__).'/Lib/common.php');
}else{
    require_once(dirname(__FILE__).'/~runtime.php');
}

define('APP_PATH',dirname(__FILE__).'/App');

//导入设置
common::config('set', $_DPLUGIN[$identifier]['vars']);


//showmessage( '完成！请继续开发！' , NULL,  'HALTED');

//controller实例化
$controllerName = (string)common::input('c', 'GET', null);
if( !preg_match("/^[a-z0-9_\-]+$/i", $controllerName) ){
    showmessage('undefined_action', NULL,  'HALTED');
}else{
    $controllerName = $controllerName. 'Controller';
}
$controller = common::getInstanceOf($controllerName, $controllerName, APP_PATH. '/Controller/'. $controllerName. '.php');


//运行action
$actionName = (string)common::input('a', 'GET', 'index');
if( method_exists($controller, $actionName. 'Action') ){
    $actionName = $actionName. 'Action';
    $controller->$actionName();
}else{
    showmessage('undefined_action', NULL,  'HALTED');
}

//Discuz View层输出
if( defined ('APP_TPL_FILENAME') ){
    include template(APP_TPL_FILENAME, $identifier, './plugins/'.$pluginmodule['directory'].'templates');
}