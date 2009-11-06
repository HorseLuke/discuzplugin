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
                $return[] = "<a href=\"plugin.php?id=iirs_userPostList:frontLoader&uid={$post['authorid']}\" onclick=\"javascript: showWindow('iirs_userPostList', this.href, 'get', 0);;\"><img src=\"plugins/iirs_userPostList/images/comments.gif\" alt=\"查看此人发布的帖子\" title=\"查看此人发布的帖子\" /></a>";
            }
        }
        return $return;
    }
}