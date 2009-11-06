<?php
/**
 * 
 * 用户帖子信息列表之——前台框架初始化载入的文件
 * 本文件为基于Discuz!架构下的微型MVC架构插件框架一部分，主要负责其初始化的载入工作（采取基于URL传值的简单Dispather方式）
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: frontLoader.inc.php 73 2009-11-06 20:30:00 horseluke $
 * @package iirs_userPostList_Discuz_7.1
 */

//安全过滤URL传值代码
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if( empty($uid) || !is_numeric($uid) || $uid==$discuz_uid ){
    showmessage('<b>本链接不支持查看自己的帖子。</b><br /><a href="my.php?item=threads" target="_blank">要查看自己的帖子，请点击这里进入“个人中心”的“我的帖子”查看。</a>', NULL,  'HALTED');
    exit();
}else{
    $uid = abs(intval($uid));
}

showmessage("OK！已接收到uid值：{$uid}。请继续代码编写。", NULL,  'HALTED');
exit();