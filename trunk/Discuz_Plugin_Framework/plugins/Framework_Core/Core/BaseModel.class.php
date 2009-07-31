<?php

/**
 * @name Discuz! Plugin Framework Core BaseModel Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0.
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_FW') && exit('ACCESS IS NOT ALLOWED!');

class BaseModel{
    public $_pk='id';    //主键
    public $dbTablepre = 'cdb_';    //表前缀
    public $options = array(); // 查询表达式参数，部分借用了ThinkPHP代码
    public $db = '';    //db实例
    public $data = array();  //查找到的数据
    
    /**
     * 创建模型
     *
     */
    public function __construct(){
        global $tablepre;
        if('cdb_' != $tablepre){
            $this->dbTablepre = $tablepre;
        }
        $dbDirverName = FWBase::getConfig('DB_DRIVER_NAME');
        $this->db = FWBase::DbFactory($dbDirverName);
        unset($dbDirverName);
    }
    
    public function throw_exception($message,$code=9003){
        FWBase::throw_exception($message,$code);
    }
    
    public function showMessage($message, $url_forward = '', $extra = '', $forwardtype = 0){
        showmessage($message, $url_forward, $extra, $forwardtype);
    }
    
 /*
     //没有实现的方法，因此进行注释掉的操作
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
    
*/
 
     /** 
      * 利用__call方法实现一些特殊的Model方法 （魔术方法）。本代码来源自ThinkPHP 1.6RC 
      * 
      * @param unknown_type $method 
      * @param unknown_type $args 
      */ 
     public function __call($method,$args){ 
         if(in_array(strtolower($method),array('field','table','where','order','limit','having','group','distinct'),true)) { 
             // 连贯操作的实现 
             $this->options[strtolower($method)] =   $args[0]; 
             return $this; 
         }else{ 
             $this->throw_exception('没有对应的方法！无法建立查询条件！'); 
         } 
     } 
 
    
}