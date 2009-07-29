<?php

/**
 * @name Discuz! Plugin Framework Core BaseModel Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0.
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_FW') && exit();

class BaseModel{
    public $_pk='id';    //主键
    public $db = '';    //db实例
    public $data = array();  //查找到的数据
    
    /**
     * 创建模型
     *
     */
    public function __construct(){
        global $tablepre;
        $dbDirverName = FWBase::getConfig('DB_DRIVER_NAME');
        $this->db = FWBase::DbFactory($dbDirverName);
        unset($dbDirverName);
    }
    
    public function throw_exception($message,$code='BASEMODEL_ERROR'){
        FWBase::throw_exception($message,$code);
    }
    
    public function throw_exception($message,$code='BASECONTROLLER_ERROR'){
        FWBase::throw_exception($message,$code);
    }
    
    public function showMessage($message, $url_forward = '', $extra = '', $forwardtype = 0){
        showmessage($message, $url_forward, $extra, $forwardtype);
    }
    
    public function find(){
        
    }
    
    public function findall(){
        
    }
    
    public function add(){
        
    }
    
    public function save(){
        
    }
    
    public function delete(){
        
    }
    
}