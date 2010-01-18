<?php


class plugin_gjd_bgdiy {

	var $identifier = 'gjd_bgdiy';
	var $plugin, $directory;
	
	function plugin_gjd_bgdiy() {
		global $_DPLUGIN;
		
		include_once DISCUZ_ROOT . './forumdata/cache/plugin_gjd_bgdiy.php';
		$this->vars = $_DPLUGIN['gjd_bgdiy']['vars'];
		$this->directory = './plugins/'.$this->identifier;
	}	
	
	function _template($file) {
		return template($file, $this->identifier, $this->directory.'/templates');
	}
	
	function viewthread_postbottom_output() {
	    global $postlist, $db, $tablepre, $timestamp, $boardurl;
		
		$return = array();	
		foreach($postlist as $key => $val) {
			if($this->vars['bgfloor']==0||$this->vars['bgfloor']>=$val['number']){
				$query_con = $db->fetch_first("SELECT * FROM {$tablepre}bgdiy_users WHERE uid ='".$val['authorid']."' and expiration>'$timestamp' and inuse='1' and position='0'");
				$query_con['bgstyle'] = unserialize($query_con['bgstyle']);
				$query_mem = $db->fetch_first("SELECT * FROM {$tablepre}bgdiy_users WHERE uid ='".$val['authorid']."' and expiration>'$timestamp' and inuse='1' and position='1'");
				$query_mem['bgstyle'] = unserialize($query_mem['bgstyle']);
				include $this->_template('bgdiy_viewthread_postbottom');
				$return[] = $html;
			}
		}	
		return $return;
	}
	
	function viewthread_top_output() {
		include $this->_template('bgdiy_top');
		return $return;
	}

}

?>