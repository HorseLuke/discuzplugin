<?php

/*
    V0.0.1 BUILD 20090303 BY Horse Luke
*/

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}


cpheader();
$lang['header_d6func']='D6功能移植';
if(!$operation || $operation == 'intro') {
		 shownav('d6func', '模块介绍');
		 showsubmenu('模块介绍');
		 showtips('<li>本模块主要增加Discuz 6有而Discuz 6.1F没有的功能。请挑选左边任务继续。</li>
				  <li><a href="http://www.freediscuz.net/bbs/thread-4711-1-1.html" target="_blank">获取该模块的升级版本请点击这里查看</a></li>
				  <li>当前模块版本：V0.0.2 Build 20090304 Fix1 BY Horse Luke</li>');

		 
}elseif($operation == 'membersmerge'){
	if(!submitcheck('mergesubmit')) {
		shownav('d6func', '合并用户');
		showsubmenu('合并用户');
		showtips('<li>合并用户 - 原用户的帖子、积分全部转入目标用户，同时删除原用户。</li>');
		showformheader('d6func&operation=membersmerge');
		showtableheader();
		showsetting('原用户名 1:', 'source[]','', 'text');
		showsetting('原用户名 2:', 'source[]','', 'text');
		showsetting('原用户名 3:', 'source[]','', 'text');
		showsetting('目标用户名:', 'target','', 'text');
		showsubmit('mergesubmit');
		showtablefooter();
		showformfooter();

	} else {

		$suids = $susernames = $comma = $tuid = $tusername = $sourcemember = $targetmember = '';

		if(is_array($source)) {
			$query = $db->query("SELECT uid, username, adminid, groupid FROM {$tablepre}members WHERE username IN ('".implode('\',\'', $source)."') AND username<>''");
			while($member = $db->fetch_array($query)) {
				if($member['adminid'] == 1 || $member['groupid'] == 1) {
					cpmsg('源用户中包含管理员身份会员，请首先将他的管理员身份解除后再进行合并操作，否则合并无法进行，请返回更改。');
				}
				$suids .= $comma.$member['uid'];
				$susernames .= $comma.'\''.addslashes($member['username']).'\'';
				$sourcemember .= $comma.$member['username'];
				$comma = ', ';
			}
		}

		$query = $db->query("SELECT uid, username FROM {$tablepre}members WHERE username='$target'");
		if(!($member = $db->fetch_array($query)) || !$suids) {
			cpmsg('您没有输入要合并的用户名，或指定的用户不存在，请返回修改。');
		}

		if(in_array($target, $source)) {
			cpmsg('对不起，原用户名不能与目标用户名相同，请返回修改。');
		}

		$tuid = $member['uid'];
		$tusername = addslashes($member['username']);
		$targetmember = $member['username'];

		if(!$confirmed) {

			$extra = '<input type="hidden" name="target" value="'.dhtmlspecialchars($target).'">';
			foreach($source as $username) {
				$extra .= '<input type="hidden" name="source[]" value="'.dhtmlspecialchars($username).'">';
			}

			cpmsg("本操作不可恢复，您确定将 {$sourcemember} 及其发表、拥有的全部资料转移到 {$targetmember} 中并删除 $sourcemember 吗？", "admincp.php?action=d6func&operation=membersmerge&mergesubmit=yes", 'form', $extra);

		} else {

			$db->query("DELETE FROM {$tablepre}access WHERE uid IN ($suids)");
			$db->query("UPDATE {$tablepre}adminnotes SET admin='$tusername' WHERE admin IN ($susernames)");
			$db->query("UPDATE {$tablepre}adminsessions SET uid='$tuid' WHERE uid IN ($suids)");
			$db->query("UPDATE {$tablepre}announcements SET author='$tusername' WHERE author IN ($susernames)");
			$db->query("UPDATE {$tablepre}banned SET admin='$tusername' WHERE admin IN ($susernames)");
			$db->query("DELETE FROM {$tablepre}buddys WHERE uid IN ($suids) OR buddyid IN ($suids)");
			$db->query("UPDATE {$tablepre}favorites SET uid='$tuid' WHERE uid IN ($suids)");
			$db->query("DELETE FROM {$tablepre}memberfields WHERE uid IN ($suids)");
			$db->query("DELETE FROM {$tablepre}moderators WHERE uid IN ($suids)");
			$db->query("UPDATE {$tablepre}myposts SET uid='$tuid' WHERE uid IN ($suids)", 'SILENT');
			$db->query("DELETE FROM {$tablepre}myposts WHERE uid IN ($suids)");
			$db->query("UPDATE {$tablepre}mythreads SET uid='$tuid' WHERE uid IN ($suids)");
			$db->query("UPDATE {$tablepre}pms SET msgfromid='$tuid', msgfrom='$tusername' WHERE msgfromid IN ($suids)");
			$db->query("UPDATE {$tablepre}pms SET msgtoid='$tuid' WHERE msgtoid IN ($suids)");
			$db->query("UPDATE {$tablepre}posts SET author='$tusername', authorid='$tuid' WHERE authorid IN ($suids)");
			$db->query("UPDATE {$tablepre}ratelog SET uid='$tuid', username='$tusername' WHERE uid IN ($suids)");
			$db->query("DELETE FROM {$tablepre}subscriptions WHERE uid IN ($suids)");
			$db->query("UPDATE {$tablepre}threads SET author='$tusername', authorid='$tuid' WHERE authorid IN ($suids)");
			$db->query("UPDATE {$tablepre}threads SET lastposter='$tusername' WHERE lastposter IN ($susernames)");
			$db->query("UPDATE {$tablepre}threadsmod SET uid='$tuid', username='$tusername' WHERE uid IN ($suids)");
			$db->query("DELETE FROM {$tablepre}validating WHERE uid IN ($suids)");
			$db->query("UPDATE {$tablepre}validating SET admin='$tusername' WHERE admin IN ($susernames)");
			$db->query("DELETE FROM {$tablepre}onlinetime WHERE uid IN ($suids)");
			$db->query("DELETE FROM {$tablepre}spacecaches WHERE uid IN ($suids)");

			$query = $db->query("SELECT SUM(credits) AS credits, SUM(extcredits1) AS extcredits1,
				SUM(extcredits2) AS extcredits2, SUM(extcredits3) AS extcredits3,
				SUM(extcredits4) AS extcredits4, SUM(extcredits5) AS extcredits5,
				SUM(extcredits6) AS extcredits6, SUM(extcredits7) AS extcredits7,
				SUM(extcredits8) AS extcredits8, SUM(posts) AS posts,
				SUM(digestposts) AS digestposts, SUM(pageviews) AS pageviews,
				SUM(oltime) AS oltime
				FROM {$tablepre}members WHERE uid IN ($suids)");

			$member = $db->fetch_array($query);
			$db->query("UPDATE {$tablepre}members SET credits=credits+$member[credits],
				extcredits1=extcredits1+$member[extcredits1], extcredits2=extcredits2+$member[extcredits2],
				extcredits3=extcredits3+$member[extcredits3], extcredits4=extcredits4+$member[extcredits4],
				extcredits5=extcredits5+$member[extcredits5], extcredits6=extcredits6+$member[extcredits6],
				extcredits7=extcredits7+$member[extcredits7], extcredits8=extcredits8+$member[extcredits8],
				posts=posts+$member[posts], digestposts=digestposts+$member[digestposts],
				pageviews=pageviews+$member[pageviews], oltime=oltime+$member[oltime]
				WHERE uid='$tuid'");
			$db->query("DELETE FROM {$tablepre}members WHERE uid IN ($suids)");

			updatecache('settings');

			cpmsg("原用户 {$sourcemember} 已成功合并入新用户 {$targetmember} 中。");

		}

	}

}elseif($operation == 'pmprune'){
	if(!submitcheck('prunesubmit')) {
		shownav('d6func', '清理短消息');
		showsubmenu('清理短消息');
		showtips('<li>本清理短消息功能依Discuz! 6原样打包</li><li>如果需要按日期范围清理短消息，推荐使用dztool for 6.1F。下载地址<a href="http://www.freediscuz.net/bbs/thread-4676-1-1.html" target="_blank">请点击这里</a></li>');
		showformheader('d6func&operation=pmprune');
		showtableheader();
	    showtablerow('', array('class=""'), array(
		    "不删除未读信息:",
		    '<input class="checkbox" type="checkbox" name="ignorenew" value="1" />'
	    ));
		showtablerow('', array('class=""'), array(
		    "删除多少天以前的短消息(不限制时间请输入 0):",
		    '<input type="text" name="days" size="7" />'
	    ));
		showtablerow('', array('class=""'), array(
		    "按发信用户名清理(多用户名中间请用半角逗号 \",\" 分割):",
		    '不区分大小写 <input class="checkbox" type="checkbox" name="cins" value="1" />
<br /><input type="text" name="users" size="40">'
	    ));
		showtablerow('', array('class=""'), array(
		    "按关键字搜索(关键字中可使用通配符 \"*\"):",
		    '<input class="radio" type="radio" name="srchtype" value="title" checked="checked" /> 只搜索标题 &nbsp; <input class="radio" type="radio" name="srchtype" value="fulltext" /> 全文搜索<br /><input type="text" name="srchtxt" size="40" maxlength="40" /><br />匹配多个关键字全部，可用空格或 "AND" 连接。如 win32 AND unix<br />匹配多个关键字其中部分，可用 "|" 或 "OR" 连接。如 win32 OR unix'
	    ));
		showsubmit('prunesubmit');
		showtablefooter();
		showformfooter();

	} else {

		if(!$confirmed || !isset($pmids) || !preg_match("/[\d,]/", $pmids)) {

			if($days == '') {
				cpmsg('您没有输入要删除短消息的时间范围，请返回修改。');
			} else {
				$uids = 0;
				$days=intval($days);
				$users = str_replace(',', '\',\'', str_replace(' ', '', $users));
				$query = $db->query("SELECT uid FROM {$tablepre}members WHERE ".(empty($cins) ? 'BINARY' : '')." username IN ('$users')");
				while($member = $db->fetch_array($query)) {
					$uids .= ','.$member['uid'];
				}

				$prunedateadd = $days != 0 ? "AND dateline<='".($timestamp - $days * 86400)."'" : '';
				$pruneuseradd = $users ? "AND ((msgfromid IN ($uids) AND folder='outbox') OR (msgtoid IN ($uids) AND folder='inbox'))" : '';
				$prunenewadd = $ignorenew ? "AND new='0'" : '';

				$prunetxtadd = '';
				if($srchtxt) {
					if(preg_match("(AND|\+|&|\s)", $srchtxt) && !preg_match("(OR|\|)", $srchtxt)) {
						$andor = ' AND ';
						$sqltxtsrch = '1';
						$srchtxt = preg_replace("/( AND |&| )/is", "+", $srchtxt);
					} else {
						$andor = ' OR ';
						$sqltxtsrch = '0';
						$srchtxt = preg_replace("/( OR |\|)/is", "+", $srchtxt);
					}
					$srchtxt = str_replace('*', '%', addcslashes($srchtxt, '%_'));
					foreach(explode('+', $srchtxt) as $text) {
						$text = trim($text);
						if($text) {
							$sqltxtsrch .= $andor;
							$sqltxtsrch .= $srchtype == 'fulltext' ? "(message LIKE '%".str_replace('_', '\_', $text)."%' OR subject LIKE '%$text%')" : "subject LIKE '%$text%'";
						}
					}
					$prunetxtadd = " AND ($sqltxtsrch)";
				}

				$pmids = 0;
				$query = $db->query("SELECT pmid FROM {$tablepre}pms WHERE 1 $prunedateadd $pruneuseradd $prunetxtadd $prunenewadd");
				while($pm = $db->fetch_array($query)) {
					$pmids .= ','.$pm['pmid'];
				}

				$pmnum = $db->num_rows($query);
				cpmsg('本操作不可恢复，您确定要删除符合条件的 {$pmnum} 条短消息吗？', "admincp.php?action=d6func&operation=pmprune&prunesubmit=yes", 'form', '<input type="hidden" name="pmids" value="'.$pmids.'">');
			}

		} else {

			$db->query("DELETE FROM {$tablepre}pms WHERE pmid IN ($pmids)");
			$num = $db->affected_rows();

			cpmsg('符合条件的 {$num} 条短消息成功删除。');

		}
	}


}elseif($operation == 'adduserwithuid'){

	if(!submitcheck('addsubmit')) {

		$groupselect = '';
		$query = $db->query("SELECT groupid, type, grouptitle, creditshigher FROM {$tablepre}usergroups WHERE type='member' AND creditshigher='0' OR (groupid NOT IN ('5', '6', '7') AND radminid<>'1' AND type<>'member') ORDER BY type DESC, (creditshigher<>'0' || creditslower<>'0'), creditslower");
		while($group = $db->fetch_array($query)) {
			if($group['type'] == 'member' && $group['creditshigher'] == 0) {
				$groupselect .= "<option value=\"$group[groupid]\" selected>$group[grouptitle]</option>\n";
			} else {
				$groupselect .= "<option value=\"$group[groupid]\">$group[grouptitle]</option>\n";
			}
		}
        shownav('d6func', '添加新用户[UID选号]');
		showsubmenu('添加新用户[UID选号]');
		$uidmax = $db->result_first("SELECT max(uid) FROM {$tablepre}members");
		showtips('<li>本功能结合Discuz 6和6.1F的代码，恢复Discuz 6中通过指定uid（即uid选号）添加用户的功能。</li><li>现在最大的uid值为<b>'.$uidmax.'</b>。若采取uid选号方式添加用户，请勿高于此值太多，否则会造成uid大段空白而形成资源浪费。<a href="http://soft.ccw.com.cn/programing/database/htm2008/20081010_517910.shtml" target="_blank">有关信息请查看MySQL的AUTO_INCREMENT知识</a></li>');
		showformheader('d6func&operation=adduserwithuid');
		showtableheader();

		showsetting('UID 选号范围：如不设定为自动分配；若需设定，请在左边填写uid选号最小值，右边填写uid选号最大值，并且范围请勿太大，以免加重服务器负担', array('uidlowerlimit', 'uidupperlimit'), array('', ''), 'range');
		
		showsetting('username', 'newusername', '', 'text');
		showsetting('password', 'newpassword', '', 'text');
		showsetting('email', 'newemail', '', 'text');
		showsetting('usergroup', '', '', '<select name="newgroupid">'.$groupselect.'</select>');
		showsetting('members_add_email_notify', 'emailnotify', '', 'radio');
		showsubmit('addsubmit');
		showtablefooter();
		showformfooter();

	} else {

		$newusername = trim($newusername);
		$newpassword = trim($newpassword);
		$newemail = trim($newemail);

		if($newusername == '' || $newpassword == '' || !$newemail) {
			cpmsg('members_add_invalid');
		}

		if(strlen($newusername) < 3) {
			cpmsg('members_add_tooshort');
		} elseif(strlen($newusername) > 15) {
			cpmsg('members_add_toolong');
		}

		$newsalt = random(6);
		$newpassword2 = md5(md5($newpassword).$newsalt);

		$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
		$censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($censoruser = trim($censoruser)), '/')).')$/i';
		if(preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is", $newusername) || ($censoruser && @preg_match($censorexp, $newusername))) {
			cpmsg('members_add_illegal');
		}

		$query = $db->query("SELECT uid FROM {$tablepre}members WHERE username='$newusername'");
		if($db->num_rows($query)) {
			cpmsg('members_add_username_duplicate');
		}
		
		

		$uidupperlimit = intval($uidupperlimit);
		$uidlowerlimit = intval($uidlowerlimit);
		
	    if($uidlowerlimit == 0){
			 $uid=NULL;
		}elseif($uidlowerlimit && $uidupperlimit < $uidlowerlimit){
			    cpmsg('错误指定UID范围（指定的uid选号最大值反而小于指定的uid选号最小值），请返回修改。','', 'error');
		}elseif($uidlowerlimit && $uidupperlimit >= $uidlowerlimit) {
			$uid = $uidlowerlimit;
			$query = $db->query("SELECT uid FROM {$tablepre}members WHERE uid BETWEEN '$uidlowerlimit' AND '$uidupperlimit' ORDER BY uid");
			while($member = $db->fetch_array($query)) {
				if($member['uid'] > $uid) {
					break;
				} else {
					$uid ++;
				}
			}
			if($uid > $uidupperlimit) {
				cpmsg('指定范围内没有可用的 UID，无法成功分配，请返回修改。','', 'error');
			}
		}

		
		

		$query = $db->query("SELECT groupid, radminid, type FROM {$tablepre}usergroups WHERE groupid='$newgroupid'");
		$group = $db->fetch_array($query);
		$newadminid = in_array($group['radminid'], array(1, 2, 3)) ? $group['radminid'] : ($group['type'] == 'special' ? -1 : 0);
		if($group['radminid'] == 1) {
			cpmsg('members_add_admin_none', '', 'error');
		}
		if(in_array($group['groupid'], array(5, 6, 7))) {
			cpmsg('members_add_ban_all_none', '', 'error');
		}

		$db->query("INSERT INTO {$tablepre}members (uid, username, password, salt, secques, gender, adminid, groupid, regip, regdate, lastvisit, lastactivity, posts, credits, email, bday, sigstatus, tpp, ppp, styleid, dateformat, timeformat, showemail, newsletter, invisible, timeoffset)
			VALUES ('$uid', '$newusername', '$newpassword2', '$newsalt', '', '0', '$newadminid', '$newgroupid', 'Manual Acting', '$timestamp', '$timestamp', '$timestamp', '0', '0', '$newemail', '0000-00-00', '0', '0', '0', '0', '0', '{$_DCACHE[settings][timeformat]}', '1', '1', '0', '{$_DCACHE[settings][timeoffset]}')");
		$uid = $db->insert_id();

		$db->query("REPLACE INTO {$tablepre}memberfields (uid) VALUES ('$uid')");

		if($emailnotify) {
			sendmail("$newusername <$newemail>", 'add_member_subject', 'add_member_message');
		}

		updatecache('settings');
		$newusername = stripslashes($newusername);
		cpmsg('members_add_succeed', '', 'succeed');

	}























}else{
   cpmsg('指定的模块或操作不存在，请返回。', '', 'error');
}
?>