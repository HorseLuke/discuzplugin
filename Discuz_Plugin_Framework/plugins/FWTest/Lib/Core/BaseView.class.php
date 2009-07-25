<?php

/**
 * @name Discuz! Plugin Framework Core BaseModel Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_DZ_PLUGIN_FW') && exit();

class BaseView{
	public $t_var = array();
	
	public function assign($name,$value=''){
		if(is_array($name)){
			foreach ($name as $k => $v){
				//$k = strval($k);
				$this->t_var[$k] = $v;
			}
		}else{
			//$name = strval($name);
		    $this->t_var[$name] = $value;
		}
	}
	
	public function display($tplFileName=''){
		$controller = FWBase::getConfigValue('DEFAULT_CONTROLLER').'Controller';
		if (empty($tplFileName)){
		    $tplFileName = FWBase::getConfigValue('DEFAULT_ACTION').'Action';
		}
		$tplFilePath = APP_PATH."/Tpl/{$controller}/{$tplFileName}.htm";
		if(!is_file($tplFilePath)){
				$map['message']='未找到模版！无法显示结果！';
				$map['type'] = 'FRAMEWORK_ERROR';
				FWBase::throw_exception($map);
		}

		foreach ($this->t_var as $name => $value){
			$$name = $value;
		}
		//ob_start();
		require ($tplFilePath);
		//ob_end_flush();
		//exit;
	}
}