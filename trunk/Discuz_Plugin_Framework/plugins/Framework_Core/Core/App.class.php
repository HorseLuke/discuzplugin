<?php
/**
 * @name Discuz! Plugin Framework Core App Class File UTF8
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_FW') && exit('ACCESS IS NOT ALLOWED!');

class App{
    
    public $controller;
    public $action;
    public $controllerInstance;
    
    /**
     * 插件程序运行的入口，在初始化完FWBase类后，必须通过此入口启动框架下的插件
     *
     */
    public function run(){
        
        $this->controller = $this->getControllerName().'Controller';
        $this->action = $this->getActionName().'Action';
        $this->controllerInstance = $this->initController();
        //运行控制器里面的方法
        if(method_exists($this->controllerInstance,$this->action)){
            $this->controllerInstance->{$this->action}();
        }else{
                FWBase::throw_exception('指定的控制器文件内没有对应的方法！无法启动框架！','FRAMEWORK_ERROR');
        }
    }
    
    /**
     * 从url中取得控制器名称
     *
     * @return mix
     */
    public function getControllerName(){
        if(defined('DEFAULT_CONTROLLER')){
            $controller = DEFAULT_CONTROLLER;
        }else{
            $controller = FWBase::getRequest('c');
            if(empty($controller)){
                $controller = FWBase::getConfig('DEFAULT_CONTROLLER');
            }
        }
        $controller = ucfirst($controller);
        FWBase::setConfig('DEFAULT_CONTROLLER',$controller);
        return $controller;
    }
    
    /**
     * 从url中取得方法名称
     *
     * @return string
     */
    public function getActionName(){
        if(defined('DEFAULT_ACTION')){
            $action = DEFAULT_ACTION;
        }else{
            $action = FWBase::getRequest('a');
            if(empty($action)){
                $action = FWBase::getConfig('DEFAULT_ACTION');
            }
        }
        $action = ucfirst($action);
        FWBase::setConfig('DEFAULT_ACTION',$action);
        return $action;
    }
    
    /**
     * 实例化控制器
     *
     * @return object
     */
    public function initController(){
        $controllerFilePath = APP_PATH."/Controller/{$this->controller}.class.php";
        if(!is_file($controllerFilePath)){
                FWBase::throw_exception("无法找到控制器文件！无法启动框架！",'FRAMEWORK_ERROR');        
        }

        require($controllerFilePath);
        if(!class_exists($this->controller)){
                FWBase::throw_exception('指定的控制器文件内没有对应的控制器！无法启动控制器！','FRAMEWORK_ERROR');                
        }
        $controller = $this->controller;
        return new $controller;
    }
    
}