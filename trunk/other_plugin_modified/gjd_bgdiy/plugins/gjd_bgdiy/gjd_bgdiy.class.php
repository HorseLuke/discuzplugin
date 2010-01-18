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
		$bgdiy_users_info = array();
		//获取这页帖子中的所有发帖用户uid
		$authorids=array();
		foreach((array)$postlist as $pid => $pinfo){ isset($pinfo['authorid']) && $authorids[] = (int)$pinfo['authorid']; }
		
		//根据$authorids，一次性获取这些用户的背景diy设置。
		if(!empty($authorids)){
			$authorids = array_unique( $authorids );
			$query = $db->query("SELECT * FROM {$tablepre}bgdiy_users WHERE uid  IN (".implode(',',$authorids).") and expiration>'$timestamp' and inuse='1' ");
			while($user = $db->fetch_array($query)){
				$user['bgstyle'] = unserialize($user['bgstyle']);
				if($user['position'] == 0){
					$bgdiy_users_info[$user['uid']]['query_con'] = $user;
				}elseif($user['position'] == 1){
					$bgdiy_users_info[$user['uid']]['query_mem'] = $user;
				}
			}
		}

		foreach((array)$postlist as $key => $val) {
			if( ($this->vars['bgfloor']==0 || $this->vars['bgfloor']>=$val['number'])  && !$val['anonymous'] ){
				$query_con = isset($bgdiy_users_info[$val['authorid']]['query_con']) ? $bgdiy_users_info[$val['authorid']]['query_con'] : null;
				$query_mem = isset($bgdiy_users_info[$val['authorid']]['query_mem']) ? $bgdiy_users_info[$val['authorid']]['query_mem'] : null;
				include $this->_template('bgdiy_viewthread_postbottom');
				$return[] = $html;	
			}else{
                $return[] = '';
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