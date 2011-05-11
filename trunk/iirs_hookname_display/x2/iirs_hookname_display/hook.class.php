<?php

/**
 * 检测并生成在一个Discuz X1.5RC页面能够运行指定钩子方法的钩子类名和写法
 * @author horseluke
 * @version $Id$
 *
 */
class plugin_iirs_hookname_display{

	var $plugin_id = '插件唯一标识';
	
	var $hscript = null;
	
	var $hscript_define = null;
	
	var $script = null;
	
	var $do = null;
	
	var $ac = null;
	
	/**
	 * 在footer那里输出提示
	 * @return string
	 */
	function global_footer(){
		$return = '';
		
		$this->hscript_define = CURSCRIPT;
		$this->hscript = (isset($GLOBALS['_G']['basescript']) ? (string)$GLOBALS['_G']['basescript'] : CURSCRIPT);
		$this->script = CURMODULE;
		
		if(isset($GLOBALS['_G']['gp_do']) && !empty($GLOBALS['_G']['gp_do'])){
			$this->do = $GLOBALS['_G']['gp_do'];
		}elseif(isset($_GET ['do']) && !empty($_GET ['do'])){
			$this->do = $_GET ['do'];
		}
		
		if(isset($GLOBALS['_G']['gp_ac']) && !empty($GLOBALS['_G']['gp_ac'])){
			$this->ac = $GLOBALS['_G']['gp_ac'];
		}elseif(isset($_GET ['ac']) && !empty($_GET ['ac'])){
			$this->ac = $_GET ['ac'];
		}		
		
		$hookscript_sub_method = $this->_create_hookscript_sub_method();
		
		$class_code = <<<EOF
//注意：普通版脚本中的类名以 plugin_ 开头。手机版脚本中的类名以 mobileplugin_ 开头
//全局嵌入点类
class plugin_{$this->plugin_id}{

	//所有模块执行前被调用
	function common(){
		//write some code
	}
	
	//discuzcode() 函数执行时调用。函数中 _G['discuzcodemessage'] 变量为解析的字串
	function discuzcode(\$param){
		//write some code
	}
	
	function global_xxx(){
		//write some code
	}
}

//脚本嵌入点类
class plugin_{$this->plugin_id}_{$this->hscript} extends plugin_{$this->plugin_id}{
	 function {$hookscript_sub_method}_xxxx(){
		//write some code
	}
	
	function {$hookscript_sub_method}_xxxx_output(\$param){
		//write some code
	}

	//showmessage() 执行时调用
	function {$hookscript_sub_method}_xxxx_message(\$param){
		//write some code
	}
	
	
}

EOF;

		//$class_code = nl2br($class_code);
		$class_code = htmlspecialchars($class_code);
		
		include template('iirs_hookname_display:global_footer');
		return $return;
	}
	
	function _create_hookscript_sub_method(){
		$script = $this->script;
		
		if ($this->hscript == 'home') {
			if ($this->script != 'spacecp') {
				$script = 'space_'. (!empty($this->do) ? $this->do : '');
			} else {
				$script .= !empty($this->ac) ? ('_'. $this->ac) : '';
			}
		}
		
		return $script;
	}
	
}
