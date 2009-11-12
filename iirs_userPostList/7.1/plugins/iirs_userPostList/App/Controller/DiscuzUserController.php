<?php
/**
 * DiscuzUser控制器
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: DiscuzUserController.php 85 2009-11-13 00:45:00 horseluke $
 * @package iirs_userPostList_Discuz_7.1
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class DiscuzUserController extends BaseController{

    /**
     * Controller实例化时的初始化操作，包括重要url传值解析与过滤
     *
     */
    public function __construct(){
        parent::__construct();
        //假如查看的是自己，则引导到个人中心查看
        if( empty($GLOBALS['uid']) || !is_numeric($GLOBALS['uid']) || ($GLOBALS['uid'] < 1) ||$GLOBALS['uid'] == $GLOBALS['discuz_uid'] ){
            showmessage('<b><a href="my.php?item=posts" target="_blank">'.$GLOBALS['scriptlang'][$GLOBALS['identifier']]['use_personal_center_to_see_own_threads'].'</a></b>', NULL,  'HALTED');
            //exit();
        //没有阅读权限，禁止之
        }elseif(empty($GLOBALS['readaccess'])){
            showmessage('group_nopermission', NULL,  'HALTED');
            //exit();
        //没有权限查看他人信息，禁止之
        }elseif(!$GLOBALS['allowviewpro']){
            showmessage('group_nopermission', NULL, 'NOPERM');
        }else{
            $this->_param['uid'] = abs(intval($GLOBALS['uid']));
        }
        $this->_param['page'] = ( empty($GLOBALS['page']) || !is_numeric($GLOBALS['page']) ) ? 1 : abs(intval($GLOBALS['page']));
    }
    
    /**
     * action：获取用户的回复
     *
     */
    public function actionGetPostlist(){
        require_once(APP_PATH.'/Model/DiscuzUserModel.php');
        $targetUser = new DiscuzUserModel($this->_param['uid']);    //被操作的目标用户实例
        $currentUser = new DiscuzUserModel($GLOBALS['discuz_uid']);      //当前用户实例
        
        $multipage = '';
        $limitnum = 25;
        $startnum = ($this->_param['page'] - 1) * $limitnum;
        $ignoreFidList = array_merge( $currentUser->getDisallowVisitFidList(), $GLOBALS['_DPLUGIN']['iirs_userPostList']['ignoreFidList']);;
        $result = $targetUser->getPostlist( $startnum , $limitnum , $ignoreFidList );

        if($result['totalCount'] > 0){
            $multipage = multi($result['totalCount'], $limitnum, $this->_param['page'] , "plugin.php?id=iirs_userPostList:frontLoader&amp;action=getPostlist&amp;uid={$targetUser->uid}&amp;handlekey=iirs_userPostList_{$targetUser->uid}");
        }
        
        $this->assign('multipage',$multipage);
        $this->assign('datalist',$result['datalist']);
        $this->assign('uid',$targetUser->uid);
        $this->assign('username',$targetUser->username);
        $this->display('DiscuzUserController_actionGetPostlist');
    }
    
    
    
    /**
     * action：获取用户发表的主题
     *
     */
    public function actionGetThreadlist(){
        require_once(APP_PATH.'/Model/DiscuzUserModel.php');
        $targetUser = new DiscuzUserModel($this->_param['uid']);    //被操作的目标用户实例
        $currentUser = new DiscuzUserModel($GLOBALS['discuz_uid']);      //当前用户实例
        
        $multipage = '';
        $limitnum = 25;
        $startnum = ($this->_param['page'] - 1) * $limitnum;
        $ignoreFidList = array_merge( $currentUser->getDisallowVisitFidList(), $GLOBALS['_DPLUGIN']['iirs_userPostList']['ignoreFidList']);;
        $result = $targetUser->getThreadlist( $startnum , $limitnum , $ignoreFidList );

        if($result['totalCount'] > 0){
            $multipage = multi($result['totalCount'], $limitnum, $this->_param['page'] , "plugin.php?id=iirs_userPostList:frontLoader&amp;action=getThreadlist&amp;uid={$targetUser->uid}&amp;handlekey=iirs_userPostList_{$targetUser->uid}");

        }
        
        $this->assign('multipage',$multipage);
        $this->assign('datalist',$result['datalist']);
        $this->assign('uid',$targetUser->uid);
        $this->assign('username',$targetUser->username);
        $this->display('DiscuzUserController_actionGetThreadlist');
    }
    
}