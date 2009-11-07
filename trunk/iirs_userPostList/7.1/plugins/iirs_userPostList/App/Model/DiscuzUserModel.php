<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class UserModel extends BaseModel{
    
    public $uid;
    public $username;
    public $groupid;
    public $adminid;
    protected $disallowVisitFidList;
    
    public function __construct($uid){
        
    }
    
    public function getDisallowVisitFidList(){
        
    }
    
    public function getThreadlist($startnum,$limitnum,$ignoreFidList=array()){
        
    }
    
    public function getPostlist($startnum,$limitnum,$ignoreFidList=array()){
        
    }
    
    
    
}