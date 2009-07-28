<?php

/**
 * @name Discuz! Plugin Framework Core Startup Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

// 定义一个常量，表示已经启动Discuz自写插件框架
define('IN_FW',TRUE);

// 定义Discuz自写插件框架的路径
!defined('FW_PATH') && define('FW_PATH',dirname(__FILE__));

class FWBase{

    //框架核心文件数组
    private static $_bootfiles = array(
                                       'BaseModel' => '/Core/BaseModel.class.php',
                                       'BaseController' => '/Core/BaseController.class.php',
                                       'BaseView' => '/Core/BaseView.class.php',
                                       'App' => '/Core/App.class.php');
    // 设置值的数组
    private static $_config = array('DB_DRIVER_NAME' =>'Db_Discuz7' );
    
    /**
     * Discuz自写插件框架启动入口。所有自写的插件运行都要从这个入口启动才能运行
     *
     */
    
    public static function Startup(){
        //设置discuz缓存文件夹位置（因为在$_config数组处无法使用常量）
        self::setConfig('CACHE_DIR', DISCUZ_ROOT.'forumdata/cache/~fwruntime.php');
        //载入框架核心文件
        if(defined('DEBUG_MODE')){
            foreach (self::$_bootfiles as $filename => $filepath){
                require_once(FW_PATH.$filepath);
            }
        }else{
            if(is_file(self::$_config['CACHE_DIR'].'/~fwruntime.php')){
                require (self::$_config['CACHE_DIR'].'/~fwruntime.php');
            }else{
                self::buildCache();
            }
        }
        return true;
    }
    
    /**
     * 生成框架核心文件缓存
     *
     */
    public static function buildCache(){
        $content = $contentTemp = '';
        foreach (self::$_bootfiles as $filename => $filepath){
            $contentTemp = php_strip_whitespace(DZ_PLUGIN_FW_PATH.$filepath);
            $contentTemp = substr(trim($content),5);
            if('?>' == substr($content,-2)) {
                $contentTemp = substr($content,0,-2);
            }
            $content .= $contentTemp;
            unset($contentTemp);
        }
        $content = @file_put_contents(DISCUZ_ROOT.'forumdata/cache/~fwruntime.php','<?php \n\n'.$content);
        if(empty($content)){
            self::throw_exception('系统框架RUNTIME缓存写入失败！请检查forumdata/cache/是否拥有读写权限！','FRAMEWORK_ERROR');
        }
        unset($content);
    }
    
    /**
     * 将指定的值写入到设置数组中
     *
     * @param mix $name 设置的名称，可以是字符串，也可以是数组；若为数组则无需设置$value
     * @param mix $value 要设置的名称的值
     * @return mix
     */
    public static function setConfig($name,$value=''){
        if(is_array($name)){
            foreach ($name as $name => $value){
                $name = strtoupper($name);
                self::$_config($name) = $value;
            }
        }else{
            $name = strtoupper($name);
            self::$_config($name) = $value;
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
        if(!empty(self::$_config($name))){
            return self::$_config($name);
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
        return array('version'=>'0.0.1','build'=>20090724,'rev'=>1,'note'=>'For Discuz! 7');
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
             * 抛出异常，在dz系统中，利用showmessage函数来完成相应的显示。
             *
             * @param string $message 异常信息
             * @param mix $code 代码，可为数值或者数字。
             */
    public static function throw_exception($message,$code=0){
        // 调用dz函数完成抛出异常的操作
        showmessage("<b>系统抛出异常：</b><br />$message");
    }

    
    
    /**
     * 进行基于原程序的数据库驱动工厂模式
     *
     * @param string $dbDriveName 数据库驱动的完整文件名（不包含扩展名）
     * @param array $dbConfig 数据库驱动配置数组
     * @return object
     */
    public static function DbFactory( $dbDriverName,$dbConfig=array() ){
        if(is_file(FW_PATH.'/DbDriver/'.$dbDriverName.'.class.php')){
            require(FW_PATH.'/DbDriver/'.$dbDriverName.'.class.php');
            return new $dbDriverName($dbConfig);
        }else{
            self::throw_exception('不存在指定的驱动！无法启动！','FRAMEWORK_ERROR');
        }
    }
    
    
    /**
     * 取得REQUEST的值，由于dz的特殊机制，因此直接从Globals中取值
     *
     * @param string $name
     * @return mix
     */
    public static function getRequest($name){
        return self::getGlobals($name);
    }
    
    /**
     * 从Globals数组中取值
     *
     * @param unknown_type $name
     * @return mix
     */
    public static function getGlobals($name){
        if(!empty($GLOBALS[$name])){
            return $GLOBALS[$name];
        }else{
            return null;
        }
    }

}

