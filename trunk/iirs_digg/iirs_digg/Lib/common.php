<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * 各种公用函数的集合类，可使用静态方法调用
 *
 * @author Horse Luke<horseluke@126.com>
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * $Id$
 */
class common{
    
    //存储对象实例
    protected static $_objectInstance = array();
    
    //设置存储区
    public static $config = array();
    
    /**
     * 获取指定对象或者指定索引对象的实例。没有则新建一个并且存储起来。
     *
     * @param string $classname 类名
     * @param string $index 索引，默认等同于$classname
     * @param string $filepath 假如没有该实例，并且需要新建，那么包含哪个具体路径的文件以便实例化？
     */
    public static function getInstanceOf( $classname , $index = null, $filepath = null ){
        if( null === $index ){
            $index = $classname;
        }
        if( isset( self::$_objectInstance[$index] ) ){
            $instance = self::$_objectInstance[$index];
            if( !($instance instanceof $classname) ){
                throw new Exception( "Key {$index} has been tied to other thing." );
            }
        }else{
            if( null !== $filepath && !class_exists($classname) ){
                if( !is_file( $filepath ) ){
                    //考虑虚拟机安全保护问题，故使用trigger_error并@抑制，以此保留可追踪性。
                    @trigger_error('No such controller file can PHP find:'.$filepath, 512 );
                    exit( 'No such controller file can PHP find' );
                }
                require( realpath($filepath) );
            }
            $instance = new $classname();
            self::$_objectInstance[$index] = $instance;
        }
        return $instance;
    }
    
    
    /**
     * 获取$_GET/$_POST/$_REQUEST数组的指定索引变量（若无设置则返回默认值）
     * 由于dz已经将其extract，因此进行特殊的提取流程
     *
     * @param string $k 指定索引
     * @param string $var 获取来源。默认为'GET'（即$_GET），可选值'POST'（对应$_POST）或者'REQUEST'（对应$_REQUEST）
     * @param string $default 默认值。如果该索引变量无设置，那么就返回此值。默认为null
     * @param boolen $emptyCheck 是否进行空值检测？默认为否。
     * @return mixed
     */
    public static function input($k, $var='GET', $default = null, $emptyCheck = false ) {
        $var = '_'. $var;
        $result = $default;
        if( isset($GLOBALS[$var][$k]) && isset($GLOBALS[$k]) ){
            $result = $GLOBALS[$k];
        }
        if( true === $emptyCheck && empty($result) ){
            $result = $default;
        }
        return $result;
    }
    
    
    /**
     * 转义处理（含数组key处理）
     * 改动自Comsenz UCenter
     * 
     * @param mixed $string 需要转义的字符串或者数组
     * @param int $force 是否强制转义（而不管magic_quotes_gpc设置如何）？
     * @param bool $strip 转义前是否进行反转义处理（防止多次转义）？
     * @return mixed
     */
    public static function addslashes($string, $force = 0, $strip = FALSE) {

        if(!ini_get('magic_quotes_gpc') || $force) {
            if(is_array($string)) {
                $temp = array();
                foreach($string as $key => $val) {
                    $key = addslashes($strip ? stripslashes($key) : $key);
                    $temp[$key] = self::addslashes($val, $force, $strip);
                }
                $string = $temp;
                unset($temp);
            } else {
                $string = addslashes($strip ? stripslashes($string) : $string);
            }
        }
        return $string;
    }
    
    /**
     * 设置获取或者设定
     *
     * @param string $type 类型。可选值为get或者set
     * @param mixed $value 当$type为get时，$value为需要查找的设置索引名称；当$type为set时，$value为需要设置的数组；
     * @return mixed
     */
    public static function config( $type, $value ){
        switch ($type){
            case 'get':
                return isset(self::$config[$value]) ? self::$config[$value] : null;
                break;
            case 'set':
                self::$config = array_merge(self::$config, $value);
                return true;
                break;
        }
    }
    
}
