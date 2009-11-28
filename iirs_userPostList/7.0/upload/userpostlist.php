<?php
/**
 * 
 * 基于Discuz!7.0架构下的微型MVC架构——初始化载入文件
 * 本文件为基于Discuz!架构下的微型MVC架构插件框架一部分，主要负责其初始化的载入工作（采取基于URL传值的简单Dispather方式）
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: userpostlist.php 95 2009-11-28 13:45:00 horseluke $
 * @package iirs_userPostList_Discuz_7.0
 */

define('NOROBOT',true);
require_once dirname(__FILE__).'/include/common.inc.php';

//DZ7.0进行部分初始化设置，以模拟DZ7.1的部分功能
$identifier = 'iirs_userPostList';
$navigation='';

//dz非ajax安全简易修正
$handlekey = ($handlekey && is_string($handlekey)) ? $handlekey : 'default_handlekey';


//包含微型MVC框架，并指定插件App的目录
include(dirname(__FILE__)."/plugins/{$identifier}/Lib/MiniMVC.php");
define('APP_PATH',dirname(__FILE__)."/plugins/{$identifier}/App");
$actionName = ($action && is_string($action)) ? 'action'.ucfirst($action) : 'actionGetPostlist';

//引入语言包和针对7.0的兼容函数包
$charsetLang = ( $charset && in_array($charset,array('utf-8','big5','gbk')) ) ? $charset : 'gbk' ;
@include_once(APP_PATH."/Lang/{$charsetLang}.php");
include(dirname(__FILE__)."/plugins/{$identifier}/Lib/compatibleFunctionTo70.php");


//包含必要的dz文件及相关缓存文件，不需要在controller和action中指定。
require_once DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';
@include_once DISCUZ_ROOT.'./forumdata/cache/cache_icons.php';
if(FALSE == @include_once(DISCUZ_ROOT.'./forumdata/cache/plugin_iirs_userPostList_ignoreFidList.php')){
    $_DPLUGIN['iirs_userPostList']['ignoreFidList']  = array();
    if (1 == $adminid) {
        showmessage( $scriptlang[$identifier]['plugin_setting_cache_lost'] , NULL,  'HALTED');
    }
}

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
    include template(APP_TPL_FILENAME, $identifier, './plugins/'.$identifier.'/templates');
}