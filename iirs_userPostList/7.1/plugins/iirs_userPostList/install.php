<?php
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
    $finish = TRUE;
}