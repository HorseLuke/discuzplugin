<?php
/**
 * 
 * 用户帖子信息列表之——后台权限设置
 * 本文件采取面向过程的编程方法
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: adminPermitEdit.inc.php 73 2009-11-06 20:30:00 horseluke $
 * @package iirs_userPostList_Discuz_7.1
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

//echo DISCUZ_ROOT;

if(FALSE == @include_once(DISCUZ_ROOT.'./forumdata/cache/cache_usergroups.php')){
    updatecache();
    cpmsg('用户组缓存不存在，重刷缓存中，请稍候......','admincp.php?action=plugins&operation=config&identifier=iirs_userPostList&mod=admin','loading');
    //cpmsg('系统缓存出现错误，无法进行设置！<br />正在转向更新缓存页面，请稍候......','admincp.php?action=tools&operation=updatecache','error');
}

foreach ($_DCACHE['usergroups'] as $groupid => $group){
    switch ($group['type']){
        case 'system':
            $group['type'] = '系统用户组';
            break;
        case 'member':
            $group['type'] = '普通用户组';
            break;
        case 'special':
            $group['type'] = '特殊用户组';
            break;
        default:
            $group['type'] = '未知性质用户组';
            break;
    }
    echo "groupid {$groupid} with name {$group['grouptitle']}, grouptype is {$group['type']}.<br />";
}