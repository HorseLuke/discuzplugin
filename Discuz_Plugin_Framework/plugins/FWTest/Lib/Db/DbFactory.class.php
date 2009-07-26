<?php

/**
 * @name Discuz! Plugin Framework Core DbFactory Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_FW') && exit();

class DbFactory{

    public static function factory( $dbDriveName,$dbConfig=array() ){
        if(is_file(FW_PATH.'/Db/Driver/'.$dbDriveName.'.class.php')){
            require(FW_PATH.'/Db/Driver/'.$dbDriveName.'.class.php');
            return new $dbDriveName($dbConfig);
        }else{
            FWBase::throw_exception('不存在指定的驱动！无法启动！','DbFactory_ERROR');
        }
    }

}