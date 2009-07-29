<?php

/**
 * @name Discuz! Plugin Framework Core BaseController Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_FW') && exit('ACCESS IS NOT ALLOWED!');

class BaseController{
    
    protected $view = '';      //view实例
    protected $_importedModel = array('BaseModel');      //已经导入的model文件名称
    
    public function __construct(){
        $this->view=new BaseView();
    }
    
    public function createModel($name){
        if(!in_array($name,$this->_importedModel)){
            $modelPath = APP_PATH."/Model/{$name}Model.class.php";
            if(!is_file($modelPath)){
                $this->throw_exception("找不到指定的模型:{$name} ，无法继续运行。请返回。",'BASECONTROLLER_ERROR');
            }else{
                require($modelPath);
                $this->_importedModel[]=$name;
            }
        }
        return new $name;
    }
    
    public function throw_exception($message,$code='BASECONTROLLER_ERROR'){
        FWBase::throw_exception($message,$code);
    }
    
    public function showMessage($message, $url_forward = '', $extra = '', $forwardtype = 0){
        showmessage($message, $url_forward, $extra, $forwardtype);
    }
    
}