<?php

$installpassword='';      //请在此处填入安装启动密码！

$installuser='Install_medaleasybuy';
$thisfilename='medaleasybuy_install.php';
$thisfileauthor='Horse Luke(竹节虚)[2009]';
$thisfileversion='0.0.2 build 20090119';
$title='勋章购买易安装程序';


if (empty($installpassword)){
    die('你尚未设置安装启动密码！请打开'.$thisfilename.',向$installpassword处输入一个密码！');
}

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/cache.func.php';
if($adminid!=1) {
	showmessage('你不是管理员，无法进行设置操作', NULL, 'NOPERM');
}




$fileList = <<<EOT
./medaleasybuy.php
./medaleasybuyadmincp.php
./APACHELICENSE2.txt
./templates/default/medaleasybuy_navbar.htm
./templates/default/medaleasybuy.htm
./templates/default/medaleasybuyadmincp_add.htm
./templates/default/medaleasybuyadmincp_basicsettings.htm
./templates/default/medaleasybuyadmincp_index.htm
./templates/default/medaleasybuyadmincp_medallist.htm
./templates/default/medaleasybuyalllogs.htm
EOT;

$installSQL = <<<EOT
DROP TABLE IF EXISTS `{$tablepre}medaleasybuylog`;
CREATE TABLE `{$tablepre}medaleasybuylog` (                         
                `eventid` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
                `medalid` smallint(6) unsigned NOT NULL DEFAULT '0',
                `buytime` int(10) unsigned NOT NULL DEFAULT '0',
                `expiration` int(10) unsigned NOT NULL DEFAULT '0',
				`buyip` char(15) NOT NULL,
                `moneyamount` int(10) unsigned NOT NULL DEFAULT '0',
                `extcreditsid` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (eventid),
                INDEX (uid)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;
DROP TABLE IF EXISTS `{$tablepre}medaleasybuymedals`;
CREATE TABLE `{$tablepre}medaleasybuymedals` (
                `medalid` smallint(6) unsigned NOT NULL DEFAULT '0' PRIMARY KEY,
                `moneyamount` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=gbk;
REPLACE INTO `{$tablepre}caches` (`cachename` ,`type` ,`dateline` ,`expiration` ,`data` )VALUES ('medaleasybuy_basicsettings', '1', '$timestamp', '0', '\$medaleasybuy_basicsettings=array(\'open\'=> 0,\'buyextcreditsid\'=> 2,);');
REPLACE INTO `{$tablepre}caches` (`cachename` ,`type` ,`dateline` ,`expiration` ,`data` )VALUES ('medaleasybuy_medallist', '1', '$timestamp', '0', '\$medaleasybuy_medallist=array(\'medalcanbuylistidcache\'=> array(),\'medalcanbuylist\'=> array(),);');
EOT;

$uninstallSQL = <<<EOT
DROP TABLE IF EXISTS `{$tablepre}medaleasybuylog`;
DROP TABLE IF EXISTS `{$tablepre}medaleasybuymedals`;
DELETE FROM `{$tablepre}caches` WHERE cachename='medaleasybuy_basicsettings';
DELETE FROM `{$tablepre}caches` WHERE cachename='medaleasybuy_medallist';
EOT;


@$action = $_GET['action'];
$actionlist=array('login','logout','index','install','uninstall');
$action = (isset($action) && in_array($action,$actionlist)) ? $action : 'login';

$mtime = explode(' ', microtime());
$starttime = $mtime[1] + $mtime[0];            //取得当前时间数组,以便统计运行时间

session_start();
$expired = FALSE;
$_SESSION['current_session'] = empty($_SESSION['current_session']) ? 'acf0fasfsafsaa' : $_SESSION['current_session'];
$_SESSION['user'] = empty($_SESSION['user']) ? 'acf0fasfsafsab' : $_SESSION['user'];
$_SESSION['session_key'] = empty($_SESSION['session_key']) ? 'acf0fasfsafsac' : $_SESSION['session_key'];
if ($_SESSION['current_session'] != $_SESSION['user']."=".$_SESSION['session_key']) $expired = TRUE;


if ($action=='logout'){
      session_destroy();
      $expired = TRUE;
      showmsg("注销退出成功！<font color=red><b>请记住删除$thisfilename</b></font>",'javascript:window.close()','点击此处关闭页面');

}elseif ($action=='login' || $expired){
      if(empty($_POST['submit'])) {
	      if ($expired==FALSE){
			  header("Location:{$thisfilename}?action=index");
		  }
          templates("header",'安装启动密码登录');
	      echo'    <form method="post" name="login" action="'.$thisfilename.'?action=login">
		  <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">&nbsp;&nbsp;&nbsp;为保证安装安全，请输入安装启动密码。</td>
      </tr>
      <tr>
        <td>
		    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" id="password" name="password" size="25" tabindex="0" onFocus="this.value=\'\'" value="@#%@#%$" />&nbsp;&nbsp;&nbsp;<button class="submit" type="submit" name="submit" value="true" tabindex="100">提交</button>
		</td>
      </tr>
      <tr>
      </tr>
    </table>
	</form>';
	      templates("footer");
		  
	  }else{
	    $password = empty($_POST['password']) ? '' : $_POST['password'];
        if (strcmp($password,$installpassword) !== 0) {
		    $expired = TRUE;
            showmsg("错误的密码！正在返回中......",'javascript:history.back(-1)','若浏览器无反应请点击这里',TRUE);
        } else {
            $time_started = md5(mktime());
            $secure_session_user = md5($installuser.$installpassword);
            $_SESSION['user'] = $installuser;
            $_SESSION['session_key'] = $time_started.$secure_session_user.session_id();
            $_SESSION['current_session'] = $installuser."=".$_SESSION['session_key'];
            $expired = FALSE;
	    	showmsg("安装程序标识符{$installuser}登录成功！正在转向中......",$thisfilename.'?action=index','若浏览器无反应请点击这里',TRUE);
			//header("Location:{$thisfilename}?action=index");
        } 
	  }

}elseif ($action=='index'){
	  if ($expired){
		   header("Location:{$thisfilename}?action=login");
	  }
      templates("header",'首页');
	  echo '    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">请选择一项任务<br /></td>
      </tr>
      <tr>
        <td class="content">
                &nbsp;&nbsp;&nbsp;
				<button class="submit" type="button" onclick="location.href=\''.$thisfilename.'?action=install\'">[√]<b>安装</b>插件所需数据库</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<button class="submit" type="button" onclick="location.href=\''.$thisfilename.'?action=uninstall\'">[x]<b>卸载</b>插件所需数据库</button>
        </td>
      </tr>
    </table>';
	templates("footer");
			
}elseif ($action=='install'){
	  if ($expired){
		   header("Location:{$thisfilename}?action=login");
	  }
	  $_SESSION['install_nextstep'] = empty($_SESSION['install_nextstep']) ? 1 : $_SESSION['install_nextstep'];
	  @$step = $_GET['step'];
	  $step = (isset($step) && $_SESSION['install_nextstep'] >= $step) ? $step : 1;
	  $_SESSION['install_nextstep'] = $step+1;
	  $prevstep=$step-1;
	  $prevbutton='<button class="submit" type="button" name="prevbtn" onclick="location.href=\''.$thisfilename.'?action=install&amp;step='.$prevstep.'\'">&lt 上一步</button>';
	  $nextbutton='<button class="submit" type="button" name="nextbtn" onclick="location.href=\''.$thisfilename.'?action=install&amp;step='.$_SESSION['install_nextstep'].'\'">下一步 &gt</button>';
	  $exittoindexbutton='<button type="submit" name="exittoindex" onclick="location.href=\''.$thisfilename.'?action=index\'">[x]退出该任务</button>';
	  if ($step==1){     //第一步,检查文件.
		  $showfileinfo = '';
		  $fileyes = '&nbsp;&nbsp;&nbsp;&nbsp;<span class="green">文件存在，通过...</span><br>';
		  $fileno = '&nbsp;&nbsp;&nbsp;&nbsp;<span class="red">文件不存在，失败...</span><br>';
		  $fileisok = 1;
		  foreach(explode("\n", trim($fileList)) as $filename) {
			  	if(trim($filename)!='') hack_fileExists(trim($filename));
		  }
		  $nextnotice = '';
		  if($fileisok==0) {
		      $nextnotice='<span class="red">文件检查失败！</span>请检查如上所述文件是否上传到服务器。<br>文件重新上传完毕后请点击下方的重试按钮。';
			  $nextbutton='<button class="submit" type="button" name="currbtn" onclick="location.href=\''.$thisfilename.'?action=install&amp;step=1\'">重试</button>';
          } else {
		      $nextnotice='<b><span class="green">文件检查通过。</span>请点击下一步继续。</b>';
          }
		  $prevbutton='';
          templates("header",'安装步骤一:检查文件');
		  echo'    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">检查文件中，请稍候......<br /><br />'.$showfileinfo.'<br />'.$nextnotice.'</td>
      </tr>
      <tr>
        <td class="buttomarea">
        '.$prevbutton.$nextbutton.$exittoindexbutton.'
        </td>
      </tr>
    </table>';		  
		  templates("footer");
		  exit;
	  }elseif ($step==2){     //第2步,阅读协议文件.
	      $nextbutton='<button class="submit" type="button" name="nextbtn" disabled onclick="location.href=\''.$thisfilename.'?action=install&amp;step='.$_SESSION['install_nextstep'].'\'">下一步 &gt</button>';
          $licensefile_dir="./APACHELICENSE2.txt"; 
          $fp=fopen($licensefile_dir,"r"); 
          $licensecontent=fread($fp,filesize($licensefile_dir)); 
          fclose($fp);

          templates("header",'安装步骤二:阅读协议');
		  echo'
	<script type="text/javascript">
	function checkok() {
		if(agree.checked) {
			nextbtn.disabled = false;
		} else {
			nextbtn.disabled = true;
		}
	}
	</script>
		      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">请阅读许可协议，继续安装前必须同意其中的条款。</td>
      </tr>
      <tr>
        <td class="content">
		<textarea name="notice" style="width: 98%; height: 200px" readonly>'.$licensecontent.'</textarea><br />
		<input type="checkbox" name="agree" id="agree" onClick="javascript:checkok()"><label for="agree">我同意按照以上协议使用该程序</label>
		</td>
      </tr>
      <tr>
        <td class="buttomarea">
        '.$prevbutton.$nextbutton.$exittoindexbutton.'
        </td>
      </tr>
    </table>';
	      unset($licensecontent);
		  templates("footer");
		  
	  }elseif ($step==3){     //第3步,确认执行SQL语句.
          templates("header",'安装步骤三:确认执行SQL语句');
		  echo'
		      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">
		  <li>该安装程序将进行数据库安装操作，会新建1个数据表。</li>
          <li>为以防万一请先备份论坛数据。<a href="admincp.php?action=database&operation=export" target="_blank">点击这里进入论坛数据备份</a></li>
          <li><font color="red">注意：一旦开始安装将清空原来本插件的所有数据库数据，如果已经安装了的话，请勿再次安装。</font></li>
          <li>点击“下一步”开始该插件的数据库安装操作。</li>
		</td>
      </tr>
      <tr>
        <td class="content">
		<textarea name="notice" style="width: 98%; height: 200px" readonly>'.$installSQL.'</textarea>
		</td>
      </tr>
      <tr>
        <td class="buttomarea">
        '.$prevbutton.$nextbutton.$exittoindexbutton.'
        </td>
      </tr>
    </table>';
		  templates("footer");
	  }elseif ($step==4){     //第4步,确认执行SQL语句.
	  
	      $db->query("DROP TABLE IF EXISTS `{$tablepre}medaleasybuylog`");
		  $db->query("CREATE TABLE `{$tablepre}medaleasybuylog` (                         
                    `eventid` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
                    `medalid` smallint(6) unsigned NOT NULL DEFAULT '0',
                    `buytime` int(10) unsigned NOT NULL DEFAULT '0',
                    `expiration` int(10) unsigned NOT NULL DEFAULT '0',
				    `buyip` char(15) NOT NULL,
                    `moneyamount` int(10) unsigned NOT NULL DEFAULT '0',
                    `extcreditsid` tinyint(1) NOT NULL DEFAULT '0',
                     PRIMARY KEY (eventid),
                     INDEX (uid)
                 ) ENGINE=MyISAM DEFAULT CHARSET=gbk;");
	      $db->query("DROP TABLE IF EXISTS `{$tablepre}medaleasybuymedals`");
	      $db->query("CREATE TABLE `{$tablepre}medaleasybuymedals` (
                `medalid` smallint(6) unsigned NOT NULL DEFAULT '0' PRIMARY KEY,
                `moneyamount` int(10) NOT NULL DEFAULT '0'
	      ) ENGINE=MyISAM DEFAULT CHARSET=gbk;");
	      $medaleasybuy_basicsettings=array(
						     'open'=> 0,
							 'buyextcreditsid'=> 2,
		  );
	      $medaleasybuy_medallist=array(
						     'medalcanbuylistidcache'=> array(),
							 'medalcanbuylist'=> array(),
          );
          $db->query("REPLACE INTO `{$tablepre}caches` (`cachename` ,`type` ,`dateline` ,`expiration` ,`data` )VALUES ('medaleasybuy_basicsettings', '1', '$timestamp', '0', '\$medaleasybuy_basicsettings=".daddslashes(var_export($medaleasybuy_basicsettings,true))."');");
          $db->query("REPLACE INTO `{$tablepre}caches` (`cachename` ,`type` ,`dateline` ,`expiration` ,`data` )VALUES ('medaleasybuy_medallist', '1', '$timestamp', '0', '\$medaleasybuy_medallist=".daddslashes(var_export($medaleasybuy_medallist,true))."');");

				 

	      showmsg("执行SQL语句成功!",$thisfilename.'?action=install&amp;step=5','如果浏览器无法跳转请点击这里',TRUE);
	  }elseif ($step==5){     //第5步,完成.
	      showmsg("恭喜！您已经安装完毕！",$thisfilename.'?action=index','点击此处返回任务页面');
	  }
						
}elseif ($action=='uninstall'){
	  if ($expired){
		    header("Location:{$thisfilename}?action=login");
	  }
	  $_SESSION['uninstall_nextstep'] = empty($_SESSION['uninstall_nextstep']) ? 1 : $_SESSION['uninstall_nextstep'];
	  @$step = $_GET['step'];
	  $step = (isset($step) && $_SESSION['uninstall_nextstep'] >= $step) ? $step : 1;
	  $_SESSION['uninstall_nextstep'] = $step+1;
	  $prevstep=$step-1;
	  $prevbutton='<button class="submit" type="button" name="prevbtn" onclick="location.href=\''.$thisfilename.'?action=uninstall&amp;step='.$prevstep.'\'">&lt 上一步</button>';
	  $nextbutton='<button class="submit" type="button" name="nextbtn" onclick="location.href=\''.$thisfilename.'?action=uninstall&amp;step='.$_SESSION['uninstall_nextstep'].'\'">下一步 &gt</button>';
	  $exittoindexbutton='<button type="submit" name="exittoindex" onclick="location.href=\''.$thisfilename.'?action=index\'">[x]退出该任务</button>';

	  if ($step==1){     //第1步,确认执行SQL语句.
          templates("header",'安装步骤三:确认执行SQL语句');
		  echo'
		      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">
		  <li>现在将开始卸载数据库工作。</li>
          <li>为以防万一请先备份论坛数据。<a href="admincp.php?action=database&operation=export" target="_blank">点击这里进入论坛数据备份</a></li>
          <li><font color="red">注意：一旦卸载将清空原来本插件的所有数据库数据，无法恢复。请慎重。</font></li>
          <li>卸载完毕后，记得删除如下插件文件：<br /><textarea name="notice" style="width: 98%; height: 200px" readonly>'.$fileList.'</textarea></li>
          <li>点击“下一步”开始该插件的数据库卸载装操作。</li>
		</td>
      </tr>
      <tr>
        <td class="content">
		<textarea name="notice" style="width: 98%; height: 100px" readonly>'.$uninstallSQL.'</textarea>
		</td>
      </tr>
      <tr>
        <td class="buttomarea">
        '.$nextbutton.$exittoindexbutton.'
        </td>
      </tr>
    </table>';
		  templates("footer");
	  }elseif ($step==2){     //第2步,执行SQL语句.
	  
	      $db->query("DROP TABLE IF EXISTS `{$tablepre}medaleasybuylog`");
		  $db->query("DROP TABLE IF EXISTS `{$tablepre}medaleasybuymedals`");
		  $db->query("DELETE FROM `{$tablepre}caches` WHERE cachename='medaleasybuy_basicsettings'");
		  $db->query("DELETE FROM `{$tablepre}caches` WHERE cachename='medaleasybuy_medallist'");
		  

	      showmsg("执行SQL语句成功!",$thisfilename.'?action=uninstall&amp;step=3','如果浏览器无法跳转请点击这里',TRUE);
	  }elseif ($step==3){     //第3步,完成.
	      showmsg("恭喜！您已经卸载数据库完毕！",$thisfilename.'?action=index','点击此处返回任务页面');
	  }


}



function templates($tpl,$subhead=NULL){
    global $installuser,$thisfilename,$title,$thisfileauthor,$thisfileversion,$starttime,$expired;
	switch ($tpl){
		case "header":
		    $subhead = empty($subhead) ? '&nbsp;' : '&nbsp;-&nbsp;'.$subhead;
		    if ($expired){
				$bottombutton = '<button type="button" onclick="location.href=\''.$thisfilename.'?action=login\'">[√]登录程序</button>';
			}else{
				$bottombutton = '<button type="button" onclick="location.href=\''.$thisfilename.'?action=logout\'">[x]注销退出</button>';
			}
            echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>'.$title.$subhead.'</title>

<style type="text/css">
<!--
BODY {
	background-color: #FFF;
	font-size: 15px;
}

button {
	border: 1px solid;
	border-color: #E8E8E8 #999 #999 #E8E8E8;
	background: #E8F3FD;
	height: 2em;
	line-height: 2em;
	cursor: pointer;
	font-size: 13px;
}
#postsubmit, button.submit {
	margin-right: 1em;
	border: 1px solid;
	border-color: #FFFDEE #FDB939 #FDB939 #FFFDEE;
	background: #FFF8C5;
	color: #090;
	padding: 0 2px;
	font-size: 13px;
}

a { color: #0066FF; text-decoration: none; }
	a:hover { text-decoration: underline; }
	
input,textarea{margin:10px 0 0 0;border-width:1px;border-style:solid;border-color:#FFF #64A7DD #64A7DD #FFF;padding:2px 2px;background:#E3EFF9;}
    input.radio,input.checkbox,input.textinput,input.specialsubmit {margin:0;padding:0;border:0;padding:0;background:none;}

.header {
	border-bottom: 1px solid #9DB3C5;
	background: #E8F3FD;
	text-align: left;
	vertical-align: middle;
	font-weight: bold;
	padding: 2px;
}

.headactions { float: right; padding: 3px 2px 0 0;font-size: 13px; }

.footer {
	border-top: 1px solid #9DB3C5;
	background: #E8F3FD;
	color: #999999;
	text-align: right;
	vertical-align: middle;
	font-size: 11px;
	padding: 2px;
}
.content{
	text-align: left;
	vertical-align: middle;
	top: 100px;
	left: 100px;
	right: 100px;
	bottom: 100px;
	padding: 2px;
}

.buttomarea{
	text-align: right;
	vertical-align: middle;
}
.mainbox {
	width:600px;
	margin: 15%;
}

.red {
	color: red;
}
.green {
	color: green;
}
-->
</style>
</head>

<body>

<div class="mainbox">
  <span class="headactions"><a href="javascript:window.close()" onClick="return confirm(\'确定退出'.$title.'？\n\n请记住退出后删除'.$thisfilename.'文件！\')">[x]关闭</a></span>
  <div class="header">'.$title.$subhead.'</div>
  <div class="buttomarea">
			'.$bottombutton.'
  </div>';

			break;  
			
			
		case "footer":
		    $mtime = explode(' ', microtime());
            $usetime = $mtime[1] + $mtime[0] - $starttime;           //取得当前时间数组,以便统计运行时间
			$usetime = empty($usetime) ? 0 : cutstr($usetime,6);
            echo '<br />
  <div class="footer">Author:&copy; '.$thisfileauthor.' ; Version: '.$thisfileversion.'<br />
  当前安装登录标识符：'.$installuser.'；当前服务器PHP版本：'.phpversion() .'；页面运行时间：'.$usetime.'秒</div>

</div>

</body>
</html>';	
			exit;
			break;
			
	}
}

function showmsg($message='Message is here.',$url=NULL,$urlmessage='点击这里继续',$autorefresh=FALSE){
            if ($autorefresh && $url){
				echo '<meta http-equiv="refresh" content="3;URL='.$url.'" />';
			}
		    templates("header",'提示信息');
			if ($url){
			    echo '<div class="content">'.$message.'<br /><a href="'.$url.'">'.$urlmessage.'</a></div>';			
			}
			else{
			    echo '<div class="content">'.$message.'</div>';			
			}
		    templates("footer");

}

function hack_fileExists($filename) {
	global $showfileinfo,$fileyes,$fileno,$fileisok;
	$showfileinfo .= $filename;
	if(file_exists($filename)) {
		$showfileinfo .= $fileyes;
	} else {
		$showfileinfo .= $fileno;
		$fileisok = 0;
	}
}

?>