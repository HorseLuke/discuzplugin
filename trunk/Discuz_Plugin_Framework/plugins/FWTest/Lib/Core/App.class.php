<?php
/**
 * @name Discuz! Plugin Framework Core App Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_DZ_PLUGIN_FW') && exit();

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
				$map['message']='ָ���Ŀ������ļ���û�ж�Ӧ�ķ������޷�������ܣ�';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);					
		}
	}
	
	/**
	 * ��url��ȡ�ÿ���������
	 *
	 * @return mix
	 */
	public function getControllerName(){
		if(defined('DEFAULT_CONTROLLER')){
			FWBase::setConfigValue('DEFAULT_CONTROLLER',DEFAULT_CONTROLLER);
			return DEFAULT_CONTROLLER;
		}else{
			$controller = FWBase::getRequestValue('c');
			if(empty($controller)){
				$map['message']='δָ�����������޷�������ܣ�';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);
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
			FWBase::setConfigValue('DEFAULT_ACTION',DEFAULT_ACTION);
			return DEFAULT_ACTION;
		}else{
			$action = FWBase::getRequestValue('a');
			if(empty($action)){
				$map['message']='δָ���������޷�������ܣ�';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);
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
				$map['message']='�޷��ҵ��������ļ����޷�������ܣ�';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);		
		}
		require($controllerFilePath);
		if(!class_exists($this->controller)){
				$map['message']='ָ���Ŀ������ļ���û�ж�Ӧ�Ŀ�ܣ��޷�������ܣ�';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);				
		}
		return new $$this->controller;
	}
	
}