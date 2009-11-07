<?php
/**
 * 
 * 用户帖子信息列表之——嵌入脚本
 * 本文件为遵循Discuz! 7.1架构下的嵌入脚本
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: frontLoader.inc.php 73 2009-11-06 20:30:00 horseluke $
 * @package iirs_userPostList_Discuz_7.1
 */

class plugin_iirs_userPostList {
    
    /**
     * 嵌入点：主题内容页个人资料框图标区域（output）
     *
     * @global $postlist 单个主题下的帖子列表
     * @return array 带链接的一个图标，用于给用户查看该人的发布的帖子
     */
    
    public function viewthread_imicons_output(){
        $return = array();
        if( is_array($GLOBALS['postlist']) ){
            foreach( $GLOBALS['postlist'] as $pid => $post ){
                if($post['authorid'] > 0){
                    $return[] = "<a href=\"plugin.php?id=iirs_userPostList:frontLoader&uid={$post['authorid']}\" onclick=\"javascript: showWindow('iirs_userPostList_{$post['authorid']}', this.href, 'get', 1);;\"><img src=\"plugins/iirs_userPostList/Public/Images/comments.gif\" alt=\"查看{$post['author']}发布的帖子\" title=\"查看{$post['author']}发布的帖子\" /></a>";
                }
            }
        }
        return $return;
    }
    
    /**
     * 嵌入点：个人资料页侧边头部（output）
     *
     * @global $member 用户信息
     * @return string 一个链接，用于给用户查看该人的回复过的帖子
     */
    public function profile_side_top_output(){
        $return = "<li class=\"searchpost\"><a href=\"plugin.php?id=iirs_userPostList:frontLoader&uid={$GLOBALS['member']['uid']}&action=getPostlist\" onclick=\"javascript: showWindow('iirs_userPostList_{$GLOBALS['member']['uid']}', this.href, 'get', 1);;\">查看回复过的帖子</a></li>";
        return $return;
    }

}