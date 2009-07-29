<?php

/**
 * @name Discuz! Plugin Framework Discuz 7 DB class For Model
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_FW') && exit();

class Db_Discuz7{

    
    public function __construct(){

    }
    
    /**
     * 进行查询和以数组返回所有结果
     *
     * @param string $sql sql语句
     * @return array
     */
    public function _query($sql){
        global $db;
        $result = array();
        $db->fetch_all($sql,$result);
        return $result;
    }
    

    /**
     * 运行某语句
     *
     * @param string $sql sq语句
     */
    public function _execute($sql){
        global $db;
        $db->query($sql);
        return true;
    }
    
    /**
     * 查询并返回一个对象
     *
     * @param string $sql sql语句
     * @param string $type 查询形式，根据dz7，可选参数为：UNBUFFERED
     * @param interger $cachetime 是否启用缓存时间。不过该参数在dz7并没有使用过（废弃了？）
     * @return object
     */
    public function query($sql, $type = '', $cachetime = FALSE){
        global $db;
        $resources = $db->query($sql, $type = '', $cachetime = FALSE);
        return $resources;
    }

    /**
     * query之后进行数据获取的操作，期间可以修改数组的东西。详情请看dz代码
     *
     * @param unknown_type $query 数据库返回的数组
     * @param unknown_type $result_type
     * @return unknown
     */
    public function fetch_array($query, $result_type = MYSQL_ASSOC){
        return mysql_fetch_array($query, $result_type);
    }
    
    public function __call($method,$args){
        FWBase::throw_exception("没有对应的查询方法: {$method} 。请返回。",'Db_Discuz7_ERROR');
    }
    
}