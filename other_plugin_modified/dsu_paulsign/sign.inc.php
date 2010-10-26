<?php
/*
	dsu_paulsign Main By shy9000[DSU.CC] 2010-09-10
*/
define('IN_dsu_paulsign', '1');
require_once DISCUZ_ROOT.'./data/plugindata/dsu_paulsign.lang.php';
if(function_exists('date_default_timezone_set'))@date_default_timezone_set("Asia/Shanghai");
$tdtime = mktime(0,0,0,dgmdate($_G['timestamp'], 'n'),dgmdate($_G['timestamp'], 'j'),dgmdate($_G['timestamp'], 'Y'));
$htime = dgmdate($_G['timestamp'], 'H');
$var = $_G['cache']['plugin']['dsu_paulsign'];
$lang = $scriptlang['dsu_paulsign'];
$nlvtext =str_replace(array("\r\n", "\n", "\r"), '/hhf/', $var['lvtext']);
$nfastreplytext =str_replace(array("\r\n", "\n", "\r"), '/hhf/', $var['fastreplytext']);
$njlmain =str_replace(array("\r\n", "\n", "\r"), '/hhf/', $var['jlmain']);
list($lv1name, $lv2name, $lv3name, $lv4name, $lv5name, $lv6name, $lv7name, $lv8name, $lv9name, $lv10name, $lvmastername) = explode("/hhf/", $nlvtext);
list($var['fastreply1'], $var['fastreply2'], $var['fastreply3'], $var['fastreply4'], $var['fastreply5'], $var['fastreply6'], $var['fastreply7'], $var['fastreply8']) = explode("/hhf/", $nfastreplytext);
list($var['jlmain1'], $var['jlmain2'], $var['jlmain3'], $var['jlmain4'], $var['jlmain5'], $var['jlmain6'], $var['jlmain7'], $var['jlmain8'], $var['jlmain9'], $var['jlmain10']) = explode("/hhf/", $njlmain);
$jlxgroups = unserialize($var['jlxgroups']);
$groups = unserialize($var['groups']);
$plgroups = unserialize($var['plgroups']);
$plgroups2 = unserialize($var['plgroups']);
$plgroups = dimplode($plgroups);
$credit = mt_rand($var['mincredit'],$var['maxcredit']);
$read_ban = explode(",",$var['ban']);
$post = DB::fetch_first("SELECT posts FROM ".DB::table('common_member_count')." WHERE uid='$_G[uid]'");
$qiandaodb = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulsign')." WHERE uid='$_G[uid]'");
$stats = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulsignset')." WHERE id='1'");
$qddb = DB::fetch_first("SELECT time FROM ".DB::table('dsu_paulsign')." ORDER BY time DESC limit 0,1");
$lastmonth=dgmdate($qddb['time'], 'm');
$nowmonth=dgmdate($_G['timestamp'], 'm');
if($nowmonth!=$lastmonth){
	DB::query("UPDATE ".DB::table('dsu_paulsign')." SET mdays=0 WHERE uid");
}
function sign_msg($msg, $treferer = '') {
	global $_G;
	include template('dsu_paulsign:float');
	dexit();
}
if(empty($_G['uid'])) showmessage('to_login', 'member.php?mod=logging&action=login', array(), array('showmsg' => true, 'login' => 1));
if(!$var['ifopen'] && $_G['adminid'] != 1) showmessage($var['plug_clsmsg'], 'index.php');
if($var['plopen'] && $plgroups) {
	$query = DB::query("SELECT groupid, grouptitle FROM ".DB::table('common_usergroup')." WHERE groupid IN ($plgroups)");
	$mccs = array();
	while($mcc = DB::fetch($query)){
		$mccs[] = $mcc;
	}
}
if($_G['gp_operation'] == 'zong' || $_G['gp_operation'] == 'month' || $_G['gp_operation'] == '' || ($_G['gp_operation'] == 'zdyhz' && $var['plopen']) || ($_G['gp_operation'] == 'rewardlist' && $var['rewardlistopen'])) {
	if($_G['gp_operation'] == 'month'){
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulsign')." WHERE mdays != 0");
		$page = max(1, intval($_G['gp_page']));
		$start_limit = ($page - 1) * 10;
		$multipage = multi($num, 10, $page, "plugin.php?id=dsu_paulsign:sign&operation={$_G[gp_operation]}");
	} elseif($_G['gp_operation'] == 'zdyhz' || $_G['gp_operation'] == 'rewardlist'){
	} elseif($_G['gp_operation'] == '' && $var['qddesc']){
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulsign')." WHERE time >= {$tdtime}");
		$page = max(1, intval($_G['gp_page']));
		$start_limit = ($page - 1) * 10;
		$multipage = multi($num, 10, $page, "plugin.php?id=dsu_paulsign:sign&operation={$_G[gp_operation]}");
	} else {
		$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulsign')."");
		$page = max(1, intval($_G['gp_page']));
		$start_limit = ($page - 1) * 10;
		$multipage = multi($num, 10, $page, "plugin.php?id=dsu_paulsign:sign&operation={$_G[gp_operation]}");
	}
	if($_G['gp_operation'] == 'zong'){
		$sql = "SELECT q.days,q.mdays,q.time,q.qdxq,q.uid,q.todaysay,q.lastreward,m.username FROM ".DB::table('dsu_paulsign')." q, ".DB::table('common_member')." m WHERE q.uid=m.uid ORDER BY q.days desc LIMIT $start_limit, 10";
	} elseif ($_G['gp_operation'] == 'month') {
		$sql = "SELECT q.days,q.mdays,q.time,q.qdxq,q.uid,q.todaysay,q.lastreward,m.username FROM ".DB::table('dsu_paulsign')." q, ".DB::table('common_member')." m WHERE q.uid=m.uid AND q.mdays != 0 ORDER BY q.mdays desc LIMIT $start_limit, 10";
	} elseif($_G['gp_operation'] == 'zdyhz'){
		if(in_array($_G['gp_qdgroupid'], $plgroups2)) {
			$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulsign')." q, ".DB::table('common_member')." m WHERE q.uid=m.uid AND m.groupid IN($_G[gp_qdgroupid])");
			$page = max(1, intval($_G['gp_page']));
			$start_limit = ($page - 1) * 10;
			$multipage = multi($num, 10, $page, "plugin.php?id=dsu_paulsign:sign&operation={$_G[gp_operation]}", 0);
			$sql = "SELECT q.days,q.mdays,q.time,q.qdxq,q.uid,q.todaysay,q.lastreward,m.username FROM ".DB::table('dsu_paulsign')." q, ".DB::table('common_member')." m WHERE q.uid=m.uid AND m.groupid IN($_G[gp_qdgroupid]) ORDER BY q.time desc LIMIT $start_limit, 10";
		} else {
			$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulsign')." q, ".DB::table('common_member')." m WHERE q.uid=m.uid AND m.groupid IN($plgroups)");
			$page = max(1, intval($_G['gp_page']));
			$start_limit = ($page - 1) * 10;
			$multipage = multi($num, 10, $page, "plugin.php?id=dsu_paulsign:sign&operation={$_G[gp_operation]}", 0);
			$sql = "SELECT q.days,q.mdays,q.time,q.qdxq,q.uid,q.todaysay,q.lastreward,m.username FROM ".DB::table('dsu_paulsign')." q, ".DB::table('common_member')." m WHERE q.uid=m.uid AND m.groupid IN($plgroups) ORDER BY q.time desc LIMIT $start_limit, 10";
		}
	} elseif ($var['rewardlistopen'] && $_G['gp_operation'] == 'rewardlist') {
		$sql = "SELECT q.days,q.mdays,q.time,q.qdxq,q.uid,q.todaysay,q.lastreward,q.reward,m.username FROM ".DB::table('dsu_paulsign')." q, ".DB::table('common_member')." m WHERE q.uid=m.uid ORDER BY q.reward desc LIMIT 0, 10";
	} elseif ($_G['gp_operation'] == '') {
		if($var['qddesc']) {
			$sql = "SELECT q.days,q.mdays,q.time,q.qdxq,q.uid,q.todaysay,q.lastreward,m.username FROM ".DB::table('dsu_paulsign')." q, ".DB::table('common_member')." m WHERE q.uid=m.uid and q.time >= {$tdtime} ORDER BY q.time LIMIT $start_limit, 10";
		} else {
			$sql = "SELECT q.days,q.mdays,q.time,q.qdxq,q.uid,q.todaysay,q.lastreward,m.username FROM ".DB::table('dsu_paulsign')." q, ".DB::table('common_member')." m WHERE q.uid=m.uid ORDER BY q.time desc LIMIT $start_limit, 10";
		}
	}
	$query = DB::query($sql);
	$mrcs = array();
	while($mrc = DB::fetch($query)) {
		$mrc['if']= $mrc['time']<$tdtime ? "<span class=gray>".$lang['tdno']."</span>" : "<font color=green>".$lang['tdyq']."</font>";
		$mrc['time'] = dgmdate($mrc['time'], 'Y-m-d H:i');
		!$qd['qdxq'] && $qd['qdxq']='kx';
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
		$mrcs[] = $mrc;
	}
} elseif($_G['gp_operation'] == 'ban') {
	if($_G['adminid'] == 1) {
		DB::query("UPDATE ".DB::table('dsu_paulsign')." SET todaysay='{$lang['ban_01']}' WHERE uid='$_G[gp_banuid]'");
		showmessage("{$lang['ban_02']}", dreferer());
	} else {
		showmessage("{$lang['ban_03']}", dreferer());
	}
} elseif($_G['gp_operation'] == 'qiandao') {
	if($_G['gp_formhash'] != FORMHASH) {
		showmessage('undefined_action', NULL);
	}
	if($var['timeopen']) {
		if ($htime < $var['stime']) {
			sign_msg("{$lang['ts_timeearly1']}{$var[stime]}{$lang['ts_timeearly2']}");
		} elseif ($htime > $var['ftime']) {
			sign_msg($lang['ts_timeov']);
		}
	}
	if(!in_array($_G['groupid'], $groups)) sign_msg($lang['ts_notallow']);
	if($var['mintdpost'] > $post['posts']) sign_msg("{$lang['ts_minpost1']}{$var[mintdpost]}{$lang['ts_minpost2']}");
	if(in_array($_G['uid'],$read_ban)) sign_msg($lang['ts_black']);
	if($qiandaodb['time']>$tdtime) sign_msg($lang['ts_yq']);
	$qdxqs=array('kx','ym','ng','wl','nu','ch','fd','shuai','yl');
	if(!in_array($_G['gp_qdxq'],$qdxqs)) sign_msg($lang['ts_xqnr']);
	if(!$_G['gp_qdxq']) sign_msg($lang['ts_noxq']);
	if($_G['gp_qdmode']=='1'){
		$todaysay = dhtmlspecialchars($_G['gp_todaysay']);
		if($todaysay=='') sign_msg($lang['ts_nots']);
		if(strlen($todaysay) > 100) sign_msg($lang['ts_ovts']);
		if(strlen($todaysay) < 6) sign_msg($lang['ts_syts']);
		if (!preg_match("/[^A-Za-z0-9.,]/",$todaysay)) sign_msg($lang['ts_saywater']);
	} elseif ($_G['gp_qdmode']=='2') {
		switch ($_G['gp_fastreply']){
			case 1:
				$todaysay = "{$var['fastreply1']}";
			break;
			case 2:
				$todaysay = "{$var['fastreply2']}";
			break;
			case 3:
				$todaysay = "{$var['fastreply3']}";
			break;
			case 4:
				$todaysay = "{$var['fastreply4']}";
			break;
			case 5:
				$todaysay = "{$var['fastreply5']}";
			break; 
			case 6:
				$todaysay = "{$var['fastreply6']}";
			break; 
			case 7:
				$todaysay = "{$var['fastreply7']}";
			break; 
			case 8:
				$todaysay = "{$var['fastreply8']}";
			break; 
			default:
				$todaysay = "{$var['fastreply1']}";
		}
	} elseif ($_G['gp_qdmode']=='3') {
		$todaysay = "{$lang['wttodaysay']}";
	}
	if(in_array($_G['groupid'], $jlxgroups) && $var['jlx'] != '0') {
		$credit = $credit * $var['jlx'];
	}
	$qiandaodb['lasted'] = $qiandaodb['lasted'] ? $qiandaodb['lasted'] : 0;
	if((86400 > ($tdtime - $qiandaodb['time'])) && $var['lastedop'] && $qiandaodb['lasted'] != '0'){
		$randlastednum = mt_rand($var[lastednuml],$var[lastednumh]);
		$randlastednum = sprintf("%03d", $randlastednum);
		$randlastednum = '0.'.$randlastednum;
		$randlastednum = $randlastednum * $qiandaodb['lasted']; 
		$credit = round($credit*(1+$randlastednum));
	}
	$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulsign')." WHERE time >= {$tdtime} ");
	if(!$qiandaodb['uid']) {
		DB::query("INSERT INTO ".DB::table('dsu_paulsign')." (uid,time) VALUES ('$_G[uid]',$_G[timestamp])");
	}
	if((86400 > ($tdtime - $qiandaodb['time'])) && $var['lastedop']){
		DB::query("UPDATE ".DB::table('dsu_paulsign')." SET days=days+1,mdays=mdays+1,time='$_G[timestamp]',qdxq='$_G[gp_qdxq]',todaysay='$todaysay',reward=reward+{$credit},lastreward='$credit',lasted=lasted+1 WHERE uid='$_G[uid]'");
	} elseif((86400 < ($tdtime - $qiandaodb['time'])) && $var['lastedop']){
		DB::query("UPDATE ".DB::table('dsu_paulsign')." SET days=days+1,mdays=mdays+1,time='$_G[timestamp]',qdxq='$_G[gp_qdxq]',todaysay='$todaysay',reward=reward+{$credit},lastreward='$credit',lasted='1' WHERE uid='$_G[uid]'");
	} else {
		DB::query("UPDATE ".DB::table('dsu_paulsign')." SET days=days+1,mdays=mdays+1,time='$_G[timestamp]',qdxq='$_G[gp_qdxq]',todaysay='$todaysay',reward=reward+{$credit},lastreward='$credit',lasted='0' WHERE uid='$_G[uid]'");
	}
	updatemembercount($_G['uid'], array($var['nrcredit'] => $credit));
	if(file_exists(DISCUZ_ROOT.'./source/plugin/dsu_kkvip/vip.func.php')){
		include_once DISCUZ_ROOT.'./source/plugin/dsu_kkvip/vip.func.php';
		$jlnum = $num + 1;
		sign_vip($jlnum);
	}
	if($num >=0 && $num <=9 ) {
		switch ($num){
			case 0:
				list($exacr,$exacz) = explode("|", $var['jlmain1']);
			break;
			case 1:
				list($exacr,$exacz) = explode("|", $var['jlmain2']);
			break;
			case 2:
				list($exacr,$exacz) = explode("|", $var['jlmain3']);
			break;
			case 3:
				list($exacr,$exacz) = explode("|", $var['jlmain4']);
			break;
			case 4:
				list($exacr,$exacz) = explode("|", $var['jlmain5']);
			break; 
			case 5:
				list($exacr,$exacz) = explode("|", $var['jlmain6']);
			break; 
			case 6:
				list($exacr,$exacz) = explode("|", $var['jlmain7']);
			break; 
			case 7:
				list($exacr,$exacz) = explode("|", $var['jlmain8']);
			break;
			case 8:
				list($exacr,$exacz) = explode("|", $var['jlmain9']);
			break;
			case 9:
				list($exacr,$exacz) = explode("|", $var['jlmain10']);
			break;	
		}
		$psc = $num+1;
		if($exacr && $exacz) updatemembercount($_G['uid'], array($exacr => $exacz));
	}
		if($var['qdtype'] == '2') {
			$thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid='$var[tidnumber]'");
			$hft = dgmdate($_G['timestamp'], 'Y-m-d H:i');
			if($num >=0 && $num <=9 && $exacr && $exacz) {
				$message = "[quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_02]}[color=red]{$lang[tsn_03]}[/color][color=darkorange]{$lang[tsn_04]}{$psc}{$lang[tsn_05]}[/color]{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit}[/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}[/color][color=gray]{$lang[tsn_17]}[/color] [color=gray]{$_G[setting][extcredits][$exacr][title]} [/color][color=darkorange]{$exacz}[/color][color=gray]{$_G[setting][extcredits][$exacr][unit]}[/color][/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
			} else {
				$message = "[quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_09]}{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}[/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
			}
			require_once libfile('function/post');
			$pid = insertpost(array('fid' => $thread['fid'],'tid' => $var['tidnumber'],'first' => '0','author' => $_G['username'],'authorid' => $_G['uid'],'subject' => '','dateline' => $_G['timestamp'],'message' => $message,'useip' => $_G['clientip'],'invisible' => '0','anonymous' => '0','usesig' => '0','htmlon' => '0','bbcodeoff' => '0','smileyoff' => '0','parseurloff' => '0','attachment' => '0',));
			DB::query("UPDATE ".DB::table('forum_thread')." SET lastposter='$_G[username]', lastpost='$_G[timestamp]', replies=replies+1 WHERE tid='$var[tidnumber]' AND fid='$thread[fid]'", 'UNBUFFERED');
			updatepostcredits('+', $_G['uid'], 'reply', $thread['fid']);
			$lastpost = "$thread[tid]\t".addslashes($thread['subject'])."\t$_G[timestamp]\t$_G[username]";
			DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost', posts=posts+1, todayposts=todayposts+1 WHERE fid='$thread[fid]'", 'UNBUFFERED');
			$tidnumber = $var['tidnumber'];
		} elseif($var['qdtype'] == '3') {
			if($num=='0' || ($stats['qdtidnumber'] == '0')) {
				$fqdtime = dgmdate($_G['timestamp'], "m{$lang[month]}d{$lang[day]}");
				$subject = "{$fqdtime} {$lang[ftids]}";
				$hft = dgmdate($_G['timestamp'], 'Y-m-d H:i');
				if($exacr && $exacz) {
					$message = "[quote][size=2][color=dimgray]{$lang[tsn_10]}[/color][url={$_G[siteurl]}plugin.php?id=dsu_paulsign:sign][color=darkorange]{$lang[tsn_11]}[/color][/url][color=dimgray]{$lang[tsn_12]}[/color][/size][/quote][quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_02]}[color=red]{$lang[tsn_03]}[/color][color=darkorange]{$lang[tsn_04]}{$lang[tsn_13]}{$lang[tsn_05]}[/color]{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit}[/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}[/color][color=gray]{$lang[tsn_17]}[/color] [color=gray]{$_G[setting][extcredits][$exacr][title]} [/color][color=darkorange]{$exacz}[/color][color=gray]{$_G[setting][extcredits][$exacr][unit]}[/color][/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
				} else {
					$message = "[quote][size=2][color=dimgray]{$lang[tsn_10]}[/color][url={$_G[siteurl]}plugin.php?id=dsu_paulsign:sign][color=darkorange]{$lang[tsn_11]}[/color][/url][color=dimgray]{$lang[tsn_12]}[/color][/size][/quote][quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_02]}[color=red]{$lang[tsn_03]}[/color][color=darkorange]{$lang[tsn_04]}{$lang[tsn_13]}{$lang[tsn_05]}[/color]{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit}[/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}.[/color][/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
				}
				DB::query("INSERT INTO ".DB::table('forum_thread')." (fid, posttableid, readperm, price, typeid, sortid, author, authorid, subject, dateline, lastpost, lastposter, displayorder, digest, special, attachment, moderated, highlight, closed, status, isgroup) VALUES ('$var[fidnumber]', '0', '0', '0', '$var[qdtypeid]', '0', '$_G[username]', '$_G[uid]', '$subject', '$_G[timestamp]', '$_G[timestamp]', '$_G[username]', '0', '0', '0', '0', '1', '1', '1', '0', '0')");
				$tid = DB::insert_id();
				DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET qdtidnumber = '$tid' WHERE id='1'");
				require_once libfile('function/post');
				$pid = insertpost(array('fid' => $var['fidnumber'],'tid' => $tid,'first' => '1','author' => $_G['username'],'authorid' => $_G['uid'],'subject' => $subject,'dateline' => $_G['timestamp'],'message' => $message,'useip' => $_G['clientip'],'invisible' => '0','anonymous' => '0','usesig' => '0','htmlon' => '0','bbcodeoff' => '0','smileyoff' => '0','parseurloff' => '0','attachment' => '0',));
				$expiration = $_G['timestamp'] + 86400;
				DB::query("INSERT INTO ".DB::table('forum_thread')."mod (tid, uid, username, dateline, action, expiration, status) VALUES ('$tid', '$_G[uid]', '$_G[username]', '$_G[timestamp]', 'EHL', '$expiration', '1')");
				DB::query("INSERT INTO ".DB::table('forum_thread')."mod (tid, uid, username, dateline, action, expiration, status) VALUES ('$tid', '$_G[uid]', '$_G[username]', '$_G[timestamp]', 'CLS', '0', '1')");
				updatepostcredits('+', $_G['uid'], 'post', $var['fidnumber']);
				$lastpost = "$tid\t".addslashes($subject)."\t$_G[timestamp]\t$_G[username]";
				DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost', threads=threads+1, posts=posts+1, todayposts=todayposts+1 WHERE fid='$var[fidnumber]'", 'UNBUFFERED');
				$tidnumber = $tid;
			} else {
				$tidnumber = $stats['qdtidnumber'];
				$thread = DB::fetch_first("SELECT subject FROM ".DB::table('forum_thread')." WHERE tid='$tidnumber'");
				$hft = dgmdate($_G['timestamp'], 'Y-m-d H:i');
				if($num >=1 && $num <=9 && $exacr && $exacz) {
					$message = "[quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_02]}[color=red]{$lang[tsn_03]}[/color][color=darkorange]{$lang[tsn_04]}{$psc}{$lang[tsn_05]}[/color]{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit}[/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}[/color][color=gray]{$lang[tsn_17]}[/color] [color=gray]{$_G[setting][extcredits][$exacr][title]} [/color][color=darkorange]{$exacz}[/color][color=gray]{$_G[setting][extcredits][$exacr][unit]}[/color][/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
				} else {
					$message = "[quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_09]}{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}[/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
				}
				require_once libfile('function/post');
				$pid = insertpost(array('fid' => $var['fidnumber'],'tid' => $tidnumber,'first' => '0','author' => $_G['username'],'authorid' => $_G['uid'],'subject' => '','dateline' => $_G['timestamp'],'message' => $message,'useip' => $_G['clientip'],'invisible' => '0','anonymous' => '0','usesig' => '0','htmlon' => '0','bbcodeoff' => '0','smileyoff' => '0','parseurloff' => '0','attachment' => '0',));
				DB::query("UPDATE ".DB::table('forum_thread')." SET lastposter='$_G[username]', lastpost='$_G[timestamp]', replies=replies+1 WHERE tid='$tidnumber' AND fid='$var[fidnumber]'", 'UNBUFFERED');
				updatepostcredits('+', $_G['uid'], 'reply', $var['fidnumber']);
				$lastpost = "$tidnumber\t".addslashes($thread['subject'])."\t$_G[timestamp]\t$_G[username]";
				DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost', posts=posts+1, todayposts=todayposts+1 WHERE fid='$var[fidnumber]'", 'UNBUFFERED');
			}
		}
	$_SESSION['signtime'] = $_G['timestamp'];
	if($num ==0) {
		if($stats['todayq'] > $stats['highestq']) DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET highestq='$stats[todayq]' WHERE id='1'");
		DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET yesterdayq='$stats[todayq]',todayq=1 WHERE id='1'");
	} else {
		DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET todayq=todayq+1 WHERE id='1'");
	}
	if($var['tzopen']) {
		if($exacr && $exacz) {
			sign_msg("{$lang[tsn_14]}{$lang[tsn_03]}{$lang[tsn_04]}{$psc}{$lang[tsn_15]}{$lang[tsn_06]} {$_G[setting][extcredits][$var[nrcredit]][title]} {$credit} {$_G[setting][extcredits][$var[nrcredit]][unit]} {$lang[tsn_16]} {$_G[setting][extcredits][$exacr][title]} {$exacz} {$_G[setting][extcredits][$exacr][unit]}","forum.php?mod=redirect&tid={$tidnumber}&goto=lastpost#lastpost");
		} else {
			sign_msg("{$lang[tsn_18]} {$_G[setting][extcredits][$var[nrcredit]][title]} {$credit} {$_G[setting][extcredits][$var[nrcredit]][unit]}","forum.php?mod=redirect&tid={$tidnumber}&goto=lastpost#lastpost");
		}
	} else {
		if($exacr && $exacz) {
			sign_msg("{$lang[tsn_14]}{$lang[tsn_03]}{$lang[tsn_04]}{$psc}{$lang[tsn_15]}{$lang[tsn_06]} {$_G[setting][extcredits][$var[nrcredit]][title]} {$credit} {$_G[setting][extcredits][$var[nrcredit]][unit]} {$lang[tsn_16]} {$_G[setting][extcredits][$exacr][title]} {$exacz} {$_G[setting][extcredits][$exacr][unit]}","plugin.php?id=dsu_paulsign:sign");
		} else {
			sign_msg("{$lang[tsn_18]} {$_G[setting][extcredits][$var[nrcredit]][title]} {$credit} {$_G[setting][extcredits][$var[nrcredit]][unit]}","plugin.php?id=dsu_paulsign:sign");
		}
	}
}
if ($qiandaodb['days'] >= '1500') {
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.Master]{$lvmastername}</b></font> .";
} elseif ($qiandaodb['days'] >= '750') {
	$q['lvqd'] = 1500 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.10]{$lv10name}{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.Master]{$lvmastername}</b></font> .";
} elseif ($qiandaodb['days'] >= '365') {
	$q['lvqd'] = 750 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.9]{$lv9name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.10]{$lv10name}</b></font> .";
} elseif ($qiandaodb['days'] >= '240') {
	$q['lvqd'] = 365 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.8]{$lv10name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.9]{$lv9name}</b></font> .";
} elseif ($qiandaodb['days'] >= '120') {
	$q['lvqd'] = 240 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.7]{$lv7name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.8]{$lv8name}</b></font> .";
} elseif ($qiandaodb['days'] >= '60') {
	$q['lvqd'] = 120 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.6]{$lv6name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.7]{$lv7name}</b></font> .";
} elseif ($qiandaodb['days'] >= '30') {
	$q['lvqd'] = 60 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.5]{$lv5name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.6]{$lv6name}</b></font> .";
} elseif ($qiandaodb['days'] >= '15') {
	$q['lvqd'] = 30 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.4]{$lv4name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.5]{$lv5name}</b></font> .";
} elseif ($qiandaodb['days'] >= '7') {
	$q['lvqd'] = 15 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.3]{$lv3name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.4]{$lv4name}</b></font> .";
} elseif ($qiandaodb['days'] >= '3') {
	$q['lvqd'] = 7 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.2]{$lv2name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.3]{$lv3name}</b></font> .";
} elseif ($qiandaodb['days'] >= '1') {
	$q['lvqd'] = 3 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.1]{$lv1name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.2]{$lv2name}</b></font> .";
}
$q['if']= $qiandaodb['time']<$tdtime ? "<span class=gray>".$lang['tdno']."</span>" : "<font color=green>".$lang['tdyq']."</font>";
$qtime = dgmdate($qiandaodb['time'], 'Y-m-d H:i');
$navigation = $lang['name'];
$navtitle = "$navigation";
$signBuild = '&copy; Shy9000[DSU Team]<br>Ver 2.2 Final'; 
$signadd = 'http://www.dsu.cc/thread-27298-1-1.html';
include template('dsu_paulsign:sign');
?>