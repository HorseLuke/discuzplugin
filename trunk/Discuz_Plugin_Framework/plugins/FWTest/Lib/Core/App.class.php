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
				$map['message']='指定的控制器文件内没有对应的方法！无法启动框架！';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);					
		}
	}
	
	/**
	 * 从url中取得控制器名称
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
				$map['message']='未指定控制器！无法启动框架！';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);
			}
			return $controller;
		}
	}
	
	/**
	 * 从url中取得方法名称
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
				$map['message']='未指定动作！无法启动框架！';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);
			}
			return $action;
		}
	}
	
	/**
	 * 实例化控制器
	 *
	 * @return mix
	 */
	public function initController(){
		$controllerFilePath = APP_PATH."/Controller/{$this->controller}.class.php";
		if(!is_file($controllerFilePath)){
				$map['message']='无法找到控制器文件！无法启动框架！';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);		
		}
		require($controllerFilePath);
		if(!class_exists($this->controller)){
				$map['message']='指定的控制器文件内没有对应的框架！无法启动框架！';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);				
		}
		return new $$this->controller;
	}
	
}