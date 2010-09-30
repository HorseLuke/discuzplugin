<?php

/**
 * 检测并生成在一个Discuz X1.5RC页面能够运行指定钩子方法的钩子类名和写法
 * @author horseluke
 * @version $Id$
 *
 */
class plugin_iirs_hookname_display{

	var $plugin_id = '插件唯一标识';
	
	/**
	 * 在footer那里输出提示
	 * @return string
	 */
	function global_footer(){
		$return = '';
		
		$hook_class_name = htmlspecialchars( $this->_create_hook_class_name() );
		$plugin_id = $this->plugin_id;
		
		$basescript = htmlspecialchars((string)$GLOBALS['_G']['basescript']);
		$CURSCRIPT = CURSCRIPT;
		$CURMODULE = CURMODULE;
		$do = isset($_GET['do']) ? htmlspecialchars((string)$_GET['do']) : '';
		
		$class_code = <<<EOF
		
class plugin_{$plugin_id}{
	function global_xxx(){
		//write some code
	}
}

class {$hook_class_name} extends plugin_{$plugin_id}{
	 function {$CURMODULE}_xxxx[_output](){
		//write some code
	}
}

EOF;

		$class_code = nl2br($class_code);
		
		include template('iirs_hookname_display:global_footer');
		return $return;
	}
	
	
	function _create_hook_class_name(){
		$hook_class_name = 'plugin_'. $this->plugin_id  ;
		$hook_class_name .= '_'. (isset($GLOBALS['_G']['basescript']) ? (string)$GLOBALS['_G']['basescript'] : CURSCRIPT);
		if(isset($_GET['do'])){
			$hook_class_name .= '_'. (string)$_GET['do'];
		}
		return $hook_class_name;
	}
	
}
