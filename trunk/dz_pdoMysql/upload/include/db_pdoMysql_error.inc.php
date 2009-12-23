<?php

/*
本文件原版权：
	[Discuz!] (C)2001-2009 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms
本文件修改者：
	$Id: db_pdoMysql_error.inc.php 000 2008-12-22 04:27:17Z horseluke $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$timestamp = time();
$errmsg = '';

$dberror = is_object($e) ? $e->getMessage() : 'UNKNOWN ERROR';
$dberrno = is_object($e) ? $e->getCode() : 999999;

if($dberrno == 1114) {

?>
<html>
<head>
<title>Max Onlines Reached [PDO-MySQL Connected]</title>
</head>
<body bgcolor="#FFFFFF">
<table cellpadding="0" cellspacing="0" border="0" width="600" align="center" height="85%">
  <tr align="center" valign="middle">
    <td>
    <table cellpadding="10" cellspacing="0" border="0" width="80%" align="center" style="font-family: Verdana, Tahoma; color: #666666; font-size: 9px">
    <tr>
      <td valign="middle" align="center" bgcolor="#EBEBEB">
        <br /><b style="font-size: 10px">Forum onlines reached the upper limit</b>
        <br /><br /><br />Sorry, the number of online visitors has reached the upper limit.
        <br />Please wait for someone else going offline or visit us in idle hours.
        <br /><br />
      </td>
    </tr>
    </table>
    </td>
  </tr>
</table>
</body>
</html>
<?

	exit();

} else {

	if($message) {
		$errmsg = "<b>Discuz! info</b> [PDO-MySQL] : $message\n\n";
	}
	if(isset($GLOBALS['_DSESSION']['discuz_user'])) {
		$errmsg .= "<b>User</b>: ".htmlspecialchars($GLOBALS['_DSESSION']['discuz_user'])."\n";
	}
	$errmsg .= "<b>Time</b>: ".gmdate("Y-n-j g:ia", $timestamp + ($GLOBALS['timeoffset'] * 3600))."\n";
	$errmsg .= "<b>Script</b>: ".$GLOBALS['PHP_SELF']."\n\n";
	if($sql) {
		$errmsg .= "<b>SQL</b>: ".htmlspecialchars($sql)."\n";
	}
	$errmsg .= "<b>Error</b>:  $dberror\n";
	$errmsg .= "<b>Errno.</b>:  $dberrno";

	echo "</table></table></table></table></table>\n";
	echo "<p style=\"font-family: Verdana, Tahoma; font-size: 11px; background: #FFFFFF;\">";
	echo nl2br(str_replace($GLOBALS['tablepre'], '[Table]', $errmsg));

	if($GLOBALS['adminemail']) {
		$errlog = array();
		if(@$fp = fopen(DISCUZ_ROOT.'./forumdata/dberror.log', 'r')) {
			while((!feof($fp)) && count($errlog) < 20) {
				$log = explode("\t", fgets($fp, 50));
				if($timestamp - $log[0] < 86400) {
					$errlog[$log[0]] = $log[1];
				}
			}
			fclose($fp);
		}

		if(!in_array($dberrno, $errlog)) {
			$errlog[$timestamp] = $dberrno;
			@$fp = fopen(DISCUZ_ROOT.'./forumdata/dberror.log', 'w');
			@flock($fp, 2);
			foreach(array_unique($errlog) as $dateline => $errno) {
				@fwrite($fp, "$dateline\t$errno");
			}
			@fclose($fp);
			if(function_exists('errorlog')) {
				errorlog('MySQL', basename($GLOBALS['_SERVER']['PHP_SELF'])." : $dberror - ".cutstr($sql, 120), 0);
			}

			if($GLOBALS['dbreport']) {
				echo "<br /><br />An error report has been dispatched to our administrator.";
				@sendmail($GLOBALS['adminemail'], '[Discuz!] MySQL Error Report',
						"There seems to have been a problem with the database of your Discuz! Board\n\n".
						strip_tags($errmsg)."\n\n".
						"Please check-up your MySQL server and forum scripts, similar errors will not be reported again in recent 24 hours\n".
						"If you have troubles in solving this problem, please visit Discuz! Community http://www.Discuz.net.");
			}

		} else {
			echo '<br /><br />Similar error report has been dispatched to administrator before.';
		}

	}
	echo '</p>';
	echo '<p style="font-family: Verdana, Tahoma; font-size: 12px; background: #FFFFFF;"><a href="http://faq.comsenz.com/?type=mysql&dberrno='.$dberrno.'&dberror='.rawurlencode($dberror).'" target="_blank">&#x5230; http://faq.comsenz.com &#x641c;&#x7d22;&#x6b64;&#x9519;&#x8bef;&#x7684;&#x89e3;&#x51b3;&#x65b9;&#x6848;</a></p>';

	exit();

}

?>