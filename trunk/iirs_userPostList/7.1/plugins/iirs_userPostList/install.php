<?php
/**
 * 
 * 用户帖子信息列表之——嵌入安装脚本
 * 本文件主要用于在安装时，检查服务器是否为PHP5.0级以上，否的话则拒绝安装并强制卸载
 * 本文件为遵循Discuz! 7.1架构下的嵌入安装脚本
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: install.php 85 2009-11-13 00:45:00 horseluke $
 * @package iirs_userPostList_Discuz_7.1
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

if(version_compare(PHP_VERSION,'5.0.0','<')){
    $pluginid = $db->result_first("SELECT pluginid FROM {$tablepre}plugins WHERE identifier='iirs_userPostList' LIMIT 1");
    if($pluginid > 0){
        cpmsg($installlang['iirs_userPostList']['PHP_ENV_NOT_SUPPORTED'], "admincp.php?action=plugins&operation=delete&pluginid={$pluginid}&confirmed=yes", 'error');
        exit;
    }else{
        cpmsg($installlang['iirs_userPostList']['PHP_ENV_NOT_SUPPORTED'], '', 'error');
        exit;
    }

}else{
	$_DPLUGIN['iirs_userPostList']['ignoreFidList'] = array();
	$cachedata="if(!defined('IN_DISCUZ')) {exit('Access Denied');}\n\n\$_DPLUGIN['iirs_userPostList']['ignoreFidList']=".var_export($_DPLUGIN['iirs_userPostList']['ignoreFidList'],true).";";
    require_once './include/cache.func.php';
    writetocache('iirs_userPostList_ignoreFidList', '', $cachedata, 'plugin_');
    $finish = TRUE;
}