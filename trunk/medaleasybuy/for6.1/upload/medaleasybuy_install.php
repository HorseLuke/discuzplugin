<?php

$installpassword='';      //���ڴ˴����밲װ�������룡

$installuser='Install_medaleasybuy';
$thisfilename='medaleasybuy_install.php';
$thisfileauthor='Horse Luke(�����)[2009]';
$thisfileversion='0.0.2 build 20090119';
$title='ѫ�¹����װ�װ����';


if (empty($installpassword)){
    die('����δ���ð�װ�������룡���'.$thisfilename.',��$installpassword������һ�����룡');
}

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/cache.func.php';
if($adminid!=1) {
	showmessage('�㲻�ǹ���Ա���޷��������ò���', NULL, 'NOPERM');
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
$starttime = $mtime[1] + $mtime[0];            //ȡ�õ�ǰʱ������,�Ա�ͳ������ʱ��

session_start();
$expired = FALSE;
$_SESSION['current_session'] = empty($_SESSION['current_session']) ? 'acf0fasfsafsaa' : $_SESSION['current_session'];
$_SESSION['user'] = empty($_SESSION['user']) ? 'acf0fasfsafsab' : $_SESSION['user'];
$_SESSION['session_key'] = empty($_SESSION['session_key']) ? 'acf0fasfsafsac' : $_SESSION['session_key'];
if ($_SESSION['current_session'] != $_SESSION['user']."=".$_SESSION['session_key']) $expired = TRUE;


if ($action=='logout'){
      session_destroy();
      $expired = TRUE;
      showmsg("ע���˳��ɹ���<font color=red><b>���סɾ��$thisfilename</b></font>",'javascript:window.close()','����˴��ر�ҳ��');

}elseif ($action=='login' || $expired){
      if(empty($_POST['submit'])) {
	      if ($expired==FALSE){
			  header("Location:{$thisfilename}?action=index");
		  }
          templates("header",'��װ���������¼');
	      echo'    <form method="post" name="login" action="'.$thisfilename.'?action=login">
		  <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">&nbsp;&nbsp;&nbsp;Ϊ��֤��װ��ȫ�������밲װ�������롣</td>
      </tr>
      <tr>
        <td>
		    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" id="password" name="password" size="25" tabindex="0" onFocus="this.value=\'\'" value="@#%@#%$" />&nbsp;&nbsp;&nbsp;<button class="submit" type="submit" name="submit" value="true" tabindex="100">�ύ</button>
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
            showmsg("��������룡���ڷ�����......",'javascript:history.back(-1)','��������޷�Ӧ��������',TRUE);
        } else {
            $time_started = md5(mktime());
            $secure_session_user = md5($installuser.$installpassword);
            $_SESSION['user'] = $installuser;
            $_SESSION['session_key'] = $time_started.$secure_session_user.session_id();
            $_SESSION['current_session'] = $installuser."=".$_SESSION['session_key'];
            $expired = FALSE;
	    	showmsg("��װ�����ʶ��{$installuser}��¼�ɹ�������ת����......",$thisfilename.'?action=index','��������޷�Ӧ��������',TRUE);
			//header("Location:{$thisfilename}?action=index");
        } 
	  }

}elseif ($action=='index'){
	  if ($expired){
		   header("Location:{$thisfilename}?action=login");
	  }
      templates("header",'��ҳ');
	  echo '    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">��ѡ��һ������<br /></td>
      </tr>
      <tr>
        <td class="content">
                &nbsp;&nbsp;&nbsp;
				<button class="submit" type="button" onclick="location.href=\''.$thisfilename.'?action=install\'">[��]<b>��װ</b>����������ݿ�</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<button class="submit" type="button" onclick="location.href=\''.$thisfilename.'?action=uninstall\'">[x]<b>ж��</b>����������ݿ�</button>
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
	  $prevbutton='<button class="submit" type="button" name="prevbtn" onclick="location.href=\''.$thisfilename.'?action=install&amp;step='.$prevstep.'\'">&lt ��һ��</button>';
	  $nextbutton='<button class="submit" type="button" name="nextbtn" onclick="location.href=\''.$thisfilename.'?action=install&amp;step='.$_SESSION['install_nextstep'].'\'">��һ�� &gt</button>';
	  $exittoindexbutton='<button type="submit" name="exittoindex" onclick="location.href=\''.$thisfilename.'?action=index\'">[x]�˳�������</button>';
	  if ($step==1){     //��һ��,����ļ�.
		  $showfileinfo = '';
		  $fileyes = '&nbsp;&nbsp;&nbsp;&nbsp;<span class="green">�ļ����ڣ�ͨ��...</span><br>';
		  $fileno = '&nbsp;&nbsp;&nbsp;&nbsp;<span class="red">�ļ������ڣ�ʧ��...</span><br>';
		  $fileisok = 1;
		  foreach(explode("\n", trim($fileList)) as $filename) {
			  	if(trim($filename)!='') hack_fileExists(trim($filename));
		  }
		  $nextnotice = '';
		  if($fileisok==0) {
		      $nextnotice='<span class="red">�ļ����ʧ�ܣ�</span>�������������ļ��Ƿ��ϴ�����������<br>�ļ������ϴ���Ϻ������·������԰�ť��';
			  $nextbutton='<button class="submit" type="button" name="currbtn" onclick="location.href=\''.$thisfilename.'?action=install&amp;step=1\'">����</button>';
          } else {
		      $nextnotice='<b><span class="green">�ļ����ͨ����</span>������һ��������</b>';
          }
		  $prevbutton='';
          templates("header",'��װ����һ:����ļ�');
		  echo'    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">����ļ��У����Ժ�......<br /><br />'.$showfileinfo.'<br />'.$nextnotice.'</td>
      </tr>
      <tr>
        <td class="buttomarea">
        '.$prevbutton.$nextbutton.$exittoindexbutton.'
        </td>
      </tr>
    </table>';		  
		  templates("footer");
		  exit;
	  }elseif ($step==2){     //��2��,�Ķ�Э���ļ�.
	      $nextbutton='<button class="submit" type="button" name="nextbtn" disabled onclick="location.href=\''.$thisfilename.'?action=install&amp;step='.$_SESSION['install_nextstep'].'\'">��һ�� &gt</button>';
          $licensefile_dir="./APACHELICENSE2.txt"; 
          $fp=fopen($licensefile_dir,"r"); 
          $licensecontent=fread($fp,filesize($licensefile_dir)); 
          fclose($fp);

          templates("header",'��װ�����:�Ķ�Э��');
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
        <td class="content">���Ķ����Э�飬������װǰ����ͬ�����е����</td>
      </tr>
      <tr>
        <td class="content">
		<textarea name="notice" style="width: 98%; height: 200px" readonly>'.$licensecontent.'</textarea><br />
		<input type="checkbox" name="agree" id="agree" onClick="javascript:checkok()"><label for="agree">��ͬ�ⰴ������Э��ʹ�øó���</label>
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
		  
	  }elseif ($step==3){     //��3��,ȷ��ִ��SQL���.
          templates("header",'��װ������:ȷ��ִ��SQL���');
		  echo'
		      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">
		  <li>�ð�װ���򽫽������ݿⰲװ���������½�1�����ݱ�</li>
          <li>Ϊ�Է���һ���ȱ�����̳���ݡ�<a href="admincp.php?action=database&operation=export" target="_blank">������������̳���ݱ���</a></li>
          <li><font color="red">ע�⣺һ����ʼ��װ�����ԭ����������������ݿ����ݣ�����Ѿ���װ�˵Ļ��������ٴΰ�װ��</font></li>
          <li>�������һ������ʼ�ò�������ݿⰲװ������</li>
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
	  }elseif ($step==4){     //��4��,ȷ��ִ��SQL���.
	  
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

				 

	      showmsg("ִ��SQL���ɹ�!",$thisfilename.'?action=install&amp;step=5','���������޷���ת��������',TRUE);
	  }elseif ($step==5){     //��5��,���.
	      showmsg("��ϲ�����Ѿ���װ��ϣ�",$thisfilename.'?action=index','����˴���������ҳ��');
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
	  $prevbutton='<button class="submit" type="button" name="prevbtn" onclick="location.href=\''.$thisfilename.'?action=uninstall&amp;step='.$prevstep.'\'">&lt ��һ��</button>';
	  $nextbutton='<button class="submit" type="button" name="nextbtn" onclick="location.href=\''.$thisfilename.'?action=uninstall&amp;step='.$_SESSION['uninstall_nextstep'].'\'">��һ�� &gt</button>';
	  $exittoindexbutton='<button type="submit" name="exittoindex" onclick="location.href=\''.$thisfilename.'?action=index\'">[x]�˳�������</button>';

	  if ($step==1){     //��1��,ȷ��ִ��SQL���.
          templates("header",'��װ������:ȷ��ִ��SQL���');
		  echo'
		      <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="content">
		  <li>���ڽ���ʼж�����ݿ⹤����</li>
          <li>Ϊ�Է���һ���ȱ�����̳���ݡ�<a href="admincp.php?action=database&operation=export" target="_blank">������������̳���ݱ���</a></li>
          <li><font color="red">ע�⣺һ��ж�ؽ����ԭ����������������ݿ����ݣ��޷��ָ��������ء�</font></li>
          <li>ж����Ϻ󣬼ǵ�ɾ�����²���ļ���<br /><textarea name="notice" style="width: 98%; height: 200px" readonly>'.$fileList.'</textarea></li>
          <li>�������һ������ʼ�ò�������ݿ�ж��װ������</li>
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
	  }elseif ($step==2){     //��2��,ִ��SQL���.
	  
	      $db->query("DROP TABLE IF EXISTS `{$tablepre}medaleasybuylog`");
		  $db->query("DROP TABLE IF EXISTS `{$tablepre}medaleasybuymedals`");
		  $db->query("DELETE FROM `{$tablepre}caches` WHERE cachename='medaleasybuy_basicsettings'");
		  $db->query("DELETE FROM `{$tablepre}caches` WHERE cachename='medaleasybuy_medallist'");
		  

	      showmsg("ִ��SQL���ɹ�!",$thisfilename.'?action=uninstall&amp;step=3','���������޷���ת��������',TRUE);
	  }elseif ($step==3){     //��3��,���.
	      showmsg("��ϲ�����Ѿ�ж�����ݿ���ϣ�",$thisfilename.'?action=index','����˴���������ҳ��');
	  }


}



function templates($tpl,$subhead=NULL){
    global $installuser,$thisfilename,$title,$thisfileauthor,$thisfileversion,$starttime,$expired;
	switch ($tpl){
		case "header":
		    $subhead = empty($subhead) ? '&nbsp;' : '&nbsp;-&nbsp;'.$subhead;
		    if ($expired){
				$bottombutton = '<button type="button" onclick="location.href=\''.$thisfilename.'?action=login\'">[��]��¼����</button>';
			}else{
				$bottombutton = '<button type="button" onclick="location.href=\''.$thisfilename.'?action=logout\'">[x]ע���˳�</button>';
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
  <span class="headactions"><a href="javascript:window.close()" onClick="return confirm(\'ȷ���˳�'.$title.'��\n\n���ס�˳���ɾ��'.$thisfilename.'�ļ���\')">[x]�ر�</a></span>
  <div class="header">'.$title.$subhead.'</div>
  <div class="buttomarea">
			'.$bottombutton.'
  </div>';

			break;  
			
			
		case "footer":
		    $mtime = explode(' ', microtime());
            $usetime = $mtime[1] + $mtime[0] - $starttime;           //ȡ�õ�ǰʱ������,�Ա�ͳ������ʱ��
			$usetime = empty($usetime) ? 0 : cutstr($usetime,6);
            echo '<br />
  <div class="footer">Author:&copy; '.$thisfileauthor.' ; Version: '.$thisfileversion.'<br />
  ��ǰ��װ��¼��ʶ����'.$installuser.'����ǰ������PHP�汾��'.phpversion() .'��ҳ������ʱ�䣺'.$usetime.'��</div>

</div>

</body>
</html>';	
			exit;
			break;
			
	}
}

function showmsg($message='Message is here.',$url=NULL,$urlmessage='����������',$autorefresh=FALSE){
            if ($autorefresh && $url){
				echo '<meta http-equiv="refresh" content="3;URL='.$url.'" />';
			}
		    templates("header",'��ʾ��Ϣ');
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