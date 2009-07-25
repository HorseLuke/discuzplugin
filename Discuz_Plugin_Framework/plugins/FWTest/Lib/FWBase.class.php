<?php

/**
 * @name Discuz! Plugin Framework Core Startup Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

// ����һ����������ʾ�Ѿ�����Discuz��д������
define('IN_DZ_PLUGIN_FW',TRUE);

// ����Discuz��д�����ܵ�·��
!defined('DZ_PLUGIN_FW_PATH') && define('DZ_PLUGIN_FW_PATH',dirname(__FILE__));

class FWBase{
	
	//��ܺ����ļ�����
    private static $_bootfiles = array(
									   'Db' => '/Core/Db.class.php',
									   'BaseModel' => '/Core/BaseModel.class.php',
									   'BaseController' => '/Core/BaseController.class.php',
									   'BaseView' => '/Core/BaseView.class.php',
									   'App' => '/Core/App.class.php');
    // ����ֵ������
	private static $_config = array();
	
	/**
	 * Discuz��д������������ڡ�������д�Ĳ�����ж�Ҫ��������������������
	 *
	 */
	
	public static function Startup(){
		//����discuz�����ļ���λ�ã���Ϊ��$_config���鴦��Ϊ�����Ĵ��ڶ��޷����壩
		FWBase::setConfigValue('CACHE_DIR', DISCUZ_ROOT.'forumdata/cache/~fwruntime.php');
		//�����ܺ����ļ�
		if(defined('DEBUG_MODE')){
			foreach (self::$_bootfiles as $filename => $filepath){
				require_once(DZ_PLUGIN_FW_PATH.$filepath);
			}
		}else{
			if(is_file(self::$_config['CACHE_DIR'].'/~fwruntime.php')){
				require (self::$_config['CACHE_DIR'].'/~fwruntime.php');
			}else{
				FWBase::buildCache();
			}
		}
		return true;
	}
	
	/**
	 * ���ɿ�ܺ����ļ�����
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
		file_put_contents(DISCUZ_ROOT.'forumdata/cache/~fwruntime.php','<?php \n\n'.$content);
    	unset($content);
    }
	
	/**
	 * ��ָ����ֵд�뵽����������
	 *
	 * @param mix $name ���õ����ƣ��������ַ�����Ҳ���������飻��Ϊ��������������$value
	 * @param mix $value Ҫ���õ����Ƶ�ֵ
	 */
	public static function setConfigValue($name,$value=''){
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
	  * ȡ�����������е�ֵ��
	  *
	  * @param string $name
	  */
	public static function getConfigValue($name){
		$name = strtoupper($name);
		if(!empty(self::$_config($name))){
			return self::$_config($name);
		}else{
			return null;
		}
	}
	
	/**
	 * ȡ�ñ���д��ܵİ汾
	 *
	 * @return array
	 */
	public static function getVersion(){
		return array('version'=>'0.0.1','build'=>20090724,'rev'=>1,'note'=>'For Discuz! 7');
	}

	
	/**
	 * �׳��쳣����dzϵͳ�У�����showmessage�����������Ӧ����ʾ��
	 *
	 * @param array $map ������Ϣͼ��������ʽ����ο�$mapDefault
	 */
	public static function throw_exception($map){
		$mapDefault = array('message' => 'Unknown Message!','type'=>'USER_EXCEPTION');
		$map = array_merge($mapDefault,$map);
		// ����dz��������׳��쳣�Ĳ���
		showmessage($map['message']);
	}
	
	
	public static function getRequestValue($name){
		return self::getGlobalsValue($name);
	}
	
	public static function getGlobalsValue($name){
		if(!empty($GLOBALS[$name])){
			return $GLOBALS[$name];
		}else{
			return null;
		}
	}
	
}

