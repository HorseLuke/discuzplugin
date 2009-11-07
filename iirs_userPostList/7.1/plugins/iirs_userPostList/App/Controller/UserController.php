<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class UserController extends BaseController{

    public function __construct(){
        $this->_getParamFromUrl();
        parent::__construct();
    }
    
    public function getThreadlistAction(){
        showmessage("getThreadlistAction: <a href=\"search.php?srchuid={$this->_param['uid']}&amp;srchfid=all&amp;srchfrom=0&amp;searchsubmit=yes\" target=\"_blank\">点击这里搜索他发布的帖子</a>", NULL,  'HALTED');
    }
    
    public function getPostlistAction(){
        //showmessage("getPostlistAction: 成功接收到uid值：{$this->_param['uid']}。请继续代码编写。", NULL,  'HALTED');
        $this->assign('uid',$this->_param['uid']);
        $this->display('UserController_getPostlistAction');
    }
    
    
    private function _getParamFromUrl(){
        //安全过滤URL传值代码
        if( empty($GLOBALS['uid']) || !is_numeric($GLOBALS['uid']) || ($GLOBALS['uid'] < 1) ||$GLOBALS['uid'] == $GLOBALS['discuz_uid'] ){
            showmessage('<b><a href="my.php?item=threads" target="_blank">要查看自己发布的帖子，请点击这里进入“个人中心”的“我的帖子”查看。</a></b>', NULL,  'HALTED');
            exit();
        }else{
            $this->_param['uid'] = abs(intval($GLOBALS['uid']));
        }
        $this->_param['inajax'] = ( empty($GLOBALS['inajax']) || ($GLOBALS['inajax'] != 1) ) ? 0 : 1;
    }
}