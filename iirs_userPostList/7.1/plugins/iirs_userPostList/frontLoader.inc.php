<?php
/**
 * 
 * 基于Discuz!7.1架构下的微型MVC架构——初始化载入文件
 * 本文件为基于Discuz!架构下的微型MVC架构插件框架一部分，主要负责其初始化的载入工作（采取基于URL传值的简单Dispather方式）
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: frontLoader.inc.php 85 2009-11-13 00:45:00 horseluke $
 * @package iirs_userPostList_Discuz_7.1
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

//dz非ajax安全简易修正
$handlekey = ($handlekey && is_string($handlekey)) ? $handlekey : 'default_handlekey';

//包含必要的dz文件及相关缓存文件，不需要在controller和action中指定。
require_once DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';
if(FALSE == @include_once(DISCUZ_ROOT.'./forumdata/cache/plugin_iirs_userPostList_ignoreFidList.php')){
    $_DPLUGIN['iirs_userPostList']['ignoreFidList']  = array();
}

//包含微型MVC框架，并指定插件App的目录
include(dirname(__FILE__).'/Lib/MiniMVC.php');
define('APP_PATH',dirname(__FILE__).'/App');
$actionName = ($action && is_string($action)) ? 'action'.ucfirst($action) : 'actionGetPostlist';

//本插件只有一个Controller，故直接指定之。
require(APP_PATH.'/Controller/DiscuzUserController.php');
$controller = new DiscuzUserController();
if( method_exists($controller,$actionName) ){
    $controller->$actionName();
}else{
    showmessage('undefined_action', NULL,  'HALTED');
}

//Discuz View层输出
if( defined ('APP_TPL_FILENAME') ){
    include template(APP_TPL_FILENAME, $identifier, './plugins/'.$pluginmodule['directory'].'templates');
}