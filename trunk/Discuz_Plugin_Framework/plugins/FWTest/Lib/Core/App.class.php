<?php
/**
 * @name Discuz! Plugin Framework Core App Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_FW') && exit();

class App{
    
    public $controller;
    public $action;
    public $controllerInstance;
    
    /**
     * ����������е���ڣ��ڳ�ʼ����FWBase��󣬱���ͨ���������������µĲ��
     *
     */
    public function run(){
        $this->controller = $this->getControllerName().'Controller';
        $this->action = $this->getActionName().'Action';
        $this->controllerInstance = $this->initController();
        //���п���������ķ���
        if(method_exists($this->controllerInstance,$this->action)){
            $this->controllerInstance->{$this->action}();
        }else{
                FWBase::throw_exception('ָ���Ŀ������ļ���û�ж�Ӧ�ķ������޷�������ܣ�','FRAMEWORK_ERROR');
        }
    }
    
    /**
     * ��url��ȡ�ÿ���������
     *
     * @return mix
     */
    public function getControllerName(){
        if(defined('DEFAULT_CONTROLLER')){
            FWBase::setConfig('DEFAULT_CONTROLLER',DEFAULT_CONTROLLER);
            return DEFAULT_CONTROLLER;
        }else{
            $controller = FWBase::getRequestValue('c');
            if(empty($controller)){
                FWBase::throw_exception('δָ�����������޷�������ܣ�','FRAMEWORK_ERROR');
            }
            return $controller;
        }
    }
    
    /**
     * ��url��ȡ�÷�������
     *
     * @return mix
     */
    public function getActionName(){
        if(defined('DEFAULT_ACTION')){
            FWBase::setConfig('DEFAULT_ACTION',DEFAULT_ACTION);
            return DEFAULT_ACTION;
        }else{
            $action = FWBase::getRequestValue('a');
            if(empty($action)){
                FWBase::throw_exception('δָ���������޷�������ܣ�','FRAMEWORK_ERROR');
            }
            return $action;
        }
    }
    
    /**
     * ʵ����������
     *
     * @return mix
     */
    public function initController(){
        $controllerFilePath = APP_PATH."/Controller/{$this->controller}.class.php";
        if(!is_file($controllerFilePath)){
                FWBase::throw_exception('�޷��ҵ��������ļ����޷�������ܣ�','FRAMEWORK_ERROR');        
        }
        require($controllerFilePath);
        if(!class_exists($this->controller)){
                FWBase::throw_exception('ָ���Ŀ������ļ���û�ж�Ӧ�Ŀ�ܣ��޷�������ܣ�','FRAMEWORK_ERROR');                
        }
        return new $$this->controller;
    }
    
}