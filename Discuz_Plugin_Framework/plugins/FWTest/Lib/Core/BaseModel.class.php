<?php

/**
 * @name Discuz! Plugin Framework Core BaseModel Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0.
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_FW') && exit();

class BaseModel{
    public $_pk = '';    //主键
    //该模型对应的表，不需要考虑前缀。若使用find/findall/delete/save等方法，则必须定义。直接query则可略过。
    public $dbTableName = '';
    public $dbTablepre = '';  //该模型对应的表的前缀
    public $db = '';    //db实例
    public $options = array();    // 查询表达式参数，部分借用了ThinkPHP代码
    public $data = array();  //查找到的数据
    
    public function __construct(){
        global $tablepre;
        $this->dbTablepre= $tablepre;
        $this->dbTableName = $this->dbTablepre.$this->dbTablepre;    //合并前缀和表名称。
        $dbDirverName = FWBase::getConfig('DB_DRIVER_NAME');
        $this->db = FWBase::DbFactory($dbDirverName);
    }
    
    /**
     * 利用__call方法实现一些特殊的Model方法 （魔术方法）。本代码来源自ThinkPHP 1.6RC
     *
     * @param unknown_type $method
     * @param unknown_type $args
     */
    public function __call($method,$args){
        if(in_array(strtolower($method),array('field','table','where','order','limit','having','group','distinct','lazy'),true)) {
            // 连贯操作的实现
            $this->options[strtolower($method)] =   $args[0];
            return $this;
        }else{
            FWBase::throw_exception('没有对应的方法！无法建立查询条件！','MODEL_ERROR');
        }
    }
    
}