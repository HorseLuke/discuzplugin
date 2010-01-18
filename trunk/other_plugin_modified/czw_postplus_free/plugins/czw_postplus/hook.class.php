<?php
!defined('IN_DISCUZ') && exit('Access Denied');

class plugin_czw_postplus {
	var $cvars=array();
	var $lang = array();
	var $fastreply=0;

	function plugin_czw_postplus(){
		global $templatelang,$adminid,$db,$tablepre;
		require DISCUZ_ROOT.'./forumdata/plugins/czw_postplus.lang.php';
		$this->lang = $scriptlang['czw_postplus'];
		include DISCUZ_ROOT.'./forumdata/cache/plugin_czw_postplus.php';
		$this->cvars=$_DPLUGIN['czw_postplus']['vars'];
		if($_GET['viewpid']) $this->fastreply=1;
		if($this->cvars['threadsmood'] && $this->cvars['threadsmood_set']){
			$threadsmoods=explode("\n",$this->cvars['threadsmood_set']);
			foreach($threadsmoods as $threadsmood){
				if($threadsmood){
					$threadsmood=explode('|',$threadsmood);
					$this->cvars['tmood'][$threadsmood[0]]=array(
						'id'=>$threadsmood[0],
						'name'=>$threadsmood[1],
						'img'=>$threadsmood[2],
						'credit'=>$threadsmood[3],
						'lzcredit'=>$threadsmood[4]
					);
				}
			}
		}else{
			$this->cvars['threadsmood']=0;
		}
	}

	function global_header() {
		global $_DCACHE,$_SERVER;
		$return='';
		if($this->cvars['delpostlink']){
			$whiteurllistarr=array();
			$whiteurllist='';
			foreach($whiteurllistarr as $id=>$value){
				$value=str_replace(array("\n","\r"), "", $value);
				$whiteurllist.='whiteurllist['.$id.'] = \''.$value.'\';'."\n";
			}
			$showmsg=$this->cvars['delpostlink_msg'];
			$showmsg=str_replace('{{bbname}}',$_DCACHE['settings']['bbname'],$showmsg);
			$showmsg=str_replace('{{linktourl}}','"+ linktourl +"',$showmsg);
			$showmsg = str_replace(array("\n","\r"), "", $showmsg);
			$siteurl=$_DCACHE[settings][siteurl];
			include $this->_templates('delpostlink_showmsg');
			$return.=$return0;
		}
		return $return;
	}

	function forumdisplay_modmarkread_output() {
		global $db,$tablepre,$_DCACHE,$threadlist,$dateformat,$timeformat;
		if($this->cvars[modmarkread]){
			foreach($threadlist as $id => $thread){
				$modmarkread=$db->result_first("select modmarkread from {$tablepre}czwpostplus_threads where tid='$thread[tid]'");
				if($modmarkread){
					$modmarkarr=explode("\n",$modmarkread);
					$modmarkread=$modmarkarr[count($modmarkarr)-1];
					$modmarkread=explode("\t",$modmarkread);
					$modmarkread[2]=gmdate("$dateformat $timeformat", $modmarkread[2] + $_DCACHE['settings']['timeoffset'] * 3600);
					$modmarkreadadd=$this->cvars['modmarkread_format'];
					$modmarkreadadd=str_replace('{{moduser}}',$modmarkread[1],$modmarkreadadd);
					$modmarkreadadd=str_replace('{{timestamp}}',$modmarkread[2],$modmarkreadadd);
					$modmarkreadadd=str_replace('{{floor}}',$modmarkread[0],$modmarkreadadd);
					$threadlist[$id]['subject'].="$modmarkreadadd";
				}
			}
		}
	}
	
	function viewthread_postheader_output() {
		global $db,$tablepre,$tid,$postlist,$page,$discuz_uid;
		if(empty($GLOBALS[postlist]) || empty($tid) || !is_array($postlist)) return;
		$returnarr=array();
		$pids=array_keys($postlist);
		if($this->cvars['fae']){
			$faelist=array();
			$myquery=$db->query("select pid,flower,egg from {$tablepre}czwpostplus_posts where pid in(".implodeids($pids).")");
			while($faeinfo=$db->fetch_array($myquery)){$faelist[$faeinfo['pid']]=$faeinfo;}
			foreach($pids as $floorid=>$pid){
				$faelist[$pid]['flower']=intval($faelist[$pid]['flower']);
				$faelist[$pid]['egg']=intval($faelist[$pid]['egg']);
				$post=$postlist[$pid];
				include $this->_templates('sendfae');
				$returnarr[$floorid].=$return;
			}
		}

		return $returnarr;
	}


	function viewthread_posttop_output() {
		global $db,$tablepre,$tid,$_DCACHE,$dateformat,$timeformat;
		$returnarr=array();
		if($this->cvars[modmarkread]){
			$modmarkarr=$db->result_first("select modmarkread from {$tablepre}czwpostplus_threads where tid='$tid'");
			if($modmarkarr){
				$modmarkarr=is_array($modmarkarr)?$modmarkarr:explode("\n",$modmarkarr);
				foreach($modmarkarr as $id=>$modmarkread){
					$modmarkread=is_array($modmarkread)?$modmarkread:explode("\t",$modmarkread);
					$modmarkread[2]=gmdate("$dateformat $timeformat", $modmarkread[2] + $_DCACHE['settings']['timeoffset'] * 3600);
					$modmarkarr[$id]=$modmarkread;
				}
				include $this->_templates('modmarkread_posttop');
				$returnarr[0].=$return;
			}
		}
		return $returnarr;
	}
	
	function viewthread_postfooter_output() {
		global $adminid,$db,$tablepre,$tid,$postlist,$discuz_uid;
		if(empty($postlist) || empty($tid) || !is_array($postlist)) return;
		$pids=array_keys($postlist);
		$returnarr=array();
		$admin=$adminid>0?1:0;
		if($adminid==3 && $admin){
			$query=$db->fetch_first("select a.uid from {$tablepre}moderators a,{$tablepre}threads b where a.uid='$discuz_uid' and a.fid=b.fid and b.tid='$tid'");
			if(!$query){$admin=0;}
		}
		if($this->cvars[modmarkread] && $admin){
			$modmarkread=$db->result_first("select modmarkread from {$tablepre}czwpostplus_threads where tid='$tid'");
			if(!$modmarkread){
				$modmarkread[0]=0;
			}else{
				$modmarkread=explode("\t",$modmarkread);
			}
			$modmarkreadfloor=max(intval($modmarkread[0]),0);
			$i=-1;
			foreach($postlist as $pid=>$post){
				$i++;
				$number=$post['number'];
				if($this->fastreply){
					$i++;
					$number=0;
				}
				if($number>$modmarkreadfloor || $this->fastreply) $returnarr[$i].="<a href='plugin.php?id=czw_postplus:main&do=modmarkread&tid=$tid&number=$number'>".$this->lang[hook_class_php_2]."</a>";
			}
		}
		return $returnarr;
	}
	
	function viewthread_sidetop_output() {
		global $postlist,$db,$tablepre,$tid;
		if(empty($postlist) || empty($tid) || !is_array($postlist)) return;
		$pids=array_keys($postlist);
		$returnarr=array();
		if($this->cvars['fae']){
			$authorids=array();
			foreach($postlist as $pid=>$pinfo){$authorids[]=$pinfo['authorid'];}
			$authorids=array_unique($authorids);
			$authorinfo=array();
			foreach($authorids as $authorid){
				$authorinfo[$authorid][flower]=$db->result_first("select sum(a.flower) from {$tablepre}czwpostplus_posts a,{$tablepre}posts b where a.pid=b.pid and b.authorid='$authorid'");
				$authorinfo[$authorid][egg]=$db->result_first("select sum(a.egg) from {$tablepre}czwpostplus_posts a,{$tablepre}posts b where a.pid=b.pid and b.authorid='$authorid'");
				$authorinfo[$authorid][authorid]=$authorid;
			}
			foreach($pids as $floorid=>$pid){
				if($this->fastreply) $floorid++;
				$uinfo=$authorinfo[$postlist[$pid][authorid]];
				$uinfo[flower]=intval($uinfo[flower]);
				$uinfo[egg]=intval($uinfo[egg]);
				include $this->_templates('userfae');
				$returnarr[$floorid]=$return;
			}
		}
		
		return $returnarr;
	}
	
	function viewthread_postbottom_output() {
		global $postlist,$db,$tablepre,$tid,$_DCACHE,$page,$discuz_uid;
		$returnarr=array();
		if(empty($tid)) return;
		if($this->cvars['threadsmood'] && $page==1){
			include $this->_templates('threadsmood_css');
			$returnarr[0].=$return0;
			$threadsmoodarray=array();
			$threadsmoodarray=$this->cvars['tmood'];
			$creditname=$_DCACHE['settings']['extcredits'][$this->cvars['threadsmood_credit']]['title'];
			$lastvote=$db->fetch_first("SELECT * FROM `{$tablepre}czwpostplus_threadsmood` WHERE `tid` =$tid AND ".($discuz_uid?"`uid`=$discuz_uid":"`ip`='$onlineip'"));
			include $this->_templates('threadsmood_vote');
			$returnarr[0].=$return;
		}
		return $returnarr;
	}
	
	function viewthread_output() {
		global $postlist,$thread;
		if(empty($postlist) || !is_array($postlist)) return;
		foreach($postlist as $id => $post){
			if($this->cvars['highlightlz'] && $postlist[$id]['authorid']==$thread['authorid'] && $postlist[$id]['first']==0) $postlist[$id]['author']='<font color=green>'.$postlist[$id]['author'].'</font>';
		}
	}

	function viewthread_delpostlink_output() {
		global $postlist;
		if(!$this->cvars['delpostlink'] || empty($postlist)) return;
		foreach($postlist as $id => $post){
				$postlist[$id][message]=str_replace(' href=',' onClick="checkurl(this.href);return false;" href=',$postlist[$id][message]);
		}
	}
	
	function viewthread_visitorread_output() {
		global $postlist,$discuz_uid;
		if(!$this->cvars['limitvistorread'] || $discuz_uid) return;
		foreach($postlist as $id => $post){
			$message=strip_tags($postlist[$id][message]);
			$message=$this->_getcnstrn($message,15);
			$message="<div style=\"background-color: #A3FF9D;padding: 10px;border: 1px solid #27FD02;\">{$this->cvars['limitvistorread_msg']}</div>".$message;
			$postlist[$id][message]=$message;
		}
	}
	
	function post_limit() {
		global $message,$_DCOOKIE,$action;
		if(!empty($message) && $this->cvars['limitsamepost'] && $action!='edit'){
			$postmd5=md5($message);
			if(isset($_DCOOKIE['czw_post_md5'])){
				if($_DCOOKIE['czw_post_md5']==$postmd5) showmessage($this->lang[hook_class_php_3]);
			}
			dsetcookie('czw_post_md5',$postmd5);
		}
	}
	

	
	function viewthread_fastpost_side() {
		//return '{$scriptlang[czw_postplus][hook_class_php_1]}';
	}
	
	function _getcnstrn($string,$length){
		global $charset;
		if(strlen($string)<=$length){
			return $string;
		}else{
			if($charset!='utf-8'){
				for($i=0;$i<$length;$i++){
					if(ord($string{$i})>127){
						$i++;
						if($i==$length){$length--;}
					}
				}
			}else{
				for($i=0;$i<$length;$i++){
					if(ord($string{$i})>=240){
						$length++;
						$i+=3;
						if($i==$length){$length-=3;}
					}elseif(ord($string{$i})>=224){
						$length++;
						$i+=2;
						if($i==$length){$length-=2;}
					}elseif(ord($string{$i})>=192){
						$i++;
						if($i==$length){$length--;}
					}
				}
			}
			return substr($string,0,$length);
		}
	}
	
	function _templates($template){
		return template($template,'czw_postplus','./plugins/czw_postplus/templates');
	}
	
	function _arraydel($delarr=array(),$arr=array()){
		$delarr=is_array($delarr)?$delarr:array($delarr);
		foreach($delarr as $delitem){
			if(in_array($delitem,$arr)){
				foreach($arr as $id=>$item){
					if($delitem==$item) unset($arr[$id]);
				}
			}
		}
		return $arr;
	}
}

?>