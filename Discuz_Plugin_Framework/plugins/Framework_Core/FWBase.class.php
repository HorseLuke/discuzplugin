<?php

/**
 * @name Discuz! Plugin Framework Core Startup Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

/* FRAMEWORKWORK ERROR EXCEPTION CODE
FRAMEWORK_ERROR:9001
DB_ERROR:9002
BASEMODEL ERROR:9003
BASECONTROLLER ERROR:9004
*/

// 定义一个常量，表示已经启动Discuz自写插件框架
define('IN_FW',TRUE);

// 定义Discuz自写插件框架的路径
!defined('FW_PATH') && define('FW_PATH',dirname(__FILE__));









class FWBase{

    //框架核心文件数组
    protected static $_bootfiles = array(
                                       'App' => '/Core/App.class.php',
                                       'BaseModel' => '/Core/BaseModel.class.php',
                                       'BaseController' => '/Core/BaseController.class.php',
                                       'BaseView' => '/Core/BaseView.class.php',
                                       'FileCache' => '/Core/FileCache.class.php'
                                       );
    // 设置值的数组
    protected static $_config = array(
                                                         'DB_DRIVER_NAME' =>'Db_Discuz7' ,
                                                         'DEFAULT_CONTROLLER' => 'Index',
                                                         'DEFAULT_ACTION' => 'Index',
                                                         'TIME_ZONE' =>'PRC',
                                                        );
    
    /**
     * Discuz自写插件框架启动入口。所有自写的插件运行都要从这个入口启动才能运行
     *
     */
    
    public static function startup($config=array()){
        //如果传入设置值，就对其进行设置初始化处理
        if(!empty($config)){
            self::setConfig($config);
        }
        
        /* dz已经完成设置，所以注释掉
        // 设置系统时区(PHP5必须，否则在STRICT环境下报错)
        if(function_exists('date_default_timezone_set')){
            date_default_timezone_set(FWBase::getConfig('TIME_ZONE'));
        }
        */
        
        //设置discuz缓存文件夹位置（因为在$_config数组处无法使用常量）
        self::$_config['CACHE_DIR']=DISCUZ_ROOT.'forumdata/cache';

        //载入框架核心文件
        if(defined('APP_DEBUG_MODE')){
            foreach (self::$_bootfiles as $filename => $filepath){
                $filepath = FW_PATH."{$filepath}";
                require_once($filepath);
            }
        }else{
            $cachefile = self::$_config['CACHE_DIR'].'/~fwruntime.php';
            if(is_file($cachefile)){
                require ($cachefile);
            }else{
                self::buildCache();
                require ($cachefile);
            }
        }

        //return true;
        return new App();
    }
    
    /**
     * 生成框架核心文件缓存
     *
     */
    public static function buildCache(){
        $content = $contentTemp = '';
        foreach (self::$_bootfiles as $filename => $filepath){
            $filepath = FW_PATH."{$filepath}";
            //require_once($filepath);
            $contentTemp = php_strip_whitespace($filepath);
            $contentTemp = substr(trim($contentTemp),5);
            $contentTemp = str_replace("!defined('IN_FW') && exit('ACCESS IS NOT ALLOWED!');",'',$contentTemp);
            if('?>' == substr($contentTemp,-2)) {
                $contentTemp = substr($contentTemp,0,-2);
            }
            $content .= $contentTemp;
            unset($contentTemp);
        }
        $content = @file_put_contents(self::$_config['CACHE_DIR'].'/~fwruntime.php','<?php  !defined(\'IN_FW\') && exit(\'ACCESS IS NOT ALLOWED!\');'.$content);
        if(empty($content)){
            self::throw_exception('系统框架RUNTIME缓存写入失败！请检查forumdata/cache/是否拥有读写权限！',9001);
        }
        unset($content);
    }
    
    /**
     * 将指定的值写入到设置数组中
     *
     * @param mixed $name 设置的名称，可以是字符串，也可以是数组；若为数组则无需设置$value
     * @param mixed $value 要设置的名称的值
     * @return mixed
     */
    public static function setConfig($name,$value=''){
        if(is_array($name)){
            foreach ($name as $name => $value){
                $name = strtoupper($name);
                self::$_config[$name] = $value;
            }
        }else{
            $name = strtoupper($name);
            self::$_config[$name] = $value;
        }
        return true;
    }
    
     /**
      * 取得设置数组中的值域
      *
      * @param string $name
      */
    public static function getConfig($name){
        $name = strtoupper($name);
        if(!empty(self::$_config[$name])){
            return self::$_config[$name];
        }else{
            return null;
        }
    }
    
    /**
     * 取得本自写框架的版本
     *
     * @return array 版本信息
     */
    public static function getVersion(){
        return array('version'=>'0.0.1','build'=>20090803,'rev'=>71,'note'=>'For Discuz! 7');
    }

   /**
    * 取得本自写框架的作者
    *
    * @return array 作者和框架信息
    */
    public static function getAuthor(){
        return array('author'=>'Horse Luke', 'email'=>'horseluke@126.com', 'Description'=>'Discuz! Plugin Framework');
    }
    
    /**
    * 抛出异常，在dz系统+非调试模式中，利用showmessage函数来完成相应的显示。
    * 调试模式下则直接抛出异常
    *
    * @param string $message 异常信息
    * @param mixed $code 代码，可为数值或者数字。
    */
    public static function throw_exception($message,$code=0){
        if(!defined('APP_DEBUG_MODE')){ 
             // 调用dz函数完成抛出异常的操作 
             showmessage("<b>系统抛出异常（代号: {$code}）：</b><br />{$message}"); 
         }else{ 
             throw new Exception($message.$code); 
         }
    }

    
    
    /**
     * 进行基于原程序的数据库驱动工厂模式
     *
     * @param string $dbDriverName 数据库驱动的完整文件名（不包含扩展名）
     * @param array $dbConfig 数据库驱动配置数组
     * @return object
     */
    public static function DbFactory( $dbDriverName,$dbConfig=array() ){
        if(is_file(FW_PATH.'/DbDriver/'.$dbDriverName.'.class.php')){
            require(FW_PATH.'/DbDriver/'.$dbDriverName.'.class.php');
            return new $dbDriverName($dbConfig);
        }else{
            self::throw_exception('不存在指定的驱动！无法启动！',1);
        }
    }
    
    
    /**
     * 取得REQUEST的值，由于dz的特殊机制，因此直接从Globals中取值
     *
     * @param string $name
     * @return mixed
     */
    public static function getRequest($name){
        if(!empty($GLOBALS[$name])){
            return $GLOBALS[$name];
        }else{
            return null;
        }
    }
    
    /**
     * 从Globals数组中取值
     *
     * @param unknown_type $name
     * @return mixed
     */
    public static function getGlobals($name){
        if(!empty($GLOBALS[$name])){
            return $GLOBALS[$name];
        }else{
            return null;
        }
    }

}

