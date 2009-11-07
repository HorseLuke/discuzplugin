<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class DiscuzUserModel extends BaseModel{
    
    public $uid;
    public $username;
    public $groupid;
    public $adminid;
    protected $disallowVisitFidList;
    
    /**
     * Construct an instance of user
     *
     * @param numeric $uid 用户的uid值
     */
    public function __construct($uid){
        parent::__construct();
        if($uid == $GLOBALS['discuz_uid']){
            $this->uid = $GLOBALS['discuz_uid'];
            $this->username = $GLOBALS['discuz_user'];
            $this->groupid = $GLOBALS['groupid'];
            $this->adminid = $GLOBALS['adminid'];
        }else{
            $member = $this->db->fetch_first("SELECT uid,username,groupid,adminid FROM {$GLOBALS['tablepre']}members WHERE uid = '{$uid}'");
            if(!is_array($member)){
                showmessage('不存在此用户，请返回。',null,'HALTED');
            }else{
                $this->uid = $member['uid'];
                $this->username = $member['username'];
                $this->groupid = $member['groupid'];
                $this->adminid = $member['adminid'];
            }
        }
    }
    
    public function getDisallowVisitFidList(){
        
    }

    
    public function getPostlist($startnum,$limitnum,$ignoreFidList=array()){
        
    }
    
    
    
}