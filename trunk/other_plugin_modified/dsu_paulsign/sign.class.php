<?php
/*
	dsu_paulsign Echo By shy9000[DSU.CC] 2010-09-10
*/
class plugin_dsu_paulsign{
	function global_footer() {
		global $_G;
		if(function_exists('date_default_timezone_set'))@date_default_timezone_set("Asia/Shanghai");
		$tdtime = mktime(0,0,0,dgmdate($_G['timestamp'], 'n'),dgmdate($_G['timestamp'], 'j'),dgmdate($_G['timestamp'], 'Y'));
		$var = $_G['cache']['plugin']['dsu_paulsign'];
		$tzgroupid = unserialize($var['tzgroupid']);
		$read_ban = explode(",",$var['ban']);
		$groups = unserialize($var['groups']);
		if($var['ifopen'] && $var['ftopen'] && !defined('IN_dsu_paulsign') && !$_G['gp_infloat'] && !$_G['inajax'] && $_G['uid'] && in_array($_G['groupid'], $tzgroupid) && !in_array($_G['uid'],$read_ban) && in_array($_G['groupid'], $groups)) {
			$signtime = $_SESSION['signtime'];
			if(!$signtime){
				$qiandaodb = DB::fetch_first("SELECT time FROM ".DB::table('dsu_paulsign')." WHERE uid='$_G[uid]'");
				$htime = dgmdate($_G['timestamp'], 'H');
				if($qiandaodb){
					$_SESSION['signtime'] = $qiandaodb['time'];
					if($qiandaodb['time'] < $tdtime){
						if($var['timeopen']) {
							if(!($htime < $var['stime']) && !($htime > $var['ftime'])) dheader('Location: plugin.php?id=dsu_paulsign:sign');
						}else{
							dheader('Location: plugin.php?id=dsu_paulsign:sign');
						}
					}
				}else{
					$ttps = DB::fetch_first("SELECT posts FROM ".DB::table('common_member_count')." WHERE uid='$_G[uid]'");
					if($var['mintdpost'] <= $ttps['posts']){
						if($var['timeopen']) {
							if(!($htime < $var['stime']) && !($htime > $var['ftime'])) dheader('Location: plugin.php?id=dsu_paulsign:sign');
						}else{
							dheader('Location: plugin.php?id=dsu_paulsign:sign');
						}
					}
				}
			}else{
				if($signtime < $tdtime){
					if($var['timeopen']) {
						if(!($htime < $var['stime']) && !($htime > $var['ftime'])) dheader('Location: plugin.php?id=dsu_paulsign:sign');
					}else{
						dheader('Location: plugin.php?id=dsu_paulsign:sign');
					}
				}
			}
		}
		return '';
	}
}
class plugin_dsu_paulsign_home extends plugin_dsu_paulsign {
	function space_profile_baseinfo_bottom() {
		global $_G;
		if(function_exists('date_default_timezone_set'))@date_default_timezone_set("Asia/Shanghai");
		$tdtime = mktime(0,0,0,dgmdate($_G['timestamp'], 'n'),dgmdate($_G['timestamp'], 'j'),dgmdate($_G['timestamp'], 'Y'));
		$var = $_G['cache']['plugin']['dsu_paulsign'];
		if($var['spaceopen']){
			$creditnamecn = $_G['setting']['extcredits'][$var[nrcredit]]['title'];
			$nlvtext =str_replace(array("\r\n", "\n", "\r"), '/hhf/', $var['lvtext']);
			list($lv1name, $lv2name, $lv3name, $lv4name, $lv5name, $lv6name, $lv7name, $lv8name, $lv9name, $lv10name, $lvmastername) = explode("/hhf/", $nlvtext);
			$qiandaodb = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulsign')." WHERE uid='$_G[gp_uid]'");
			if($qiandaodb){
				$qtime = dgmdate($qiandaodb['time'], 'Y-m-d H:i');
				if ($qiandaodb['days'] >= '1500') {
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.Master]{$lvmastername}</b></font> .";
				} elseif ($qiandaodb['days'] >= '750') {
					$q['lvqd'] = 1500 - $qiandaodb['days'];
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.10]{$lv10name}".lang('plugin/dsu_paulsign','echo_12')."<font color=#FF0000><b>[LV.Master]{$lvmastername}</b></font>".lang('plugin/dsu_paulsign','echo_13')."<font color=#FF0000><b>{$q['lvqd']}</b></font>".lang('plugin/dsu_paulsign','echo_14');
				} elseif ($qiandaodb['days'] >= '365') {
					$q['lvqd'] = 750 - $qiandaodb['days'];
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.9]{$lv9name}</b></font>".lang('plugin/dsu_paulsign','echo_12')."<font color=#FF0000><b>[LV.10]{$lv10name}</b></font>".lang('plugin/dsu_paulsign','echo_13')."<font color=#FF0000><b>{$q['lvqd']}</b></font>".lang('plugin/dsu_paulsign','echo_14');
				} elseif ($qiandaodb['days'] >= '240') {
					$q['lvqd'] = 365 - $qiandaodb['days'];
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.8]{$lv10name}</b></font>".lang('plugin/dsu_paulsign','echo_12')."<font color=#FF0000><b>[LV.9]{$lv9name}</b></font>".lang('plugin/dsu_paulsign','echo_13')."<font color=#FF0000><b>{$q['lvqd']}</b></font>".lang('plugin/dsu_paulsign','echo_14');
				} elseif ($qiandaodb['days'] >= '120') {
					$q['lvqd'] = 240 - $qiandaodb['days'];
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.7]{$lv7name}</b></font>".lang('plugin/dsu_paulsign','echo_12')."<font color=#FF0000><b>[LV.8]{$lv8name}</b></font>".lang('plugin/dsu_paulsign','echo_13')."<font color=#FF0000><b>{$q['lvqd']}</b></font>".lang('plugin/dsu_paulsign','echo_14');
				} elseif ($qiandaodb['days'] >= '60') {
					$q['lvqd'] = 120 - $qiandaodb['days'];
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.6]{$lv6name}</b></font>".lang('plugin/dsu_paulsign','echo_12')."<font color=#FF0000><b>[LV.7]{$lv7name}</b></font>".lang('plugin/dsu_paulsign','echo_13')."<font color=#FF0000><b>{$q['lvqd']}</b></font>".lang('plugin/dsu_paulsign','echo_14');
				} elseif ($qiandaodb['days'] >= '30') {
					$q['lvqd'] = 60 - $qiandaodb['days'];
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.5]{$lv5name}</b></font>".lang('plugin/dsu_paulsign','echo_12')."<font color=#FF0000><b>[LV.6]{$lv6name}</b></font>".lang('plugin/dsu_paulsign','echo_13')."<font color=#FF0000><b>{$q['lvqd']}</b></font>".lang('plugin/dsu_paulsign','echo_14');
				} elseif ($qiandaodb['days'] >= '15') {
					$q['lvqd'] = 30 - $qiandaodb['days'];
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.4]{$lv4name}</b></font>".lang('plugin/dsu_paulsign','echo_12')."<font color=#FF0000><b>[LV.5]{$lv5name}</b></font>".lang('plugin/dsu_paulsign','echo_13')."<font color=#FF0000><b>{$q['lvqd']}</b></font>".lang('plugin/dsu_paulsign','echo_14');
				} elseif ($qiandaodb['days'] >= '7') {
					$q['lvqd'] = 15 - $qiandaodb['days'];
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.3]{$lv3name}</b></font>".lang('plugin/dsu_paulsign','echo_12')."<font color=#FF0000><b>[LV.4]{$lv4name}</b></font>".lang('plugin/dsu_paulsign','echo_13')."<font color=#FF0000><b>{$q['lvqd']}</b></font>".lang('plugin/dsu_paulsign','echo_14');
				} elseif ($qiandaodb['days'] >= '3') {
					$q['lvqd'] = 7 - $qiandaodb['days'];
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.2]{$lv2name}</b></font>".lang('plugin/dsu_paulsign','echo_12')."<font color=#FF0000><b>[LV.3]{$lv3name}</b></font>".lang('plugin/dsu_paulsign','echo_13')."<font color=#FF0000><b>{$q['lvqd']}</b></font>".lang('plugin/dsu_paulsign','echo_14');
				} elseif ($qiandaodb['days'] >= '1') {
					$q['lvqd'] = 3 - $qiandaodb['days'];
					$q['level'] = lang('plugin/dsu_paulsign','echo_11')."<font color=green><b>[LV.1]{$lv1name}</b></font>".lang('plugin/dsu_paulsign','echo_12')."<font color=#FF0000><b>[LV.2]{$lv2name}</b></font>".lang('plugin/dsu_paulsign','echo_13')."<font color=#FF0000><b>{$q['lvqd']}</b></font>".lang('plugin/dsu_paulsign','echo_14');
				}
				$q['if']= $qiandaodb['time']< $tdtime ? "<span class=gray>".lang('plugin/dsu_paulsign','echo_1')."</span>" : "<font color=green>".lang('plugin/dsu_paulsign','echo_2')."</font>";
				return "<div class='bm bbda cl'><h2>".lang('plugin/dsu_paulsign','echo_3')."</h2><p>".lang('plugin/dsu_paulsign','echo_4')." <b>{$qiandaodb['days']}</b> ".lang('plugin/dsu_paulsign','echo_5')."</p><p>".lang('plugin/dsu_paulsign','echo_17')." <b>{$qiandaodb['lasted']}</b> ".lang('plugin/dsu_paulsign','echo_5')."</p><p>".lang('plugin/dsu_paulsign','echo_6')." <b>{$qiandaodb['mdays']}</b> ".lang('plugin/dsu_paulsign','echo_5')."</p><p>".lang('plugin/dsu_paulsign','echo_7')." <font color=#ff00cc>{$qtime}</font></p><p>".lang('plugin/dsu_paulsign','echo_15')."{$creditnamecn} <font color=#ff00cc><b>{$qiandaodb['reward']}</b></font> {$_G[setting][extcredits][$var[nrcredit]]['unit']}".lang('plugin/dsu_paulsign','echo_16')."{$creditnamecn} <font color=#ff00cc><b>{$qiandaodb['lastreward']}</b></font> {$_G[setting][extcredits][$var[nrcredit]]['unit']}.</p><p>{$q['level']}</p><p>".lang('plugin/dsu_paulsign','echo_8')."{$q['if']}".lang('plugin/dsu_paulsign','echo_9')."</p></div>";
			}else{
				return "<div class='bm bbda cl'><h2>".lang('plugin/dsu_paulsign','echo_3')."</h2><p>".lang('plugin/dsu_paulsign','echo_10')."</p></div>";
			}
		}else{
			return "";
		}
	}
}
class plugin_dsu_paulsign_forum extends plugin_dsu_paulsign {
	function viewthread_postbottom_output(){
		global $_G,$postlist;
		if(function_exists('date_default_timezone_set'))@date_default_timezone_set("Asia/Shanghai");
		$authorid_pd = $postlist[$_G["forum_firstpid"]]["authorid"];
		$tdtime = mktime(0,0,0,dgmdate($_G['timestamp'], 'n'),dgmdate($_G['timestamp'], 'j'),dgmdate($_G['timestamp'], 'Y'));
		$lang['classn_03'] = lang('plugin/dsu_paulsign','classn_03');
		$lang['classn_04'] = lang('plugin/dsu_paulsign','classn_04');
		$lang['classn_05'] = lang('plugin/dsu_paulsign','classn_05');
		$lang['classn_06'] = lang('plugin/dsu_paulsign','classn_06');
		$lang['classn_07'] = lang('plugin/dsu_paulsign','classn_07');
		$lang['classn_08'] = lang('plugin/dsu_paulsign','classn_08');
		$lang['classn_09'] = lang('plugin/dsu_paulsign','classn_09');
		$lang['classn_10'] = lang('plugin/dsu_paulsign','classn_10');
		$open = $_G['cache']['plugin']['dsu_paulsign']['tidphopen'];   
		if($open){
			$qdtype = $_G['cache']['plugin']['dsu_paulsign']['qdtype'];
			if($qdtype == 2){
				$qdtidnumber = $_G['cache']['plugin']['dsu_paulsign']['tidnumber'];
			} elseif($qdtype == 3){
				$stats = DB::fetch_first("SELECT qdtidnumber FROM ".DB::table('dsu_paulsignset')." WHERE id='1'");
				$qdtidnumber = $stats['qdtidnumber'];
			}else{
				$qdtidnumber = 0;
			}
			if(($qdtidnumber == $_G['gp_tid']) && $authorid_pd){
				$pnum = $_G['cache']['plugin']['dsu_paulsign']['tidpnum'];
				$nrcredit = $_G['cache']['plugin']['dsu_paulsign']['nrcredit'];
				$nlvtext =str_replace(array("\r\n", "\n", "\r"), '/hhf/', $_G['cache']['plugin']['dsu_paulsign']['lvtext']);
				list($lv1name, $lv2name, $lv3name, $lv4name, $lv5name, $lv6name, $lv7name, $lv8name, $lv9name, $lv10name, $lvmastername) = explode("/hhf/", $nlvtext);
				$query = DB::query("SELECT q.days,q.time,q.uid,q.lastreward,m.username FROM ".DB::table('dsu_paulsign')." q, ".DB::table('common_member')." m WHERE q.uid=m.uid and q.time > {$tdtime} ORDER BY q.time LIMIT 0,{$pnum}");
				$mrcs = array();
				$i = 1;
				while($mrc = DB::fetch($query)) {
					$mrc['time'] = dgmdate($mrc['time'], 'Y-m-d H:i');
					if ($mrc['days'] >= '1500') {
			  			$mrc['level'] = "[LV.Master]{$lvmastername}";
					} elseif ($mrc['days'] >= '750') {
			  			$mrc['level'] = "[LV.10]{$lv10name}";
					} elseif ($mrc['days'] >= '365') {
			  			$mrc['level'] = "[LV.9]{$lv9name}";
					} elseif ($mrc['days'] >= '240') {
			  			$mrc['level'] = "[LV.8]{$lv10name}";
					} elseif ($mrc['days'] >= '120') {
			  			$mrc['level'] = "[LV.7]{$lv7name}";
					} elseif ($mrc['days'] >= '60') {
			  			$mrc['level'] = "[LV.6]{$lv6name}";
					} elseif ($mrc['days'] >= '30') {
			  			$mrc['level'] = "[LV.5]{$lv5name}";
					} elseif ($mrc['days'] >= '15') {
			  			$mrc['level'] = "[LV.4]{$lv4name}";
					} elseif ($mrc['days'] >= '7') {
			  			$mrc['level'] = "[LV.3]{$lv3name}";
					} elseif ($mrc['days'] >= '3') {
			  			$mrc['level'] = "[LV.2]{$lv2name}";
					} elseif ($mrc['days'] >= '1') {
			  			$mrc['level'] = "[LV.1]{$lv1name}";
					}
			 		$mrcs[$i++] = $mrc;
				}
				include template('dsu_paulsign:sign_list');
				return array(0=>$return);
			}else{
				return array();
			}	 
		}else{
		  return array();
		}
	}
	function viewthread_sidetop_output() {
		global $postlist,$_G;
		$open = $_G['cache']['plugin']['dsu_paulsign']['sidebarop'];
		$lastedop = $_G['cache']['plugin']['dsu_paulsign']['lastedop'];
		if(empty($_G['gp_tid']) || !is_array($postlist) || !$open) return array();
		$pids=array_keys($postlist);
		$authorids=array();
		foreach($postlist as $pid=>$pinfo){
			$authorids[]=$pinfo['authorid'];
		}
		$authorids = array_unique($authorids);
		$authorids = array_filter($authorids);
		$authorids = dimplode($authorids);
		if($authorids == '') return array();
		$uidlists = DB::query("SELECT uid,days,lasted,qdxq,time FROM ".DB::table('dsu_paulsign')." WHERE uid IN($authorids)");
		$days = array();
		$nlvtext =str_replace(array("\r\n", "\n", "\r"), '/hhf/', $_G['cache']['plugin']['dsu_paulsign']['lvtext']);
		list($lv1name, $lv2name, $lv3name, $lv4name, $lv5name, $lv6name, $lv7name, $lv8name, $lv9name, $lv10name, $lvmastername) = explode("/hhf/", $nlvtext);
		while($mrc = DB::fetch($uidlists)) {
			$days[$mrc['uid']]['days'] = $mrc['days'];
			$days[$mrc['uid']]['qdxq'] = $mrc['qdxq'];
			$days[$mrc['uid']]['time'] = dgmdate($mrc['time'], 'u');
			if ($lastedop) $days[$mrc['uid']]['lasted'] = $mrc['lasted'];
			if ($mrc['days'] >= '1500') {
				$days[$mrc['uid']]['level'] = "[LV.Master]{$lvmastername}";
			} elseif ($mrc['days'] >= '750') {
			  	$days[$mrc['uid']]['level'] = "[LV.10]{$lv10name}";
			} elseif ($mrc['days'] >= '365') {
			  	$days[$mrc['uid']]['level'] = "[LV.9]{$lv9name}";
			} elseif ($mrc['days'] >= '240') {
			  	$days[$mrc['uid']]['level'] = "[LV.8]{$lv10name}";
			} elseif ($mrc['days'] >= '120') {
			  	$days[$mrc['uid']]['level'] = "[LV.7]{$lv7name}";
			} elseif ($mrc['days'] >= '60') {
			  	$days[$mrc['uid']]['level'] = "[LV.6]{$lv6name}";
			} elseif ($mrc['days'] >= '30') {
			  	$days[$mrc['uid']]['level'] = "[LV.5]{$lv5name}";
			} elseif ($mrc['days'] >= '15') {
			  	$days[$mrc['uid']]['level'] = "[LV.4]{$lv4name}";
			} elseif ($mrc['days'] >= '7') {
			  	$days[$mrc['uid']]['level'] = "[LV.3]{$lv3name}";
			} elseif ($mrc['days'] >= '3') {
			  	$days[$mrc['uid']]['level'] = "[LV.2]{$lv2name}";
			} elseif ($mrc['days'] >= '1') {
			  	$days[$mrc['uid']]['level'] = "[LV.1]{$lv1name}";
			}
			if($mrc[qdxq] == 'kx'){
				$days[$mrc['uid']]['qdxqzw'] = lang('plugin/dsu_paulsign','mb_qb1');
			}elseif($mrc[qdxq] == 'ng') {
				$days[$mrc['uid']]['qdxqzw'] = lang('plugin/dsu_paulsign','mb_qb2');
			}elseif($mrc[qdxq] == 'ym') {
				$days[$mrc['uid']]['qdxqzw'] = lang('plugin/dsu_paulsign','mb_qb3');
			}elseif($mrc[qdxq] == 'wl') {
				$days[$mrc['uid']]['qdxqzw'] = lang('plugin/dsu_paulsign','mb_qb4');
			}elseif($mrc[qdxq] == 'nu') {
				$days[$mrc['uid']]['qdxqzw'] = lang('plugin/dsu_paulsign','mb_qb5');
			}elseif($mrc[qdxq] == 'ch') {
				$days[$mrc['uid']]['qdxqzw'] = lang('plugin/dsu_paulsign','mb_qb6');
			}elseif($mrc[qdxq] == 'fd') {
				$days[$mrc['uid']]['qdxqzw'] = lang('plugin/dsu_paulsign','mb_qb7');
			}elseif($mrc[qdxq] == 'yl') {
				$days[$mrc['uid']]['qdxqzw'] = lang('plugin/dsu_paulsign','mb_qb8');
			}elseif($mrc[qdxq] == 'shuai') {
				$days[$mrc['uid']]['qdxqzw'] = lang('plugin/dsu_paulsign','mb_qb9');
			}
			$days[] = $mrc;
		} 
		$echoq = array();  
		$firstcycle = 1;
		foreach($postlist as $key => $val) {
			if($days[$postlist[$key][authorid]][days]) {
				if ($lastedop){
					if($firstcycle == '1'){
						$echoq[] = '<style>
.qdsmile {padding:3px; margin-left:10px; margin-right:10px; list-style:none;}
.qdsmile li{padding:5px .4em;background:#F7FAFF;border:2px dashed #D1D8D8;}
.qdsmile li img{margin-bottom:5px;}
</style>
<div class="qdsmile"><li><center>'.lang('plugin/dsu_paulsign','ta_mind').'</center><table><tr><th><img src=source/plugin/dsu_paulsign/img/'.$days[$postlist[$key][authorid]][qdxq].'.gif><th><font size=5>'.$days[$postlist[$key][authorid]][qdxqzw].'</font><br>'.$days[$postlist[$key][authorid]][time].'</tr></table></li></div><p>'.lang('plugin/dsu_paulsign','classn_01').': '.$days[$postlist[$key][authorid]][days].' '.lang('plugin/dsu_paulsign','classn_02').'</p><p>'.lang('plugin/dsu_paulsign','classn_12').': '.$days[$postlist[$key][authorid]][lasted].' '.lang('plugin/dsu_paulsign','classn_02').'</p><p>'.$days[$postlist[$key][authorid]][level].'</p>';
					}else{
						$echoq[] = '<div class="qdsmile"><li><center>'.lang('plugin/dsu_paulsign','ta_mind').'</center><table><tr><th><img src=source/plugin/dsu_paulsign/img/'.$days[$postlist[$key][authorid]][qdxq].'.gif><th><font size=5>'.$days[$postlist[$key][authorid]][qdxqzw].'</font><br>'.$days[$postlist[$key][authorid]][time].'</tr></table></li></div><p>'.lang('plugin/dsu_paulsign','classn_01').': '.$days[$postlist[$key][authorid]][days].' '.lang('plugin/dsu_paulsign','classn_02').'</p><p>'.lang('plugin/dsu_paulsign','classn_12').': '.$days[$postlist[$key][authorid]][lasted].' '.lang('plugin/dsu_paulsign','classn_02').'</p><p>'.$days[$postlist[$key][authorid]][level].'</p>';
					}
				} else {
					if($firstcycle == '1'){
						$echoq[] = '<style>
.qdsmile {padding:3px; margin-left:10px; margin-right:10px; list-style:none;}
.qdsmile li{padding:5px .4em;background:#F7FAFF;border:2px dashed #D1D8D8;}
.qdsmile li img{margin-bottom:5px;}
</style>
<div class="qdsmile"><li><center>'.lang('plugin/dsu_paulsign','ta_mind').'</center><table><tr><th><img src=source/plugin/dsu_paulsign/img/'.$days[$postlist[$key][authorid]][qdxq].'.gif><th><font size=5>'.$days[$postlist[$key][authorid]][qdxqzw].'</font><br>'.$days[$postlist[$key][authorid]][time].'</tr></table></li></div><p>'.lang('plugin/dsu_paulsign','classn_01').': '.$days[$postlist[$key][authorid]][days].' '.lang('plugin/dsu_paulsign','classn_02').'</p><p>'.$days[$postlist[$key][authorid]][level].'</p>';
					}else{
						$echoq[] = '<div class="qdsmile"><li><center>'.lang('plugin/dsu_paulsign','ta_mind').'</center><table><tr><th><img src=source/plugin/dsu_paulsign/img/'.$days[$postlist[$key][authorid]][qdxq].'.gif><th><font size=5>'.$days[$postlist[$key][authorid]][qdxqzw].'</font><br>'.$days[$postlist[$key][authorid]][time].'</tr></table></li></div><p>'.lang('plugin/dsu_paulsign','classn_01').': '.$days[$postlist[$key][authorid]][days].' '.lang('plugin/dsu_paulsign','classn_02').'</p><p>'.$days[$postlist[$key][authorid]][level].'</p>';
					}
				}
			} else {
				if($firstcycle == '1'){
					$echoq[] = '<style>
.qdsmile {padding:3px; margin-left:10px; margin-right:10px; list-style:none;}
.qdsmile li{padding:5px .4em;background:#F7FAFF;border:2px dashed #D1D8D8;}
.qdsmile li img{margin-bottom:5px;}
</style>
<p>'.lang('plugin/dsu_paulsign','classn_11').'</p>';
				}else{
					$echoq[] = '<p>'.lang('plugin/dsu_paulsign','classn_11').'</p>';
				}
			}
			$firstcycle++;
		}  
		return $echoq;
	}
}
?>