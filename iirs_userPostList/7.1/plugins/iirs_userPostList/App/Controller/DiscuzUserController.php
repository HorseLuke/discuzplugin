<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class DiscuzUserController extends BaseController{

    public function __construct(){
        parent::__construct();
        $this->_getParamFromUrl();
    }
    
    public function getPostlistAction(){
        //showmessage("getPostlistAction: 成功接收到uid值：{$this->_param['uid']}。请继续代码编写。", NULL,  'HALTED');
        require_once(APP_PATH.'/Model/DiscuzUserModel.php');
        $targetUser = new DiscuzUserModel($this->_param['uid']);    //被操作的目标用户实例
        $currentUser = new DiscuzUserModel($GLOBALS['discuz_uid']);      //当前用户实例

        //$postListResult = $targetUser->getPostlist(0,20,$currentUser->getDisallowVisitFidList());

        //$this->assign('floatTitle',"{$targetUser->username}的回复（<a href=\"search.php?srchuid={$targetUser->uid}&amp;srchfid=all&amp;srchfrom=0&amp;searchsubmit=yes\" target=\"_blank\">点击这里搜索他发布的帖子</a>）");
        $this->assign('uid',$targetUser->uid);
        $this->assign('username',$targetUser->username);
        $this->display('DiscuzUserController_getPostlistAction');
    }
    
    
    public function getAttachmentlistAction(){
        require_once(APP_PATH.'/Model/DiscuzUserModel.php');
        $targetUser = new DiscuzUserModel($this->_param['uid']);    //被操作的目标用户实例
        $currentUser = new DiscuzUserModel($GLOBALS['discuz_uid']);      //当前用户实例
        
        $this->assign('uid',$targetUser->uid);
        $this->assign('username',$targetUser->username);
        $this->display('DiscuzUserController_getAttachmentlistAction');
    }
    
    
    private function _getParamFromUrl(){
        //安全过滤URL传值代码
        if( empty($GLOBALS['uid']) || !is_numeric($GLOBALS['uid']) || ($GLOBALS['uid'] < 1) ||$GLOBALS['uid'] == $GLOBALS['discuz_uid'] ){
            showmessage('<b><a href="my.php?item=posts" target="_blank">要查看自己发布的帖子，请点击这里进入“个人中心”的“我的帖子”查看。</a></b>', NULL,  'HALTED');
            exit();
        }else{
            $this->_param['uid'] = abs(intval($GLOBALS['uid']));
        }
        $this->_param['inajax'] = ( empty($GLOBALS['inajax']) || ($GLOBALS['inajax'] != 1) ) ? 0 : 1;
    }
}