<?php
/*
	[Discuz!] Tools (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: tools.php 1761 2008-12-10 07:55:12 by xiaobozi $
*/
/**
 * **********************	�t�m��	*******************************
 */
$tool_password = ''; // ������������ �бz�]�m�@�Ӥu��]�����j�ױK�X�A���ର�šI������������
error_reporting(E_ERROR | E_WARNING | E_PARSE);	//E_ERROR | E_WARNING | E_PARSE
@set_time_limit(0);
define('TOOLS_ROOT', dirname(__FILE__)."/");
define('VERSION', '2009�P����');
$functionall = array(
	array('all', 'all_repair', '�ˬd�έ״_�ƾڮw', '��Ҧ��ƾڪ�i���ˬd�״_�u�@�]����Ҧ�Comsenz���~�^�C'),
	array('all', 'all_runquery', '�ƾڮw�ɯ�', '�i�H�B����NSQL�y�y�A�зV�Ρ]����Ҧ�Comsenz���~�^�C'),
	array('all', 'all_checkcharset', '�s�X�˴��״_', '��Ҧ��ƾڪ�i��s�X�ˬd�M�״_�]����Ҧ�Comsenz���~�^�C'),
	array('dz_uc_ss_uch', 'all_restore', '�ɤJ�ƾڮw�ƥ�', '�@���ʾɤJ�׾¼ƾڳƥ��]�i�H��_Discuz!�BUCenter�BSupeSite�BUCenter Home�{�ǳƥ����ƾڡ^�C'),
	array('uc_dz', 'uc_dz_deletepms', '�M�z�u����', '��bUCenter�U�i�H'),
	array('dz_uc_uch_ec_ss', 'all_setadmin', '��^�޲z��', '�N��z���w���|���]�m���޲z���A�p�G�ѰO�޲z���b���K�X�A�o�O�Ӥ������u��]���Discuz!�BSupeSite�BUCenter�BUCenter Home�BECshop�^�C'),
	array('dz', 'dz_doctor', '�׾����', '�۰��ˬd�z���׾°t�m��󱡪p�A�t�����ҫH���H�ο��~���i�]Discuz!�׾¤U�ϥΡ^�C'),
	array('dz', 'dz_filecheck', '�j���������', '�ˬd�׾µ{�ǥؿ��U���DDiscuz!�x����]Discuz!�׾¤U�ϥΡ^�C'),
	array('dz', 'dz_rplastpost', '�״_�̫�^�_', '�״_�����̫�^�_�]Discuz!�׾¤U�ϥΡC)'),
	array('dz', 'dz_rpthreads', '��q�״_�D�D', '�Y�ǩ��l�����|�X�{���w�q�ާ@�A�i�H�Χ�q�״_�D�D���\\��״_�U�]Discuz!�׾¤U�ϥΡC'),
	array('dz', 'dz_mysqlclear', '�ƾڮw���l�ƾڲM�z', '��z���ƾڶi�榳�ĩ��ˬd�A�R�����l�ƾګH���]Discuz!�׾¤U�ϥΡ^�C'),
	array('dz', 'dz_moveattach', '����O�s�覡', '�N�z�{�b������s�x�覡���ӫ��w�覡�i��ؿ����c�վ�í��s�s�x�]Discuz!�׾¤U�ϥΡ^�C'),
	array('dz', 'dz_replace', '���l���e��q����', '���ӽ׾«�x���]�m�����y�L�o�C��A�i��ܩʪ���Ҧ����l�i��B�z,���l�N���ӹL�o�W�h�i��B�z�]Discuz!�׾¤U�ϥΡ^�C'),
	array('dz', 'dz_repair_auto', '�r�q�ۼW���״_', '�۰��˯��׾©Ҧ����ƾڪ�A�i�״_�ۼW�r�q�ᥢ�����D�]Discuz!�׾¤U�ϥΡ^�C'),
	array('dz', 'dz_updatecache', '��s�w�s', '�M���w�s�]Discuz!�׾¤U�ϥΡ^�C'),
	array('all', 'all_toolsback', '<font color="red">���X��ĳ</font>', '�z��Tools�u��c����ĳ�M�N���A�H�ΨϥιL�{���J�쪺���D�A�i�H�ήɪ����X���ڭ̡C')
);
//��l��
$lockfile = '';	//tools��s���m
$action = '';
$target_fsockopen = '0'; //�ϥΦ�ؤ覡�i��s���A�Ⱦ� 0=��W, 1=IP �]�ϥ�IP�覡�ݭn�O��IP�a�}�i�H���`�X�ݨ�z�����I�^
$alertmsg = ' onclick="alert(\'�I���T�w�}�l�B��,�i��ݭn�@�q�ɶ�,�еy��\');"';

foreach(array('_COOKIE', '_POST', '_GET') as $_request) {  //�����ܶq�����
	foreach($$_request as $_key => $_value) {
		($_key{0} != '_' && $_key != 'tool_password' && $_key != 'lockfile') && $$_key = taddslashes($_value);
	}
}
$whereis = getplace(); //�P�_����m
if($whereis == 'is_dz' && !defined('DISCUZ_ROOT')) {
	define('DISCUZ_ROOT', TOOLS_ROOT);
}
if(!$whereis && !in_array($whereis, array('is_dz', 'is_uc', 'is_uch', 'is_ss', 'is_ec', 'is_ecm'))) {
	$alertmsg = '';
	errorpage('<ul><li>�u��c������bDiscuz!�BUCenter�BUCente Home�BSupeSite�BECShop�Ϊ�ECmall���ڥؿ��U�~�ॿ�`�ϥΡC</li><li>�p�G�A�T���b�F�W�z�{�ǥؿ��U�A���ˬd�W�z�{�ǹB��һݭn�]�w���ؿ��iŪ�g�v���O�_���T</li>');
}
if(@file_exists($lockfile)) { //�u��c�O�_��w
	$alertmsg = '';
	errorpage("<h6>�u��c�w�����A�p�ݶ}�ҥu�n�q�L FTP �R�� $lockfile ���Y�i�I </h6>");
} elseif ($tool_password == ''){
	$alertmsg = '';
	errorpage('<h6>�u��c�K�X�q�{���šA�Ĥ@���ϥΫe�бz�ק糧���$tool_password�]�m�K�X�I</h6>');
}
if($action == 'login') {//�n��
	setcookie('toolpassword',md5($toolpassword), 0);
	echo '<meta http-equiv="refresh" content="2 url=?">';
	errorpage("<h6>�еy���A�{�ǵn�����I</h6>");
}
if(isset($toolpassword)) {
	if($toolpassword != md5($tool_password)) {
		$alertmsg = '';	//bug ���I�h�l�H
		errorpage("login");
	}
} else {
	$alertmsg = '';
	errorpage("login");
}
// �P�_�O�_�t���ɯũΪ̦w�ˤ��A���ܧR��
if(file_exists(TOOLS_ROOT.'./install/index.php') && $whereis=='is_dz'){
	$installfile = './install/index.php';
}

for($ti=1;$ti<11;$ti++){
	if(file_exists(TOOLS_ROOT.'./upgrade'.$ti.'.php') && $whereis=='is_dz'){	
		$upgradefile = './upgrade'.$ti.'.php';
	}
}
getdbcfg();//��o�ƾڮw�t�m�H�� �s���ƾڮw
mysql_connect($dbhost, $dbuser, $dbpw);
mysql_select_db($dbname);
$my_version = mysql_get_server_info();
if($my_version > '4.1'){
		$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
		$serverset .=$my_version > '5.0.1' ? ((empty($serverset))? '' : ',').'sql_mode=\'\'' : '';
		$serverset && mysql_query("SET $serverset");
}
//�y�{�}�l
if($action == 'all_repair') {//�״_�ƾڮw�}�l
	$counttables = $oktables = $errortables = $rapirtables = 0;
	if($check) {
		$tables = mysql_query("SHOW TABLES");
		if(!$nohtml) {
			echo "<html><head></head><body>";
		}
		if($iterations) {
			$iterations --;
		}
		while($table = mysql_fetch_row($tables)) {
			
				$counttables += 1;
				$answer = checktable($table[0],$iterations);
				if(!$nohtml) {
					echo "<tr><td colspan=4>&nbsp;</td></tr>";
				} elseif (!$simple) {
					flush();
				}
			
		}
		if(!$nohtml) {
			echo "</body></html>";
		}
		if($simple) {
			htmlheader();
			echo '<h4>�ˬd�״_�ƾڮw</h4>
			    <h5>�ˬd���G:</h5>
					<table>
						<tr><th>�ˬd��(�i)</th><th>���`��(�i)</th><th>�״_����(�i)</th><th>���~����(��)</th></tr>
						<tr><td>'.$counttables.'</td><td>'.$oktables.'</td><td>'.$rapirtables.'</td><td>'.$errortables.'</td></tr>
					</table>
				<p>�ˬd���G�S�����~��Ъ�^�u��c�����Ϥ��h�~��״_</p>
				<p><b><a href="tools.php?action=all_repair">�~��״_</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="tools.php">��^����</a></b></p>
				</td></tr></table>';
			specialdiv();
		}
	} else {
		htmlheader();
		echo "<h4>�ˬd�״_�ƾڮw</h4>
		<div class='specialdiv'>
				�ާ@���ܡG
				<ul>
				<li>�z�i�H�q�L�U�����覡�״_�w�g�l�a���ƾڮw�C�I����Э@�ߵ��ݭ״_���G�I</li>
				<li>���{�ǥi�H�״_�`�����ƾڮw���~�A���L�k�O�ҥi�H�״_�Ҧ����ƾڮw���~�C(�ݭn MySQL 3.23+)</li>
				</ul>
				</div>
				<h5>�ާ@�G</h5>
				<ul>
				<li><a href=\"?action=all_repair&check=1&nohtml=1&simple=1\">�ˬd�ù��խ״_�ƾڮw1��</a>
				<li><a href=\"?action=all_repair&check=1&iterations=5&nohtml=1&simple=1\">�ˬd�ù��խ״_�ƾڮw5��</a> (�]���ƾڮwŪ�g���Y�i�঳�ɻݭn�h�״_�X���~�৹���״_���\\)
				</ul>";
		specialdiv();
	}
	htmlfooter();
}elseif($action == 'all_restore') {//�ɤJ�ƾڮw�ƥ�
	ob_implicit_flush();
	$backdirarray = array( //���P���{�Ǧs��ƥ���󪺥ؿ��O���P��
						'is_dz'=>'forumdata',
						'is_uc'=>'data/backup',
						'is_uch'=>'data',
						'is_ss'=>'data'
	);
	if(!get_cfg_var('register_globals')) {
		@extract($HTTP_GET_VARS);
	}
	$sqldump = '';
	htmlheader();
	?><h4>�ƾڮw��_��Τu�� </h4><?php
	echo "<div class=\"specialdiv\">�ާ@���ܡG<ul>
		<li>�u���_�s��b�A�Ⱦ�(���{�Υ��a)�W���ƾڤ��,�p�G�z���ƾڤ��b�A�Ⱦ��W,�Х� FTP �W��</li>
		<li>�ƾڤ�󥲶��� Discuz! �ɥX�榡,�ó]�m�����ݩʨ� PHP ���Ū��</li>
		<li>�кɶq��ܪA�Ⱦ��Ŷ��ɬq�ާ@,�H�קK�W��.�p�{�Ǫ��[(�W�L 10 ����)������,�Ш�s</li></ul></div>";
	if($file) {
		if(strtolower(substr($file, 0, 7)) == "http://") {
			echo "�q���{�ƾڮw��_�ƾ� - Ū�����{�ƾ�:<br><br>";
			echo "�q���{�A�Ⱦ�Ū����� ... ";
			$sqldump = @fread($fp, 99999999);
			@fclose($fp);
			if($sqldump) {
				echo "���\\<br><br>";
			} elseif (!$multivol) {
				cexit("����<br><br><b>�L�k��_�ƾ�</b>");
			}
		} else {
			echo "<div class=\"specialtext\">�q���a��_�ƾ� - �ˬd�ƾڤ��:<br><br>";
			if(file_exists($file)) {
				echo "�ƾڤ�� $file �s�b�ˬd ... ���\\<br><br>";
			} elseif (!$multivol) {
				cexit("�ƾڤ�� $file �s�b�ˬd ... ����<br><br><br><b>�L�k��_�ƾ�</b></div>");
			}
			if(is_readable($file)) {
				echo "�ƾڤ�� $file �iŪ�ˬd ... ���\\<br><br>";
				@$fp = fopen($file, "r");
				@flock($fp, 3);
				$sqldump = @fread($fp, filesize($file));
				@fclose($fp);
				echo "�q���aŪ���ƾ� ... ���\\<br><br>";
			} elseif (!$multivol) {
				cexit("�ƾڤ�� $file �iŪ�ˬd ... ����<br><br><br><b>�L�k��_�ƾ�</b></div>");
			}
		}
		if($multivol && !$sqldump) {
			cexit("�����ƥ��d���ˬd ... ���\\<br><br><b>���߱z,�ƾڤw�g�������\\��_!�w���_��,�аȥ��R�����{��.</b></div>");
		}
		echo "�ƾڤ�� $file �榡�ˬd ... ";
		if($whereis == 'is_uc') {
			
			$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", substr($sqldump, 0, 256))));		
			$method = 'multivol';
			$volume = $identify[2];
		}else{
			@list(,,,$method, $volume) = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", preg_replace("/^(.+)/", "\\1", substr($sqldump, 0, 256)))));
		}
		if($method == 'multivol' && is_numeric($volume)) {
			echo "���\\<br><br>";
		} else {
			cexit("����<br><br><b>�ƾګD Discuz! �����ƥ��榡,�L�k��_</b></div>");
		}
		if($onlysave == "yes") {
			echo "�N�ƾڤ��O�s�쥻�a�A�Ⱦ� ... ";
			$filename = TOOLS_ROOT.'./'.$backdirarray[$whereis].strrchr($file, "/");
			@$filehandle = fopen($filename, "w");
			@flock($filehandle, 3);
			if(@fwrite($filehandle, $sqldump)) {
				@fclose($filehandle);
				echo "���\\<br><br>";
			} else {
				@fclose($filehandle);
				die("����<br><br><b>�L�k�O�s�ƾ�</b>");
			}
			echo "���\\<br><br><b>���߱z,�ƾڤw�g���\\�O�s�쥻�a�A�Ⱦ� <a href=\"".strstr($filename, "/")."\">$filename</a>.�w���_��,�аȥ��R�����{��.</b></div>";
		} else {
			$sqlquery = splitsql($sqldump);
			echo "����ާ@�y�y ... ���\\<br><br>";
			unset($sqldump);

			echo "���b��_�ƾ�,�е��� ... </div>";
			foreach($sqlquery as $sql) {
				$dbversion = mysql_get_server_info();
				$sql = syntablestruct(trim($sql), $dbversion > '4.1', $dbcharset);
				if(trim($sql)) {
					@mysql_query($sql);
				}
			}
		if($auto == 'off'){
			$nextfile = str_replace("-$volume.sql", '-'.($volume + 1).'.sql', $file);
			cexit("<ul><li>�ƾڤ�� <b>$volume#</b> ��_���\\,�p�G���ݭn���~���_��L���ƾڤ��</li><li>���I��<b><a href=\"?action=all_restore&file=$nextfile&multivol=yes\">������_</a></b>	�γ\\��W��_�U�@�Ӽƾڤ��<b><a href=\"?action=all_restore&file=$nextfile&multivol=yes&auto=off\">��W��_�U�@�ƾڤ��</a></b></li></ul>");
		} else {
			$nextfile = str_replace("-$volume.sql", '-'.($volume + 1).'.sql', $file);
			echo "<ul><li>�ƾڤ�� <b>$volume#</b> ��_���\\,�{�b�N�۰ʾɤJ��L�����ƥ��ƾ�.</li><li><b>�Ф������s�����Τ��_���{�ǹB��</b></li></ul>";
			redirect("?action=all_restore&file=$nextfile&multivol=yes");
		}
		}
	} else {
		
			$exportlog = array();
			if(is_dir(TOOLS_ROOT.'./'.$backdirarray[$whereis])) {
				$dir = dir(TOOLS_ROOT.'./'.$backdirarray[$whereis]);
				while($entry = $dir->read()) {
					$entry = "./".$backdirarray[$whereis]."/$entry";
					if(is_file($entry) && preg_match("/\.sql/i", $entry)) {
						$filesize = filesize($entry);
						$fp = @fopen($entry, 'rb');
						@$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 256))));
						@fclose ($fp);
							if(preg_match("/\-1.sql/i", $entry) || $identify[3] == 'shell'){
								$exportlog[$identify[0]] = array(	'version' => $identify[1],
													'type' => $identify[2],
													'method' => $identify[3],
													'volume' => $identify[4],
													'filename' => $entry,
													'size' => $filesize);
							}
					} elseif (is_dir($entry) && preg_match("/backup\_/i", $entry)) {
						$bakdir = dir($entry);
							while($bakentry = $bakdir->read()) {
								$bakentry = "$entry/$bakentry";
								if(is_file($bakentry)){
									@$fp = fopen($bakentry, 'rb');
									@$bakidentify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 256))));
									@fclose ($fp);
									if(preg_match("/\-1\.sql/i", $bakentry) || $bakidentify[3] == 'shell') {
										$identify['bakentry'] = $bakentry;
									}
								}
							}
							if(preg_match("/backup\_/i", $entry)){
								$exportlog[filemtime($entry)] = array(	'version' => $bakidentify[1],
													'type' => $bakidentify[2],
													'method' => $bakidentify[3],
													'volume' => $bakidentify[4],
													'bakentry' => $identify['bakentry'],
													'filename' => $entry);
							}
					}
				}
				$dir->close();
			} else {
				echo 'error';
			}
			krsort($exportlog);
			reset($exportlog);

			$exportinfo = '<h5>�ƾڳƥ��H��</h5><table><caption>&nbsp;&nbsp;&nbsp;�ƾڮw���</caption><tr><th>�ƥ�����</th><th>����</th><th>�ɶ�</th><th>����</th><th>�d��</th><th>�ާ@</th></tr>';
			foreach($exportlog as $dateline => $info) {
				$info['dateline'] = is_int($dateline) ? gmdate("Y-m-d H:i", $dateline + 8*3600) : '����';
					switch($info['type']) {
						case 'full':
							$info['type'] = '�����ƥ�';
							break;
						case 'standard':
							$info['type'] = '�зǳƥ�(����)';
							break;
						case 'mini':
							$info['type'] = '�̤p�ƥ�';
							break;
						case 'custom':
							$info['type'] = '�۩w�q�ƥ�';
							break;
					}
				$info['volume'] = $info['method'] == 'multivol' ? $info['volume'] : '';
				$info['method'] = $info['method'] == 'multivol' ? '�h��' : 'shell';
				$info['url'] = str_replace(".sql", '', str_replace("-$info[volume].sql", '', substr(strrchr($info['filename'], "/"), 1)));
				$exportinfo .= "<tr>\n".
					"<td>".$info['url']."</td>\n".
					"<td>$info[version]</td>\n".
					"<td>$info[dateline]</td>\n".
					"<td>$info[type]</td>\n";
				if($info['bakentry']){
				$exportinfo .= "<td><a href=\"?action=all_restore&bakdirname=".$info['url']."\">�d��</a></td>\n".
					"<td><a href=\"?action=all_restore&file=$info[bakentry]&importsubmit=yes\">[�����ɤJ]</a></td>\n</tr>\n";
				} else {
				$exportinfo .= "<td><a href=\"?action=all_restore&filedirname=".$info['url']."\">�d��</a></td>\n".
					"<td><a href=\"?action=all_restore&file=$info[filename]&importsubmit=yes\">[�����ɤJ]</a></td>\n</tr>\n";
				}
			}
		$exportinfo .= '</table>';
		echo $exportinfo;
		unset($exportlog);
		unset($exportinfo);
		echo "<br>";
	//�d�ݥؿ��̪��ƥ����C��A�@�ťؿ��U
	if(!empty($filedirname)){
			$exportlog = array();
			if(is_dir(TOOLS_ROOT.'./'.$backdirarray[$whereis])) {
					$dir = dir(TOOLS_ROOT.'./'.$backdirarray[$whereis]);
					while($entry = $dir->read()) {
						$entry = "./".$backdirarray[$whereis]."/$entry";
						if(is_file($entry) && preg_match("/\.sql/i", $entry) && preg_match("/$filedirname/i", $entry)) {
							$filesize = filesize($entry);
							@$fp = fopen($entry, 'rb');
							@$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 256))));
							@fclose ($fp);

							$exportlog[$identify[0]] = array(	'version' => $identify[1],
												'type' => $identify[2],
												'method' => $identify[3],
												'volume' => $identify[4],
												'filename' => $entry,
												'size' => $filesize);
						}
					}
					$dir->close();
				} else {
				}
				krsort($exportlog);
				reset($exportlog);

				$exportinfo = '<table>
								<caption>&nbsp;&nbsp;&nbsp;�ƾڮw���C��</caption>
								<tr>
								<th>���W</th><th>����</th>
								<th>�ɶ�</th><th>����</thd>
								<th>�j�p</th><td>�覡</th>
								<th>����</th><th>�ާ@</th></tr>';
				foreach($exportlog as $dateline => $info) {
					$info['dateline'] = is_int($dateline) ? gmdate("Y-m-d H:i", $dateline + 8*3600) : '����';
						switch($info['type']) {
							case 'full':
								$info['type'] = '�����ƥ�';
								break;
							case 'standard':
								$info['type'] = '�зǳƥ�(����)';
								break;
							case 'mini':
								$info['type'] = '�̤p�ƥ�';
								break;
							case 'custom':
								$info['type'] = '�۩w�q�ƥ�';
								break;
						}
					$info['volume'] = $info['method'] == 'multivol' ? $info['volume'] : '';
					$info['method'] = $info['method'] == 'multivol' ? '�h��' : 'shell';
					$exportinfo .= "<tr>\n".
						"<td><a href=\"$info[filename]\" name=\"".substr(strrchr($info['filename'], "/"), 1)."\">".substr(strrchr($info['filename'], "/"), 1)."</a></td>\n".
						"<td>$info[version]</td>\n".
						"<td>$info[dateline]</td>\n".
						"<td>$info[type]</td>\n".
						"<td>".get_real_size($info[size])."</td>\n".
						"<td>$info[method]</td>\n".
						"<td>$info[volume]</td>\n".
						"<td><a href=\"?action=all_restore&file=$info[filename]&importsubmit=yes&auto=off\">[�ɤJ]</a></td>\n</tr>\n";
				}
			$exportinfo .= '</table>';
			echo $exportinfo;
		}
	// �d�ݥؿ��̪��ƥ����C��A �G�ťؿ��U�A�䤤�G�ťؿ��O�H�����ͪ�
	if(!empty($bakdirname)){
			$exportlog = array();
			$filedirname = TOOLS_ROOT.'./'.$backdirarray[$whereis].'/'.$bakdirname;
			if(is_dir($filedirname)) {
					$dir = dir($filedirname);
					while($entry = $dir->read()) {
						$entry = $filedirname.'/'.$entry;
						if(is_file($entry) && preg_match("/\.sql/i", $entry)) {
							$filesize = filesize($entry);
							@$fp = fopen($entry, 'rb');
							@$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 256))));
							@fclose ($fp);

							$exportlog[$identify[0]] = array(	
												'version' => $identify[1],
												'type' => $identify[2],
												'method' => $identify[3],
												'volume' => $identify[4],
												'filename' => $entry,
												'size' => $filesize);
						}
					}
					$dir->close();
			}
			krsort($exportlog);
			reset($exportlog);

			$exportinfo = '<table>
					<caption>&nbsp;&nbsp;&nbsp;�ƾڮw���C��</caption>
					<tr>
					<th>���W</th><th>����</th>
					<th>�ɶ�</th><th>����</th>
					<th>�j�p</th><th>�覡</th>
					<th>����</th><th>�ާ@</th></tr>';
			foreach($exportlog as $dateline => $info) {
				$info['dateline'] = is_int($dateline) ? gmdate("Y-m-d H:i", $dateline + 8*3600) : '����';
				switch($info['type']) {
					case 'full':
						$info['type'] = '�����ƥ�';
						break;
					case 'standard':
						$info['type'] = '�зǳƥ�(����)';
						break;
					case 'mini':
						$info['type'] = '�̤p�ƥ�';
						break;
					case 'custom':
						$info['type'] = '�۩w�q�ƥ�';
						break;
				}
				$info['volume'] = $info['method'] == 'multivol' ? $info['volume'] : '';
				$info['method'] = $info['method'] == 'multivol' ? '�h��' : 'shell';
				$exportinfo .= "<tr>\n".
						"<td><a href=\"$info[filename]\" name=\"".substr(strrchr($info['filename'], "/"), 1)."\">".substr(strrchr($info['filename'], "/"), 1)."</a></td>\n".
						"<td>$info[version]</td>\n".
						"<td>$info[dateline]</td>\n".
						"<td>$info[type]</td>\n".
						"<td>".get_real_size($info[size])."</td>\n".
						"<td>$info[method]</td>\n".
						"<td>$info[volume]</td>\n".
						"<td><a href=\"?action=all_restore&file=$info[filename]&importsubmit=yes&auto=off\">[�ɤJ]</a></td>\n</tr>\n";
			}
			$exportinfo .= '</table>';
			echo $exportinfo;
		}
		echo "<br>";
		cexit("");
	}
} elseif ($action == 'uc_dz_deletepms') {//�M�z�u����
	htmlheader();
		if($dz_version > 600){
			errorpage('<ul><li>Discuz!6.0.0�H�W�������׾½Ч�tools�u��c��bUCenter�ؿ��U����~��ϥΦ��\\��</li><li>Discuz!6.0.0�Ψ䤧�e�������i�H������bDiscuz!�{�ǥؿ��U����ϥΦ��\\��</li>','','');
			exit;
		}
	echo '<h4>�M�z�u���� </h4>';
	echo "<div class=\"specialdiv\">�ާ@���ܡG<ul><li>�M�z�u�����e�@�w�n�ƥ��ƾڮw,�]�A�׾ª��ƾڮw�MUCenter���ƾڮw</li><li>�ϥΥ��{�ǲM�z�u�����O�����M�z���A���y�����u�����ƾڥᥢ���{�Ǥ��t����d��</li></ul></div>";
	if($step == 'search'){
		$sqladd = '';
		if($suid){//�o�e�̪�uid
			$sqladd .= $sqladd? ' and msgfromid='.$suid : 'msgfromid='.$suid;	
		}
		if($tuid){//�����̪�uid	
			$sqladd .= $sqladd? ' and msgtoid='.$tuid :'msgtoid='.$tuid;		
		}
		if($starttime){//�}�l���ɶ�
			$starttime = strtotime($starttime);
			$sqladd .= $sqladd? ' and dateline>'.$starttime : 'dateline>'.$starttime;
		}
		
		if($endtime){//�������ɶ�
			$endtime = strtotime($endtime);
			$sqladd .= $sqladd? ' and dateline<'.$endtime : 'dateline<'.$endtime;
		}
		
		if($notdeletenew){//�R����Ū�u����
		}else{
			$sqladd .= $sqladd?' and new=0':'new=0';
		}
		if(!$sqladd){
			errorpage('������g�z��u����������','','');		
		}
		$sql = "select count(*) from {$tablepre}pms ".($sqladd?"where ".$sqladd:'');
		$query = mysql_query($sql);
		$deletenums = mysql_result($query, 0);
		echo $deletenums."���u�������ݧR��";
		echo '<form action="tools.php?action=uc_dz_deletepms&step=delete" method="post" >';
		echo '<input type="hidden" name="sqladd" value="'.$sqladd.'" />';
		echo '<input type="submit" />';
		echo '</form>';
		htmlfooter();
		exit;
	}elseif($step == 'delete'){	
		if(!$sqladd){	
			errorpage('�ѼƤ����T');
			htmlfooter();
		}
		$sql = "delete from {$tablepre}pms ".($sqladd?"where ".$sqladd:'');
		$query = mysql_query($sql);
		$deletednums = mysql_affected_rows();

		echo "<table><tr><th>���ܫH��</th></tr><tr><td>�M�z�u�������\\�A�@�M�z�� $deletednums �u����</td></tr></table>";
		htmlfooter();
		exit;
	}else{
?>
<style>
.calendar_expire, .calendar_expire a:link, .calendar_expire a:visited {color: #999999;}.calendar_default, .calendar_default a:link, .calendar_default a:visited {color: #000000;}.calendar_checked, .calendar_checked a:link, .calendar_checked a:visited {color: #FF0000;}.calendar_today, .calendar_today a:link, .calendar_today a:visited {color: #00BB00;}.calendar_header td{ padding:5px;}#calendar_year {display: none;line-height: 130%;background: #FFFFFF;position: absolute;z-index: 10;}#calendar_year .col {float: left;background: #FFFFFF;margin-left: 1px;border: 1px solid #86B9D6;padding: 4px;}#calendar_month {display: none;background: #FFFFFF;line-height: 130%;border: 1px solid #86B9D6;padding: 4px;position: absolute;z-index: 11;}.calheader {height:35px;background: #E8F3FD;color:#FFF;font-size:14px;font-weight:bold;text-align:left;}.tableborder {background: #E8F3FD;border: 1px solid #E0E0E0;border-collapse:collapse;margin: 0;padding: 0; width:220px;}.category {color: #92A05A;background-color: #FFFFD9;margin: 0;padding: 0;}.category td {border-bottom: 1px solid #E0E0E0;}#calendar td { font-weight: bold;}
</style>
<script type="text/JavaScript">
var today = new Date();
function $(id) {
	return document.getElementById(id);
}
var userAgent = navigator.userAgent.toLowerCase();
var is_webtv = userAgent.indexOf('webtv') != -1;
var is_kon = userAgent.indexOf('konqueror') != -1;
var is_mac = userAgent.indexOf('mac') != -1;
var is_saf = userAgent.indexOf('applewebkit') != -1 || navigator.vendor == 'Apple Computer, Inc.';
var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
var is_moz = (navigator.product == 'Gecko' && !is_saf) && userAgent.substr(userAgent.indexOf('firefox') + 8, 3);
var is_ns = userAgent.indexOf('compatible') == -1 && userAgent.indexOf('mozilla') != -1 && !is_opera && !is_webtv && !is_saf;
var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera && !is_saf && !is_webtv) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);
var controlid = null;
var currdate = null;
var startdate = null;
var enddate  = null;
var yy = null;
var mm = null;
var hh = null;
var ii = null;
var currday = null;
var addtime = false;
var today = new Date();
var lastcheckedyear = false;
var lastcheckedmonth = false;
function getposition(obj) {
	var r = new Array();
	r['x'] = obj.offsetLeft;
	r['y'] = obj.offsetTop;
	while(obj = obj.offsetParent) {
		r['x'] += obj.offsetLeft;
		r['y'] += obj.offsetTop;
	}
	return r;
}
function loadcalendar() {
	s = '';
	s += '<div id="calendar" style="display:none; position:absolute; margin: 0;padding: 0;z-index:100;" onclick="doane(event)">';
	s += '<div><table class="tableborder" cellspacing="0" cellpadding="0" width="100%" style="text-align: center">';
	s += '<tr align="center" class="calheader"><td class="calheader"><a href="###" onclick="refreshcalendar(yy, mm-1)" title="�W�@��">�m</a></td><td colspan="5" style="text-align: center" class="calheader"><a href="###" onclick="showdiv(\'year\');doane(event)" title="�I����ܦ~��" id="year"></a>&nbsp; - &nbsp;<a id="month" title="�I����ܤ��" href="###" onclick="showdiv(\'month\');doane(event)"></a></td><td class="calheader"><A href="###" onclick="refreshcalendar(yy, mm+1)" title="�U�@��">�n</A></td></tr>';
	s += '<tr class="category calendar_header"><td>��</td><td>�@</td><td>�G</td><td>�T</td><td>�|</td><td>��</td><td>��</td></tr>';
	for(var i = 0; i < 6; i++) {
		s += '<tr class="altbg2">';
		for(var j = 1; j <= 7; j++)
			s += "<td id=d" + (i * 7 + j) + " height=\"19\">0</td>";
		s += "</tr>";
	}
	s += '<tr id="hourminute"><td colspan="7" align="center"><input class="textinput" style="width:30px;" type="text" size="1" value="" id="hour" onKeyUp=\'this.value=this.value > 23 ? 23 : zerofill(this.value);controlid.value=controlid.value.replace(/\\d+(\:\\d+)/ig, this.value+"$1")\'> �I <input class="textinput" style="width:30px;" type="text" size="1" value="" id="minute" onKeyUp=\'this.value=this.value > 59 ? 59 : zerofill(this.value);controlid.value=controlid.value.replace(/(\\d+\:)\\d+/ig, "$1"+this.value)\'> ��</td></tr>';
	s += '</table></div></div>';
	s += '<div id="calendar_year" onclick="doane(event)" style="display: none"><div class="col">';
	for(var k = 1930; k <= 2019; k++) {
		s += k != 1930 && k % 10 == 0 ? '</div><div class="col">' : '';
		s += '<a href="###" onclick="refreshcalendar(' + k + ', mm);$(\'calendar_year\').style.display=\'none\'"><span' + (today.getFullYear() == k ? ' class="calendar_today"' : '') + ' id="calendar_year_' + k + '">' + k + '</span></a><br />';
	}
	s += '</div></div>';
	s += '<div id="calendar_month" onclick="doane(event)" style="display: none">';
	for(var k = 1; k <= 12; k++) {
		s += '<a href="###" onclick="refreshcalendar(yy, ' + (k - 1) + ');$(\'calendar_month\').style.display=\'none\'"><span' + (today.getMonth()+1 == k ? ' class="calendar_today"' : '') + ' id="calendar_month_' + k + '">' + k + ( k < 10 ? '&nbsp;' : '') + ' ��</span></a><br />';
	}
	s += '</div>';
	if(is_ie && is_ie < 7) {
		s += '<iframe id="calendariframe" frameborder="0" style="display:none;position:absolute;filter:progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)"></iframe>';
		s += '<iframe id="calendariframe_year" frameborder="0" style="display:none;position:absolute;filter:progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)"></iframe>';
		s += '<iframe id="calendariframe_month" frameborder="0" style="display:none;position:absolute;filter:progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)"></iframe>';
	}
	document.write(s);
	document.onclick = function(event) {
		$('calendar').style.display = 'none';
		$('calendar_year').style.display = 'none';
		$('calendar_month').style.display = 'none';
		if(is_ie && is_ie < 7) {
			$('calendariframe').style.display = 'none';
			$('calendariframe_year').style.display = 'none';
			$('calendariframe_month').style.display = 'none';
		}
	}
	$('calendar').onclick = function(event) {
		doane(event);
		$('calendar_year').style.display = 'none';
		$('calendar_month').style.display = 'none';
		if(is_ie && is_ie < 7) {
			$('calendariframe_year').style.display = 'none';
			$('calendariframe_month').style.display = 'none';
		}
	}
}
 function settime(d) {
	$('calendar').style.display = 'none';
	$('calendar_month').style.display = 'none';
	if(is_ie && is_ie < 7) {
		$('calendariframe').style.display = 'none';
	}
	controlid.value = yy + "-" + zerofill(mm + 1) + "-" + zerofill(d) + (addtime ? ' ' + zerofill($('hour').value) + ':' + zerofill($('minute').value) : '');
}
function showcalendar(event, controlid1, addtime1, startdate1, enddate1) {
	
	controlid = controlid1;
	addtime = addtime1;
	startdate = startdate1 ? parsedate(startdate1) : false;
	enddate = enddate1 ? parsedate(enddate1) : false;
	currday = controlid.value ? parsedate(controlid.value) : today;
	hh = currday.getHours();
	ii = currday.getMinutes();
	var p = getposition(controlid);	
	$('calendar').style.display = 'block';
	$('calendar').style.left = p['x']+'px';
	if(is_ie && is_ie==7){ 
		$('calendar').style.top	= (p['y']-38)+'px';
	}else{
		$('calendar').style.top	= (p['y']+21)+'px';
	}
	doane(event);
	refreshcalendar(currday.getFullYear(), currday.getMonth());
	if(lastcheckedyear != false) {
		$('calendar_year_' + lastcheckedyear).className = 'calendar_default';
		$('calendar_year_' + today.getFullYear()).className = 'calendar_today';
	}
	if(lastcheckedmonth != false) {
		$('calendar_month_' + lastcheckedmonth).className = 'calendar_default';
		$('calendar_month_' + (today.getMonth() + 1)).className = 'calendar_today';
	}
	$('calendar_year_' + currday.getFullYear()).className = 'calendar_checked';
	$('calendar_month_' + (currday.getMonth() + 1)).className = 'calendar_checked';
	$('hourminute').style.display = addtime ? '' : 'none';
	lastcheckedyear = currday.getFullYear();
	lastcheckedmonth = currday.getMonth() + 1;
	if(is_ie && is_ie < 7) {
		$('calendariframe').style.top = $('calendar').style.top;
		$('calendariframe').style.left = $('calendar').style.left;
		$('calendariframe').style.width = $('calendar').offsetWidth;
		$('calendariframe').style.height = $('calendar').offsetHeight;
		$('calendariframe').style.display = 'block';
	}
}
function parsedate(s) {
	/(\d+)\-(\d+)\-(\d+)\s*(\d*):?(\d*)/.exec(s);
	var m1 = (RegExp.$1 && RegExp.$1 > 1899 && RegExp.$1 < 2101) ? parseFloat(RegExp.$1) : today.getFullYear();
	var m2 = (RegExp.$2 && (RegExp.$2 > 0 && RegExp.$2 < 13)) ? parseFloat(RegExp.$2) : today.getMonth() + 1;
	var m3 = (RegExp.$3 && (RegExp.$3 > 0 && RegExp.$3 < 32)) ? parseFloat(RegExp.$3) : today.getDate();
	var m4 = (RegExp.$4 && (RegExp.$4 > -1 && RegExp.$4 < 24)) ? parseFloat(RegExp.$4) : 0;
	var m5 = (RegExp.$5 && (RegExp.$5 > -1 && RegExp.$5 < 60)) ? parseFloat(RegExp.$5) : 0;
	/(\d+)\-(\d+)\-(\d+)\s*(\d*):?(\d*)/.exec("0000-00-00 00\:00");
	return new Date(m1, m2 - 1, m3, m4, m5);
}
function refreshcalendar(y, m) {
	var x = new Date(y, m, 1);
	var mv = x.getDay();
	var d = x.getDate();
	var dd = null;
	yy = x.getFullYear();
	mm = x.getMonth();
	$("year").innerHTML = yy;
	$("month").innerHTML = mm + 1 > 9  ? (mm + 1) : '0' + (mm + 1);
	for(var i = 1; i <= mv; i++) {
		dd = $("d" + i);
		dd.innerHTML = "&nbsp;";
		dd.className = "";
	}
	while(x.getMonth() == mm) {
		dd = $("d" + (d + mv));
		dd.innerHTML = '<a href="###" onclick="settime(' + d + ');return false">' + d + '</a>';
		if(x.getTime() < today.getTime() || (enddate && x.getTime() > enddate.getTime()) || (startdate && x.getTime() < startdate.getTime())) {
			dd.className = 'calendar_expire';
		} else {
			dd.className = 'calendar_default';
		}
		if(x.getFullYear() == today.getFullYear() && x.getMonth() == today.getMonth() && x.getDate() == today.getDate()) {
			dd.className = 'calendar_today';
			dd.firstChild.title = '����';
		}
		if(x.getFullYear() == currday.getFullYear() && x.getMonth() == currday.getMonth() && x.getDate() == currday.getDate()) {
			dd.className = 'calendar_checked';
		}
		x.setDate(++d);
	}
	while(d + mv <= 42) {
		dd = $("d" + (d + mv));
		dd.innerHTML = "&nbsp;";
		d++;
	}
	if(addtime) {
		$('hour').value = zerofill(hh);
		$('minute').value = zerofill(ii);
	}
}
function doane(event) {
	e = event ? event : window.event ;
	if(is_ie) {
		e.returnValue = false;
		e.cancelBubble = true;
	} else {
		e.stopPropagation();
		e.preventDefault();
	}
}
function refreshcalendar(y, m) {
	var x = new Date(y, m, 1);
	var mv = x.getDay();
	var d = x.getDate();
	var dd = null;
	yy = x.getFullYear();
	mm = x.getMonth();
	$("year").innerHTML = yy;
	$("month").innerHTML = mm + 1 > 9  ? (mm + 1) : '0' + (mm + 1);

	for(var i = 1; i <= mv; i++) {
		dd = $("d" + i);
		dd.innerHTML = "&nbsp;";
		dd.className = "";
	}
	while(x.getMonth() == mm) {
		dd = $("d" + (d + mv));
		dd.innerHTML = '<a href="###" onclick="settime(' + d + ');return false">' + d + '</a>';
		if(x.getTime() < today.getTime() || (enddate && x.getTime() > enddate.getTime()) || (startdate && x.getTime() < startdate.getTime())) {
			dd.className = 'calendar_expire';
		} else {
			dd.className = 'calendar_default';
		}
		if(x.getFullYear() == today.getFullYear() && x.getMonth() == today.getMonth() && x.getDate() == today.getDate()) {
			dd.className = 'calendar_today';
			dd.firstChild.title = '����';
		}
		if(x.getFullYear() == currday.getFullYear() && x.getMonth() == currday.getMonth() && x.getDate() == currday.getDate()) {
			dd.className = 'calendar_checked';
		}
		x.setDate(++d);
	}
	while(d + mv <= 42) {
		dd = $("d" + (d + mv));
		dd.innerHTML = "&nbsp;";
		d++;
	}
}
function zerofill(s) {
	var s = parseFloat(s.toString().replace(/(^[\s0]+)|(\s+$)/g, ''));
	s = isNaN(s) ? 0 : s;
	return (s < 10 ? '0' : '') + s.toString();
}
loadcalendar();
</script>

		<div>
		<form action="tools.php?action=uc_dz_deletepms&step=search" method="post"/>
		<table><tr><th>�o�e��UID</th><td><input class="textinput" type="text" name="suid"></td></tr>
		<tr><th>������UID</th><td><input class="textinput" type="text" name="tuid" value=""></td></tr>
		<tr><th>�o�e�ɶ�����</th><td><input class="textinput" type="text" onclick="showcalendar(event, this, true)" id="endtime" name="endtime" value=""></td></tr>
		<tr><th>�o�e�ɶ��ߩ�</th><td><input class="textinput" type="text" onclick="showcalendar(event, this, true)" id="starttime" name="starttime" value=""></td></tr>
		</table>
		<input type="submit" style="margin-right:10px;" value="�}�l�M�z" /><input type="radio" class="radio" name="notdeletenew" value="1" />�R����Ū�u����
		</form>
		</div>
<?php
		specialdiv();
		htmlfooter();
	}
} elseif ($action == 'all_runquery') {//�B��sql
		if(!empty($_POST['sqlsubmit']) && $_POST['queries']) {
			$sqlquery = splitsql(str_replace(array(' cdb_', ' {tablepre}', ' `cdb_'), array(' '.$tablepre, ' '.$tablepre, ' `'.$tablepre), $queries));
			$affected_rows = 0;
			foreach($sqlquery as $sql) {
				$sql = syntablestruct(trim($sql), $my_version > '4.1', $dbcharset);
				if(trim($sql) != '') {
					mysql_query(stripslashes($sql));
					if($sqlerror = mysql_error()) {
						break;
					} else {
						$affected_rows += intval(mysql_affected_rows());
					}
				}
			}
			errorpage($sqlerror? $sqlerror : "�ƾڮw�ɯŦ��\\,�v�T���: &nbsp;$affected_rows",'�ƾڮw�ɯ�');
			if(strpos($queries,'settings') && $whereis == 'is_dz') {
				require_once './include/cache.func.php';
				updatecache('settings');
			}
		}
		htmlheader();
		echo "<h4>�ƾڮw�ɯ�</h4>
			<form method=\"post\" action=\"tools.php?action=all_runquery\">
			<h5>�бN�ƾڮw�ɯŻy�y�߶K�b�U��</h4>";
		if($whereis == 'is_dz') {
			echo "<select name=\"queryselect\" onChange=\"queries.value = this.value\">
				<option value = ''>�i���TOOLS���m�ɯŻy�y</option>
				<option value = \"REPLACE INTO ".$tablepre."settings (variable, value) VALUES ('seccodestatus', '0')\">�����Ҧ����ҽX�\\��</option>
				<option value = \"REPLACE INTO ".$tablepre."settings (variable, value) VALUES ('supe_status', '0')\">�����׾¤���supersite�\\��</option>
				<option value = \"TRUNCATE TABLE ".$tablepre."failedlogins\">�M�ŵn�����~�O��</option>
			</select>";
		}
			echo "<br />
			<br /><textarea name=\"queries\">$queries</textarea><br />
			<input type=\"submit\" name=\"sqlsubmit\" value=\"�� &nbsp; ��\">
			</form>";
	
	htmlfooter();
	
		
}elseif($action == 'all_checkcharset') {//�s�X�˴�
	$maincharset = $dbcharset;
	if($my_version > '4.1') {
		if($repairsubmit){
			htmlheader();
			echo '<h4>�s�X�ˬd</h4>';
			echo "<div class=\"specialdiv\">�ާ@���ܡG<ul>
			<li>MySQL�����b4.1�H�W�~���r�Ŷ����]�w�A�ҥH�ƾڮw4.1�����H�W���~��ϥΥ��\\��</li>
			<li>�p�G�Y�Ǧr�q���r�Ŷ����@�P�A���i��|�ɭP�{�Ǥ��X�{�ýX�A�ɶq��r�Ŷ����@�P���r�q�ഫ���Τ@�r�Ŷ�</li>
			<li>����MySQL�s�X����i�H�Ѧ� <a href='http://www.discuz.net/viewthread.php?tid=1022673' target='_blank'>�I���d��</a></li>
			<li>�@������MySQL�s�X�譱��<a href='http://www.discuz.net/viewthread.php?tid=1070306' target='_blank'>�е{</a></li>
			<li><font color=red>Tools�u��c�u�O�������A�״_�ƾڮw���r�q�s�X�A�״_�e�Х��ƥ��A���ƾڮw�A�H�K�y�������n���l���A�p�G�]���A�S���ƥ��ƾڮw�y�����l���P���{�ǵL��</font></li>
			<li><font color=red>����״_latin1�r�Ŷ��A�i�H���ըϥΨ�L��k�i����X</font></li>
			</ul></div>";
			if(!is_array($repair)){
				$repair=array();
				show_tools_message('�S���״_����r�q', 'tools.php?action=all_checkcharset');
				htmlfooter();
				exit;
			}
			foreach($repair as $key=>$value){
				$tableinfo = '';
				$tableinfo = explode('|', $value);
				$tablename = $tableinfo[0];
				$collation = $tableinfo[1];
				$maincharset = $tableinfo[2];
                $query = mysql_query("SHOW CREATE TABLE $tablename");
				while($createsql = mysql_fetch_array($query)){
						$colationsql = explode(",\n",$createsql[1]);
						foreach($colationsql as $numkey=>$collsql){
							
							if(strpos('ddd'.$collsql,'`'.$collation.'`') == 5){	
								$colarray = explode(' ', $collsql);
								print_r($colarray);exit;
								$collsql = preg_replace("/character set (?!latin1)(.+?)\b/","character set $maincharset",$collsql);
								$changesql = 'alter table '.$tablename.' change `'.$collation.'` '.$collsql;
								mysql_query($changesql);
							}
						}
				}
			}
			show_tools_message('�״_����', 'tools.php?action=all_checkcharset');
			htmlfooter();
			exit;
		}else{
			$sql = "SELECT `TABLE_NAME` AS `Name`, `TABLE_COLLATION` AS `Collation` FROM `information_schema`.`TABLES` WHERE   ".(strpos("php".PHP_OS,"WIN")?"":"BINARY")."`TABLE_SCHEMA` IN ('$dbname') AND TABLE_NAME like '$tablepre%'";
			$query = @mysql_query($sql);
			$dbtable = array();
			$chars = array('gbk'=>0,'big5'=>0,'utf8'=>0,'latin1'=>0);//�U�ӽs�X���r�q�ƶq 
			if(!$query){
				htmlheader();
				errorpage('�z��e���ƾڮw�����L�k�ˬd�r�Ŷ��]�w�A�i��O�ѩ󪩥��L�C������ˬd�y�y�ɭP', '', 0, 0);
				htmlfooter();
				exit;
			}
			while($dbdetail = mysql_fetch_array($query)){
				
				$dbtable[$dbdetail["Name"]]["Collation"] = pregcharset($dbdetail["Collation"],1); //��o�C�Ӫ��s�X
				$dbtable[$dbdetail["Name"]]["tablename"] = $dbdetail["Name"]; //�ƾڮw�Ҧ�����W
				
				$tablequery = mysql_query("SHOW FULL FIELDS FROM `".$dbdetail["Name"]."`");//���X�C�Ӫ����c
				while($tables= mysql_fetch_array($tablequery)){
					if(!empty($tables["Collation"])) {
						$collcharset = pregcharset($tables["Collation"], 0);
						$tableschar[$collcharset][$dbdetail["Name"]][] = $tables["Field"];
						$chars[pregcharset($tables["Collation"], 0)]++;
					}
				}
				
			}
		}
	}
	
	htmlheader();
	echo '<h4>�s�X�ˬd</h4>';
	echo "<div class=\"specialdiv\">�ާ@���ܡG<ul>
			<li>MySQL�����b4.1�H�W�~���r�Ŷ����]�w�A�ҥH�ƾڮw4.1�����H�W���~��ϥΥ��\\��</li>
			<li>�p�G�Y�Ǧr�q���r�Ŷ����@�P�A���i��|�ɭP�{�Ǥ��X�{�ýX�A�ɶq��r�Ŷ����@�P���r�q�ഫ���Τ@�r�Ŷ�</li>
			<li>����MySQL�s�X����i�H�Ѧ� <a href='http://www.discuz.net/viewthread.php?tid=1022673' target='_blank'>�I���d��</a></li>
			<li>�@������MySQL�s�X�譱��<a href='http://www.discuz.net/viewthread.php?tid=1070306' target='_blank'>�е{</a></li>
			<li><font color=red>Tools�u��c�u�O�������A�״_�ƾڮw���r�q�s�X�A�״_�e�Х��ƥ��A���ƾڮw�A�H�K�y�������n���l���A�p�G�]���A�S���ƥ��ƾڮw�y�����l���P���{�ǵL��</font></li>
			<li><font color=red>����״_latin1�r�Ŷ��A�i�H���ըϥΨ�L��k�i����X</font></li>
			
			</ul></div>";
	if($my_version > '4.1') {
	echo'<div class="tabbody">
		<style>.tabbody p em { color:#09C; padding:0 10px;} .char_div { margin-top:30px; margin-bottom:30px;} .char_div h4, .notice h4 { font-weight:600; font-size:16px; margin:0; padding:0; margin-bottom:10px;}</style>
		<div class="char_div"><h5>�ƾڮw('.$dbname.')���r�Ŷ��έp�G</h5>
		<table style="width:40%; margin:0; margin-bottom:20px;"><tr><th>gbk�r�q</th><th>big5�r�q</th><th>utf8�r�q</th><th>latin1�r�q</th></tr><tr><td>'.$chars[gbk].'&nbsp;</td><td>'.$chars[big5].'&nbsp;</td><td>'.$chars[utf8].'&nbsp;</td><td>'.$chars[latin1].'&nbsp;</td></tr></table>
		<div class="notice">
			<h5>�U�C�r�q�i��s�b�s�X�]�m���`�G</h5>';
			?>
			
			<script type="text/JavaScript">
	function setrepaircheck(obj, form, table, char) {
		eval('var rem = /^' + table + '\\|.+?\\|.+?\\|' + char + '$/;');
		eval('var rechar = /latin1/;');
		for(var i = 0; i < form.elements.length; i++) {
			var e = form.elements[i];
			if(e.type == 'checkbox' && e.name == 'repair[]') {
			
				if(rem.exec(e.value) != null) {
				
					if(obj.checked) {
						if(rechar.exec(e.value) != null){
							e.checked = false;
							e.disabled = true;
							
						}else{
							
							e.checked = true;
						}
						
					} else {
						e.checked = false;
						

					}
				}
				
			}
		}
	}
</script>
<?php
		  foreach($chars as $char => $num) {
			  
			  if ($char != $maincharset) {
					if(is_array($tableschar[$char])) {
				  echo '<form name="form" action="" method="post">';
					  foreach($tableschar[$char] as $tablename => $fields) {
					   echo'<table style="margin-left:0; width:40%;">
							<tr>
								<th><input type="checkbox" id="tables[]" style="border-style:none;"  name="chkall"  onclick="setrepaircheck(this, this.form, \''.$tablename.'\', \''.$char.'\');"  value="'.$tablename.'">����</th>
								<th width=60%><strong>'.$tablename.'</strong> <font color="red">���`���r�q</font></th>
								<th>�s�X</th>
							</tr>';
						   foreach($fields as $collation) {
								echo'<tr><td><input type="checkbox" style="border-style:none;"';
								if($char == 'latin1'){
								echo ' disabled ';
								}
								echo 'id="fields['.$tablename.'][]"';
								echo 'name=repair[] value="'.$tablename.'|'.$collation.'|'.$maincharset.'|'.$char.'">';
								echo '</td><td>'.$collation.'</td><td><font color="red">'.$char.'</font></td></tr>';
						   }
						echo '</table>';
					  }
				
				  }
				 
			  }
		  }
		  echo '<input type="submit" value="����w���r�q�s�X�ഫ��'.$maincharset.'" name="repairsubmit" onclick="javascript:if (confirm(\'Tools�u��c�u�O�������A�״_�ƾڮw�r�q�r�Ŷ��A�״_�e�Х��ƥ��A���ƾڮw�A�H�K�y�������n���l���A�p�G�]���A�S���ƥ��ƾڮw�y�����l���P���{�ǵL��\'));else return false;"></form>';
		echo'<br /><br /><br /></div> </div>';
	}else {
		errorpage('MySQL�ƾڮw�����b4.1�H�U�A�S���r�Ŷ��]�w�A�L���˴�', '', 0, 0);
	}
		htmlfooter();
} elseif ($action == 'dz_doctor') {//�׾����
	htmlheader();
	echo "<script language=\"javascript\">
					function copytoclip(obj) {
						var userAgent = navigator.userAgent.toLowerCase();
						var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
						var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);
						if(is_ie && obj.style.display != 'none') {
							var rng = document.body.createTextRange();
							rng.moveToElementText(obj);
							rng.scrollIntoView();
							rng.select();
							rng.execCommand(\"Copy\");
							rng.collapse(false);
						}
					}
					function $(id) {
						return document.getElementById(id);
					}
					function openerror(error){
						obj = document.getElementById(error);
						if(obj.style.display == ''){
							obj.style.display='none';
						}else{
							obj.style.display='';
						}
					}
			  </script>";
	function create_checkfile() {
		global $dir;
		$fp = @fopen('./forumdata/checkfile.php',w);
		$includedir = $dir != './' ?  str_replace('forumdata/','./',$dir) : '../';
		$content = "<?php
			define('IN_DISCUZ',TRUE);
			if(function_exists('ini_set')) @ini_set('display_errors',1);
			if(\$_GET['file'] != 'config.inc.php') include '../include/common.inc.php';
			error_reporting(E_ERROR | E_WARNING | E_PARSE);
			include '$includedir'.\$_GET['file'];\n?>";
		fwrite($fp, $content);
		fclose($fp);
	}
	function http_fopen($host, $path, $port="80") {
		global $target_fsockopen;
		$conn_host = $target_fsockopen == 1 ? gethostbyname($host) : $host;
		$conn_port = $port;
		$abs_url = "http://$host:$port$path";
		$query="GET   $abs_url   HTTP/1.0\r\n".
		"HOST:$host:$port\r\n".
		"User-agent:PHP/class   http   0.1\r\n".
		"\r\n";
		$fp=fsockopen($conn_host, $conn_port);
		if(!$fp){
			return   false;
		}
		fputs($fp,$query);
		//�o���^�����G
		$contents = "";
		while (!feof($fp)) {
			$contents .= fread($fp, 1024);
		}
		fclose($fp);
		$array = split("\n\r", $contents, "2");
		return trim($array[1]);
	}
	//�׾¼Ҧ��˦��N�X�ܶq
	$ok_style_s = '[color=RoyalBlue][b]';
	$error_style_s = '[color=Red][b]';
	$style_e = '[/b][/color]';
	$title_style_s = '[b]';
	$title_style_e = '[/b]';
	
	$phpfile_array = array('discuzroot', 'templates', 'cache');//�����~�ˬd�����ؿ��ι����W��($dir_array)
	$dir_array = array('�׾®ڥؿ�', '�ҪO�w�s�ؿ�(forumdata/templates)', '�䥦�w�s�ؿ�(forumdata/cache)');
	$doctor_top = count($phpfile_array) - 1;
	//$doctor_step = isset($_REQUEST['doctor_step']) ? intval($_REQUEST['doctor_step']) : '';
	
	if(@!include("./config.inc.php")) {
		if(@!include("./config.php")) {
			cexit("<h4>�Х��W��config���H�O�ұz���ƾڮw�ॿ�`�챵�I</h4>");
		}
	}
	if($doctor_step == $doctor_top) {
	
		//�ˬdConfig.inc.php���t�m
		$carray = $clang = $comment = array();
		$doctor_config = $doctor_config_db = '';
		$configfilename = file_exists('./config.inc.php') ? './config.inc.php' : './config.php';
		$fp = @fopen($configfilename, 'r');
		$configfile = @fread($fp, @filesize($configfilename));
		@fclose($fp);
		preg_match_all("/[$]([\w\[\]\']+)\s*\=\s*[\"']?(.*?)[\"']?;/is", $configfile, $cmatch);
		foreach($cmatch[1] as $key => $var) {
			if(!in_array($var, array('database','adminemail','admincp'))) {
				$carray[$var] = $cmatch[2][$key];
			}
		}
		$clang = array(
		'dbhost' => '�ƾڮw�A�Ⱦ�',
		'dbuser' => '�ƾڮw�Τ�W',
		'dbpw' => '�ƾڮw�K�X',
		'dbname' => '�ƾڮw�W',
		'pconnect' => '�ƾڮw�O�_���[�s��',
		'cookiepre' => 'cookie �e��',
		'cookiedomain' => 'cookie �@�ΰ�',
		'cookiepath' => 'cookie �@�θ��|',
		'tablepre' => '��W�e��',
		'dbcharset' => 'MySQL�챵�r�Ŷ�',
		'charset' => '�׾¦r�Ŷ�',
		'headercharset' => '�j��׾­����ϥ��q�{�r�Ŷ�',
		'tplrefresh' => '�׾­���ҪO�۰ʨ�s�}��',
		'forumfounders' => '�׾³Щl�Huid',
		'dbreport' => '�O�_�o�e���~���i���޲z��',
		'errorreport' => '�O�_�̽��{�ǿ��~�H��',
		'attackevasive' => '�׾¨��m�ŧO',
		'admincp[\'forcesecques\']' => '�޲z�H���O�_�����]�m�w�����ݤ~��i�J�t�γ]�m',
		'admincp[\'checkip\']' => '��x�޲z�ާ@�O�_���Һ޲z���� IP',
		'admincp[\'tpledit\']' => '�O�_���\\�b�u�s��׾¼ҪO',
		'admincp[\'runquery\']' => '�O�_���\\��x�B�� SQL �y�y',
		'admincp[\'dbimport\']' => '�O�_���\\��x��_�׾¼ƾ�',
		);
		$comment = array(
		'pconnect' => '�D���[�s��',
		'cookiepre' => '���˴�',
		'cookiepath' => '���˴�',
		'charset' => '���˴�',
		'adminemail' => '���˴�',
		'admincp' => '�D�]�m��',
		);
		@mysql_connect($carray['dbhost'], $carray['dbuser'], $carray['dbpw']) or $mysql_errno = mysql_errno();
		!$mysql_errno && @mysql_select_db($carray['dbname']) or $mysql_errno = mysql_errno();
		$comment_error = "{$error_style_s}�X��{$style_e}";
		if ($mysql_errno == '2003') {
			$comment['dbhost'] = "{$error_style_s}�ݤf�]�m�X��{$style_e}";
		} elseif ($mysql_errno == '2005') {
			$comment['dbhost'] = $comment_error;
		} elseif ($mysql_errno == '1045') {
			$comment['dbuser'] = $comment_error;
			$comment['dbpw'] = $comment_error;
		} elseif ($mysql_errno == '1049') {
			$comment['dbname'] = $comment_error;
		} elseif (!empty($mysql_errno)) {
			$comment['dbhost'] = $comment_error;
			$comment['dbuser'] = $comment_error;
			$comment['dbpw'] = $comment_error;
			$comment['dbname'] = $comment_error;
		}
		$comment['pconnect'] = '�D���[�챵';
		$carray['pconnect'] == 1 && $comment['pconnect'] = '���[�s��';
		if ($carray['cookiedomain'] && substr($carray['cookiedomain'], 0, 1) != '.') {
			$comment['cookiedomain'] = "{$error_style_s}�ХH . �}�Y,���M�P�B�n���|�X��{$style_e}";
		}
		(!$mysql_errno && !mysql_num_rows(mysql_query('SHOW TABLES LIKE \''.$carray['tablepre'].'posts\''))) && $comment['tablepre'] = $comment_error;
		if (!$comment['tablepre'] && !$mysql_errno && @mysql_get_server_info() > '4.1') {
			$tableinfo = loadtable('threads');
			$dzdbcharset = substr($tableinfo['subject']['Collation'], 0, strpos($tableinfo['subject']['Collation'], '_'));
			if(!$carray['dbcharset'] && in_array(strtolower($carray['charset']), array('gbk', 'big5', 'utf-8'))) {
				$ckdbcharset = str_replace('-', '', $carray['charset']);
			} else {
				$ckdbcharset = $carray['dbcharset'];
			}
			if ($dzdbcharset != $ckdbcharset && $ckdbcharset != '') {
				$carray['dbcharset'] .= $error_style_s.'�X���A�z���׾¼ƾڮw�r�Ŷ��� '.$dzdbcharset.' �A�бN�����]�m�� '.$dzdbcharset.$style_e;
			}
		}
		if(!in_array($carray['charset'],array('gbk', 'big5', 'utf-8'))) {
			$carray['charset'] .= $error_style_s."  �X���A�ثe�r�Ŷ��u���'gbk', 'big5', 'utf-8'".$style_e;
		}
		if ($carray['headercharset'] == 0) {
			$comment['headercharset'] = $title_style_s.'���}��'.$title_style_e;
		} else {
			$comment['headercharset'] = $ok_style_s.'�}��'.$style_e;
		}
		if ($carray['tplrefresh'] == 0) {
			$comment['tplrefresh'] = $title_style_s.'����'.$title_style_e;
		} else {
			$comment['tplrefresh'] = $ok_style_s.'�}��'.$style_e;
		}
		if (preg_match('/[^\d,]/i', str_replace(' ', '', $carray['forumfounders']))) {
			$comment['forumfounders'] = $error_style_s.'�X���G�t���D�k�r�šA�Ӷ��]�m�u��t���Ʀr�M�b���r�� ,'.$style_e;
		} elseif(!$comment['tablepre'] && !$mysql_errno) {
			if ($carray['forumfounders']) {
				$founderarray = explode(',', str_replace(' ', '', $carray['forumfounders']));
				$adminids = $notadminids = '';
				$notadmin = 0;
				foreach($founderarray as $fdkey) {
					if (@mysql_result(@mysql_query("SELECT adminid FROM {$carray[tablepre]}members WHERE uid = '$fdkey' LIMIT 1"), 0) == 1) {
						$isadmin ++;
						$iscomma = $isadmin > 1 ? ',' : '';
						$adminids .= $iscomma.$fdkey;
					} else {
						$notadmin ++;
						$notcomma = $notadmin > 1 ? ',' : '';
						$notadminids .= $notcomma.$fdkey;
					}
				}
				if (!$isadmin) {
					$comment['forumfounders'] = $error_style_s.'�X���G�Щl�H���L�޲z��'.$style_e;
				} elseif ($notadmin) {
					$comment['forumfounders'] = $error_style_s.'ĵ�i�G�Щl�H�����D�޲z���Auid�p�U�G'.$notadminids.$style_e;
				}
			} else {
				$comment['forumfounders'] = $error_style_s.'ĵ�i�G�Щl�H�]�m���šA�h�Ӻ޲z���N�i�঳�w�����D'.$style_e;
			}
		}
		$comment['dbreport'] = $carray['dbreport'] == 0 ? '���o�e���~���i' : '�o�e���~���i';
		$comment['errorreport'] = $carray['errorreport'] == 1 ? '�̽��{�ǿ��~' : '���̽��{�ǿ��~';
		if (preg_match('/[^\d|]/i', str_replace(' ', '', $carray['attackevasive']))) {
			$carray['attackevasive'] .= $error_style_s.'�X���G�t���D�k�r��,�Ӷ��]�m�u��t���Ʀr�M�b���r��,'.$style_e;
		} else {
			if (preg_match('/[8]/i', $carray['attackevasive']) && @mysql_result(@mysql_query("SELECT COUNT(*) FROM {$carray[tablepre]}members")) < 1) {
				$carray['attackevasive'] .= $error_style_s.'�X���G�z�]�m�F�^�����D(8)�A�����K�[���Ұ��D�M���� ,'.$style_e;
			}
		}
		$comment_admincp_error = "�_ > {$error_style_s}ĵ�i�G���w�����w{$style_e}";
		$comment_admincp_ok = "�O > {$error_style_s}ĵ�i�G���w�����w{$style_e}";
		if ($carray['admincp[\'forcesecques\']'] == 1) {
			$comment['admincp[\'forcesecques\']'] = "{$ok_style_s}�O{$style_e}";
		} else {
			$comment['admincp[\'forcesecques\']'] = $comment_admincp_error;
		}
		if ($carray['admincp[\'checkip\']'] == 0) {
			$comment['admincp[\'checkip\']'] = $comment_admincp_error;
		} else {
			$comment['admincp[\'checkip\']'] = "{$ok_style_s}�O{$style_e}";
		}
		if ($carray['admincp[\'tpledit\']'] == 1) {
			$comment['admincp[\'tpledit\']'] = $comment_admincp_ok;
		} else {
			$comment['admincp[\'tpledit\']'] = "{$title_style_s}�_{$title_style_e}";
		}
		if ($carray['admincp[\'runquery\']'] == 1) {
			$comment['admincp[\'runquery\']'] = $comment_admincp_ok;
		} else {
			$comment['admincp[\'runquery\']'] = "{$title_style_s}�_{$title_style_e}";
		}
		if ($carray['admincp[\'dbimport\']'] == 1) {
			$comment['admincp[\'dbimport\']'] = $comment_admincp_ok;
		} else {
			$comment['admincp[\'dbimport\']'] = "{$title_style_s}�_{$title_style_e}";
		}
		foreach($carray as $key => $keyfield) {
			$clang[$key] == '' && $clang[$key] = '&nbsp;';
			strpos('comma'.$comment[$key], 'ĵ�i') && $comment[$key] = $comment[$key];
			strpos('comma'.$comment[$key], '�X��') && $comment[$key] = $comment[$key];
			$comment[$key] == '' && $comment[$key] = "{$ok_style_s}���`{$style_e}";
			if(in_array($key, array('dbuser', 'dbpw'))) {
				$keyfield = '**����**';
			}
			$keyfield == '' && $keyfield = '��';
			if(!in_array($key, array('dbhost','dbuser','dbpw','dbname'))) {
				if(in_array($key, array('pconnect', 'headercharset', 'tplrefresh', 'dbreport', 'errorreport', 'admincp[\'forcesecques\']', 'admincp[\'checkip\']', 'admincp[\'tpledit\']', 'admincp[\'runquery\']', 'admincp[\'dbimport\']'))) {
					$doctor_config .= "\n\t{$title_style_s}$key{$title_style_e} ---> $clang[$key] ---> $comment[$key]\n";
				} elseif(in_array($key, array('cookiepre', 'cookiepath', 'cookiedomain', 'charset', 'dbcharset', 'attackevasive'))) {
					$doctor_config .= "\n\t{$title_style_s}$key{$title_style_e} ---> $clang[$key] ---> $keyfield\n";
				} else {
					$doctor_config .= "\n\t{$title_style_s}$key{$title_style_e} ---> $clang[$key] ---> $keyfield ---> $comment[$key]\n";
				}
			} else {
				if(strstr($comment[$key], '�X��')) {
					strstr($doctor_config_db, '���`') && $doctor_config_db = '';
					$doctor_config_db .= "{$title_style_s}$key{$title_style_e} ---> $clang[$key] ---> $comment[$key]";
				} else {
					if(empty($doctor_config_db)) {
						$doctor_config_db ="\n\t{$ok_style_s}�ƾڮw���`�챵.{$style_e}";
					}
				}
			}
	
		}
		$doctor_config = "\n".$doctor_config_db.$doctor_config;
		//�������ҬO�_���DZ/SS�A�d�ݼƾڮw�M���r�Ŷ��A�ӷP�H�� charset,dbcharset, php,mysql,zend,php �u�аO
	
		$msg = '';
		$curr_os = PHP_OS;
	
		if(!function_exists('mysql_connect')) {
			$curr_mysql = $error_style_s.'�����'.$style_e;
			$msg .= "�z���A�Ⱦ������MySql�ƾڮw�A�L�k�w�˽׾µ{��";
			$quit = TRUE;
		} else {
			if(@mysql_connect($dbhost, $dbuser, $dbpw)) {
				$curr_mysql =  mysql_get_server_info();
			} else {
				$curr_mysql = $ok_style_s.'���'.$style_e;
			}
		}
		if(function_exists('mysql_connect')) {
			$authkeylink = @mysql_connect($dbhost, $dbuser, $dbpw);
			mysql_select_db($dbname, $authkeylink);
			$authkeyresult = mysql_result(mysql_query("SELECT `value` FROM {$tablepre}settings WHERE `variable`='authkey'", $authkeylink), 0);
			if($authkeyresult) {
				$authkeyexist = $ok_style_s.'�s�b'.$style_e;
			} else {
				$authkeyexist = $error_style_s.'���s�b'.$style_e;
			}
		}
		$curr_php_version = PHP_VERSION;
		if($curr_php_version < '4.0.6') {
			$msg .= "�z�� PHP �����p�� 4.0.6, �L�k�ϥ� Discuz! / SuperSite�C";
		}
	
		if(ini_get('allow_url_fopen')) {
			$allow_url_fopen = $ok_style_s.'���\\'.$style_e;
		} else {
			$allow_url_fopen = $title_style_s.'�����\\'.$title_style_e;
		}
		$max_execution_time = get_cfg_var('max_execution_time');
		$max_execution_time == 0 && $max_execution_time = '������';
	
		$memory_limit = get_cfg_var('memory_limit');
	
		$curr_server_software = $_SERVER['SERVER_SOFTWARE'];
	
		if(function_exists('ini_get')) {
			if(!@ini_get('short_open_tag')) {
				$curr_short_tag = $title_style_s.'�����\\'.$title_style_e;
				$msg .='�бN php.ini ���� short_open_tag �]�m�� On�A�_�h�L�k�ϥν׾¡C';
			} else {
				$curr_short_tag = $ok_style_s.'���\\'.$style_e;
			}
			if(@ini_get(file_uploads)) {
				$max_size = @ini_get(upload_max_filesize);
				$curr_upload_status = '�z�i�H�W�Ǫ��󪺳̤j�ؤo: '.$max_size;
			} else {
				$msg .= "����W�ǩά����ާ@�Q�A�Ⱦ��T��C";
			}
		} else {
			$msg .= 'php.ini���T�ΤFini_get()���.�������ҰѼƵL�k�˴�.';
		}
	
		if(!defined('OPTIMIZER_VERSION')) define('OPTIMIZER_VERSION','�S���w�˩Ϊ������C');
		if(OPTIMIZER_VERSION < 3.0) {
			$msg .="�z��ZEND�����C��3.0,�N�L�k�ϥ�SuperSite.";
		}
		//�{�ɥؿ����ˬd
		if(@is_writable(@ini_get('upload_tmp_dir'))){
			$tmpwritable = $ok_style_s.'�i�g'.$style_e;
		} elseif(!@ini_get('upload_tmp_dir') & @is_writable($_ENV[TEMP])) {
			$tmpwritable = $ok_style_s.'�i�g'.$style_e;
		} else {
			$tmpwritable = $title_style_s.'���i�g'.$title_style_e;
		}
	
		if(@ini_get('safe_mode') == 1) {
			$curr_safe_mode = $ok_style_s.'�}��'.$style_e;
		} else {
			$curr_safe_mode = $title_style_s.'����'.$title_style_e;
		}
		if(@diskfreespace('.')) {
			$curr_disk_space = intval(diskfreespace('.') / (1024 * 1024)).'M';
		} else {
			$curr_disk_space = '�L�k�˴�';
		}
		if(function_exists('xml_parser_create')) {
			$curr_xml = $ok_style_s.'�i��'.$style_e;
		} else {
			$curr_xml = $title_style_s.'���i��'.$title_style_e;
		}
	
		if(function_exists('file')) {
			$funcexistfile = $ok_style_s.'�s�b'.$style_e;
		} else {
			$funcexistfile = $title_style_s.'���s�b'.$title_style_e;
		}
	
		if(function_exists('fopen')) {
			$funcexistfopen = $ok_style_s.'�s�b'.$style_e;
		} else {
			$funcexistfopen = $title_style_s.'���s�b'.$title_style_e;
		}
	
		if(@ini_get('display_errors')) {
			$curr_display_errors = $ok_style_s.'�}��'.$style_e;
		} else {
			$curr_display_errors = $title_style_s.'����'.$title_style_e;
		}
		if(!function_exists('ini_get')) {
			$curr_display_errors = $tmpwritable = $curr_safe_mode = $curr_upload_status = $curr_short_tag = '�L�k�˴�';
		}
		//�ؿ��v���ˬd
		$envlogs = array();
		$entryarray = array (
		'attachments',
		'forumdata',
		'forumdata/threadcaches',
		'forumdata/logs',
		'forumdata/templates',
		'forumdata/cache',
		'customavatars',
		'forumdata/viewcount.log',
		'forumdata/dberror.log',
		'forumdata/errorlog.php',
		'forumdata/ratelog.php',
		'forumdata/cplog.php',
		'forumdata/modslog.php',
		'forumdata/illegallog.php'
		);
	
		foreach(array('templates', 'forumdata/logs', 'forumdata/cache', 'forumdata/templates') as $directory) {
			getdirentry($directory);
		}
		$fault = 0;
		foreach($entryarray as $entry) {
			$fullentry = './'.$entry;
			if(!is_dir($fullentry) && !file_exists($fullentry)) {
				continue;
			} else {
				if(!is_writeable($fullentry)) {
					$dir_perm .= "\n\t\t".(is_dir($fullentry) ? '�ؿ�' : '���')." ./$entry {$error_style_s}�L�k�g�J.{$style_e}";
					$msg .= "\n\t\t".(is_dir($fullentry) ? '�ؿ�' : '���')." ./$entry {$error_style_s}�L�k�g�J.{$style_e}";
					$fault = 1;
				}
			}
		}
		$dir_perm .= $fault ? '' : $ok_style_s.'���Υؿ��ݩʥ������T'.$style_e;
	
		/**
		 * gd�w�һݨ�ƪ��ˬd
		 */
		$gd_check = '';
		if(!extension_loaded('gd')) {
			$gd_check .= '�z��php.ini���}��extension=php_gd2.dll(windows)�Ϊ̥��sĶgd�w(linux).';
		} elseif(!function_exists('gd_info') && phpversion() < '4.3') {
			$gd_check .= 'php�����C��4.3.0�A�������������gd�w�A�Фɯűz��php����.';
		} else {
			$ver_info = gd_info();
			preg_match('/([0-9\.]+)/', $ver_info['GD Version'], $match);
			if($match[0] < '2.0') {
				$gd_check .= "\n\t\tgd�����C��2.0,�Фɯűz��gd�����H���gd�����ҽX�M���L.";
			} elseif(!(function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) ) {
				$gd_check .= "\n\t\tgd���������jpeg�����ҽX�M���L.";
			} elseif(!(function_exists('imagecreatefromgif') && function_exists('imagegif')) ) {
				$gd_check .= "\n\t\tgd���������gif�����ҽX�M���L.";
			} elseif(!(function_exists('imagecreatefrompng') && function_exists('imagepng')) ) {
				$gd_check .= "\n\t\tgd���������png�����ҽX�M���L.";
			} else {
				$gd_check .= '���`�}��';
			}
		}
		if($gd_check != '���`�}��') {
			$gd_check = $error_style_s.$gd_check.$style_e;
		} else {
			$gd_check = $ok_style_s.$gd_check.$style_e;
		}
	
		/**
		 * �ˬdming�w�A�ت����ˬd�O�_���flash���ҽX
		 */
		$ming_check = '';
		if(extension_loaded('ming')) {
			if(substr($curr_os,0,3) == 'WIN') {
				$ming_check .= '�z��php.ini���}��extension=php_ming.dll�A�ҥH�L�k���flash���ҽX';
			} else {
				$ming_check .= '�z���sĶming�w�A�ҥH�L�k���flash���ҽX';
			}
		} else {
			$ming_check .= '�z���t�Τ��flash���ҽX�A�p�G�ٵL�k�ϥ�flash���ҽX���ܡA���i��O�z��php�����ӧC';
		}
	
		/**
		 *�ˬd�t�άO�_�i�H����ImageMagick���R�O
		 */
		$imagemagick_check = '';
		if(!function_exists('exec')) {
			$imagemagick_check .='�z��php.ini�̩Ϊ̪Ŷ��ӸT��F�ϥ�exec��ơA�L�k�ϥ�ImageMagick';
		} else {
			$imagemagick_check .='�z�{�b�u�ݦw�˦nImageMagick�A�M��t�m�n�����ѼƴN�i�H�ϥ�ImageMagick(�ϥΤ��e�Х��ϥΫ�x���w���\\����ˬd�z��ImageMagick�O�_�w�˦n)';
		}
		if($msg == '') {
			$msg = "{$ok_style_s}�S���o�{�t�����Ұ��D.{$style_e}";
		} else {
			$msg = $error_style_s.$msg.$style_e;
		}
		$doctor_env = "
			�ާ@�t��--->$curr_os
	
			WEB ���� --->$curr_server_software
	
			PHP ����--->$curr_php_version
	
			MySQL ����--->$curr_mysql
	
			Zend ����--->".OPTIMIZER_VERSION."
	
			�{�ǳ̪��B��ɶ�(max_execution_time)--->{$max_execution_time}��
	
			���s�j�p(memory_limit)--->$memory_limit
	
			�O�_���\\���}���{���(allow_url_fopen)--->$allow_url_fopen
	
			�O�_���\\�ϥεu�аO(short_open_tag)--->$curr_short_tag
	
			�w���Ҧ�(safe_mode)--->$curr_safe_mode
	
			���~����(display_errors)--->$curr_display_errors
	
			XML �ѪR��--->$curr_xml
	
			authkey �O�_�s�b--->$authkeyexist
	
			�t���{�ɥؿ�--->$tmpwritable
	
			�ϽL�Ŷ�--->$curr_disk_space
	
			����W��--->$curr_upload_status
	
			��� file()--->$funcexistfile
	
			��� fopen()--->$funcexistfopen
	
			�ؿ��v��---$dir_perm
	
			GD �w--->$gd_check
	
			ming �w--->$ming_check
	
			ImageMagick --->$imagemagick_check
	
			�t�����ҿ��~����\r\n\t$msg";
	}
	if(!$doctor_step) {
		$doctor_step = '0';
		@unlink('./forumdata/doctor_cache.cache');
	}
	//php���~�ˬd
	$dberrnomsg = array (
		'1008' => '�ƾڮw���s�b�A�R���ƾڮw����',
		'1016' => '�L�k���}�ƾڤ��',
		'1041' => '�t�Τ��s����',
		'1045' => '�s���ƾڮw���ѡA�Τ�W�αK�X���~',
		'1046' => '��ܼƾڮw���ѡA�Х��T�t�m�ƾڮw�W��',
		'1044' => '��e�Τ�S���X�ݼƾڮw���v��',
		'1048' => '�r�q���ର��',
		'1049' => '�ƾڮw���s�b',
		'1051' => '�ƾڪ��s�b',
		'1054' => '�r�q���s�b',
		'1062' => '�r�q�ȭ��ơA�J�w����',//�����_
		'1064' => '�i���]�G1.�ƾڶW�����������ǰt�F2.�ƾڮw�O������',//�����_
		'1065' => '�L�Ī�SQL�y�y�ASQL�y�y����',//�����_
		'1081' => '����إ�Socket�s��',
		'1129' => '�ƾڮw�X�{���`�A�Э��Ҽƾڮw',
		'1130' => '�s���ƾڮw���ѡA�S���s���ƾڮw���v��',
		'1133' => '�ƾڮw�Τᤣ�s�b',
		'1141' => '��e�Τ�L�v�X�ݼƾڮw',
		'1142' => '��e�Τ�L�v�X�ݼƾڪ�',
		'1143' => '��e�Τ�L�v�X�ݼƾڪ����r�q',
		'1146' => '�ƾڪ��s�b',
		'1149' => 'SQL�y�y�y�k���~',
		'1169' => '�r�q�ȭ��ơA��s�O������',//�����_
		'2003' => '���ˬd�ƾڮw�A�Ⱦ��ݤf�]�m�O�_���T�A�q�{�ݤf�� 3306',
		'2005' => '�ƾڮw�A�Ⱦ����s�b',
		'1114' => 'Forum onlines reached the upper limit',
	);
	$display_errorall = '';
	$tempdir = $phpfile_array[$doctor_step];
	$dirname = $dir_array[$doctor_step];
	//foreach($phpfile_array as $tempdir=>$dirname) {
	$display_error = '';
	$mtime = explode(' ', microtime());
	$time_start = $mtime[1] + $mtime[0];
	if(!in_array($tempdir, array('templates', 'cache', 'discuzroot'))) exit('�Ѽƿ��~');
	
	$tempdir == 'discuzroot' ?  $dir = './' : $dir = 'forumdata/'.$tempdir.'/';
	create_checkfile();
	if (is_dir($dir)) {
		if ($dh = dir($dir)) {
			$PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
			$BASESCRIPT = basename($PHP_SELF);
			$host = htmlspecialchars($_SERVER['HTTP_HOST']);
			$boardurl = preg_replace("/\/+(api|archiver|wap)?\/*$/i", '', substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'))).'/';
			while (($file = $dh->read()) !== false) {
				if ($file != '.' && $file != '..' && $file != 'index.htm' && $file != 'checkfile.php' && $file != 'tools.php' && !is_dir($file)) {
					$extnum	=	strrpos($file, '.') + 1;
					$exts	=	strtolower(substr($file, $extnum));
					if($exts == 'php') {
						$content = '';
						if($dir == './') {
							$content = http_fopen($host, "{$boardurl}{$file}");
						} else {
							$content = http_fopen($host, "{$boardurl}/forumdata/checkfile.php?file=$file");
						}
						$content = str_replace(':  Call to undefined function:  ','',$content);
						$content = str_replace(':  Call to undefined function  ','',$content);
						$out = $out_mysql = array();
						if(preg_match_all("/<b>.+<\/b>:.* on line <b>\d+<\/b>/",$content,$out) || preg_match_all("/<b>Error<\/b>:.+<br \/>\n<b>Errno.<\/b>:\s{2}([1-9][0-9]+)/",$content,$out_mysql)) {
							$display_error .= "\t{$error_style_s}$file ---���~:{$style_e}";
							foreach ($out[0] as $value) {
								$display_error .= "\n\t\t".$value."\n";
							}
							foreach ($out_mysql[0] as $key =>$value) {
								$display_error .= "\n\t\t{$error_style_s}".$dberrnomsg[$out_mysql[1][$key]].$style_e;
								$display_error .= "\n\t\t".str_replace("\n", '', $value);
							}
						}
					}
				}
			}
			$dh->close();
		} else {
			echo "$dir�ؿ����s�b�Τ��iŪ��.";
		}
	}
	@unlink('./forumdata/checkfile.php');
	if($display_error == '') {
		$dot = '�w�s���';
		$dir == './' && $dot = 'php���';
		$display_errorall .= "\n---------{$ok_style_s}{$dirname}{$style_e}�U�S���˴��즳���~��$dot.\n";
	} else {
		$display_errorall .= "\n---------{$error_style_s}{$dirname}{$style_e}\n".$display_error;
	}
	$fp = @fopen('./forumdata/doctor_cache.cache', 'ab');
	@fwrite($fp, $display_errorall);
	@fclose($fp);
	
	if($doctor_step < $doctor_top) {
		$doctor_step ++;
		continue_redirect('dz_doctor', "&doctor_step=$doctor_step");
		htmlfooter();
	}
	$fp = @fopen('./forumdata/doctor_cache.cache','rb');
	$display_errorall = @fread($fp, @filesize('./forumdata/doctor_cache.cache'));
	@fclose($fp);
	@unlink('./forumdata/doctor_cache.cache');
	//}
	$display_errorall = str_replace('<b>', '', $display_errorall);
	$display_errorall = str_replace('</b>', '', $display_errorall);
	$display_errorall = str_replace('<br />', '', $display_errorall);
	$records_style = "\n\n==={$title_style_s}�t�m����ˬd{$title_style_e}=================================================$doctor_config\n==={$title_style_s}�t�������ˬd{$title_style_e}=================================================\n$doctor_env\n==={$title_style_s}�����~�ˬd{$title_style_e}=================================================\n$display_errorall\n==={$title_style_s}�ˬd����{$title_style_e}=====================================================";
	$search_style_all = array($error_style_s, $style_e, $ok_style_s, $title_style_s, $title_style_e);
	$replace_style_all = array('', '', '', '', '');
	$records = str_replace($search_style_all, '', $records_style);
	echo "<h4>�׾���ͶE�_���G</h4><br /><p id=records style=\"display:\"><textarea name=\"contents\" readonly=\"readonly\">$records</textarea><br><br><input value=\"�׾¼˦��N�X\" onclick=\"records.style.display='none';records_style.style.display='';\"  type=\"button\">  <input value=\"�N�N�X�ƻs��ڪ��Ť��O\" onclick=\"copytoclip($('contents'))\" type=\"button\"></p>
	<p id=records_style style=\"display:none\"><textarea name=\"contents_style\" readonly=\"readonly\">$records_style</textarea><br><br><input value=\"�M���˦��N�X\" onclick=\"records_style.style.display='none';records.style.display='';\"  type=\"button\"> <input value=\"�N�N�X�ƻs��ڪ��Ť��O\" onclick=\"copytoclip($('contents_style'))\" type=\"button\"></p>
	";
	htmlfooter();
} elseif ($action == 'dz_filecheck') {//�j���������
		//�j���������\\��
	if(!file_exists("./config.inc.php") && !file_exists("config.php")) {
		htmlheader();
		cexit("<h4>�Х��W��config���H�O�ұz���ƾڮw�ॿ�`�챵�I</h4>");
	}
	$do = isset($_GET['do']) ? $_GET['do'] : 'advance';
	
	$lang = array(
		'filecheck_fullcheck' => '�j���������',
		'filecheck_fullcheck_select' => '�j��������� - ��ܻݭn�j�����ؿ�',
		'filecheck_fullcheck_selectall' => '[�j�������ؿ�]',
		'filecheck_fullcheck_start' => '�}�l�ɶ�:',
		'filecheck_fullcheck_current' => '��e�ɶ�:',
		'filecheck_fullcheck_end' => '�����ɶ�:',
		'filecheck_fullcheck_file' => '��e���:',
		'filecheck_fullcheck_foundfile' => '�o�{��������: ',
		'filecheck_fullcheck_nofound' => '�S���o�{���󥼪����'
	);
	if(!$discuzfiles = @file('./admin/discuzfiles.md5')) {
		show_tools_message('�S�������MD5��');
	}
	htmlheader();
	if($do == 'advance') {
		$dirlist = array();
		$starttime = date('Y-m-d H:i:s');
		$cachelist = $templatelist = array();
		if(empty($checkdir)) {
			checkdirs('./');
		} elseif($checkdir == 'all') {
			echo "\n<script>var dirlist = ['./'];var runcount = 0;var foundfile = 0</script>";
		} else {
			$checkdir = str_replace('..', '', $checkdir);
			$checkdir = $checkdir{0} == '/' ? '.'.$checkdir : $checkdir;
			checkdirs($checkdir.'/');
			echo "\n<script>var dirlist = ['$checkdir/'];var runcount = 0;var foundfile = 0</script>";
		}
		echo '<h4>�j���������</h4>
			<table>
			<tr><th class="specialtd">'.(empty($checkdir) ? '<a href="tools.php?action=dz_filecheck&do=advance&start=yes&checkdir=all">'.$lang['filecheck_fullcheck_selectall'].'</a>' : $lang['filecheck_fullcheck'].($checkdir != 'all' ? ' - '.$checkdir : '')).'</th></tr>
			<script language="JavaScript" src="include/javascript/common.js"></script>';
		if(empty($checkdir)) {
			echo '<tr><td class="specialtd"><br><ul>';
			foreach($dirlist as $dir) {
				$subcount = count(explode('/', $dir));
				echo '<li>'.str_repeat('-', ($subcount - 2) * 4);
				echo '<a href="tools.php?action=dz_filecheck&do=advance&start=yes&checkdir='.rawurlencode($dir).'">'.basename($dir).'</a></li>';
			}
			echo '</ul></td></tr></table><br />';
		} else {
			
			echo '<tr><td>'.$lang['filecheck_fullcheck_start'].' '.$starttime.'<br><span id="msg"></span><br /><br /><div id="checkresult"></div></td></tr></table><br />
				<iframe name="checkiframe" id="checkiframe" style="display: none"></iframe>';
			echo "<script>checkiframe.location = 'tools.php?action=dz_filecheck&do=advancenext&start=yes&dir=' + dirlist[runcount];</script>";
		}
		htmlfooter();
	} elseif($do == 'advancenext') {
		
		$nopass = 0;
		foreach($discuzfiles as $line) {
			$md5files[] = trim(substr($line, 34));
		}
		$foundfile = checkfullfiles($dir);
		echo "<script>";
		if($foundfile) {
			echo "parent.foundfile += $foundfile;";
		}
		echo "parent.runcount++;
		if(parent.dirlist.length > parent.runcount) {
			parent.checkiframe.location = 'tools.php?action=dz_filecheck&do=advancenext&start=yes&dir=' + parent.dirlist[parent.runcount];
		} else {
			var msg = '';
			msg = '$lang[filecheck_fullcheck_end] ".addslashes(date('Y-m-d H:i:s'))."';
			if(parent.foundfile) {
				msg += '<br>$lang[filecheck_fullcheck_foundfile] ' + parent.foundfile;
			} else {
				msg += '<br>$lang[filecheck_fullcheck_nofound]';
			}
			parent.$('msg').innerHTML = msg;
		}</script>";
		exit;
	}
} elseif ($action == 'dz_mysqlclear') {//�ƾڮw�M�z
	ob_implicit_flush();
	define('IN_DISCUZ', TRUE);
	if(@!include("./config.inc.php")) {
		if(@!include("./config.php")) {
			htmlheader();
			cexit("<h4>�Х��W��config���H�O�ұz���ƾڮw�ॿ�`�챵�I</h4>");
		}
	}
	require './include/db_'.$database.'.class.php';
	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	$db->select_db($dbname);

	if(!get_cfg_var('register_globals')) {
		@extract($_GET, EXTR_SKIP);
	}
	$rpp			=	"1000"; //�C���B�z�h�ֱ��ƾ�
	$totalrows		=	isset($totalrows) ? $totalrows : 0;
	$convertedrows		=	isset($convertedrows) ? $convertedrows : 0;
	$start			=	isset($start) && $start > 0 ? $start : 0;
	$sqlstart		=	isset($start) && $start > $convertedrows ? $start - $convertedrows : 0;
	$end			=	$start + $rpp - 1;
	$stay			=	isset($stay) ? $stay : 0;
	$converted		=	0;
	$step			=	isset($step) ? $step : 0;
	$info			=	isset($info) ? $info : '';
	$action			=	array(
						'1'=>'���E�^�ƼƾڲM�z',
						'2'=>'���E����ƾڲM�z',
						'3'=>'���E�|���ƾڲM�z',
						'4'=>'���E�O���ƾڲM�z',
						'5'=>'�D�D�H���M�z',
						'6'=>'�����ƾڤ��E�M�z'
					);
	$steps			=	count($action);
	$actionnow		=	isset($action[$step]) ? $action[$step] : '����';
	$maxid			=	isset($maxid) ? $maxid : 0;
	$tableid		=	isset($tableid) ? $tableid : 1;
	htmlheader();
	if($step==0){
	?>
		<h4>�ƾڮw���l�ƾڲM�z</h4>
		<h5>�M�z���ظԲӫH��</h5>
		<table>
		<tr><th width="30%">Posts���M�z</th><td>[<a href="?action=dz_mysqlclear&step=1&stay=1">��B�M�z</a>]</td></tr>
		<tr><th width="30%">Attachments���M�z</th><td>[<a href="?action=dz_mysqlclear&step=2&stay=1">��B�M�z</a>]</td></tr>
		<tr><th width="30%">Members���M�z</th><td>[<a href="?action=dz_mysqlclear&step=3&stay=1">��B�M�z</a>]</td></tr>
		<tr><th width="30%">Forums���M�z</th><td>[<a href="?action=dz_mysqlclear&step=4&stay=1">��B�M�z</a>]</td></tr>
		<tr><th width="30%">Threads���M�z</th><td>[<a href="?action=dz_mysqlclear&step=5&stay=1">��B�M�z</a>]</td></tr>
		<tr><th width="30%">�Ҧ����M�z</th><td>[<a href="?action=dz_mysqlclear&step=1&stay=0">�����M�z</a>]</td></tr>
		</table>
	<?php
	specialdiv();
	} elseif ($step == '1'){
		if($start == 0) {
			validid('pid','posts');
		}
		$query = "SELECT pid, tid FROM {$tablepre}posts WHERE pid >= $start AND pid <= $end";
		$posts=$db->query($query);
			while ($post = $db->fetch_array($posts)){
				$query = $db->query("SELECT tid FROM {$tablepre}threads WHERE tid='".$post['tid']."'");
				if ($db->result($query, 0)) {
					} else {
						$convertedrows ++;
						$db->query("DELETE FROM {$tablepre}posts WHERE pid='".$post['pid']."'");
					}
				$converted = 1;
				$totalrows ++;
		}
			if($converted || $end < $maxid) {
				continue_redirect();
			} else {
				stay_redirect();
			}
	} elseif ($step == '2'){
		if($start == 0) {
			validid('aid','attachments');
		}
		$query = "SELECT aid,pid,attachment FROM {$tablepre}attachments WHERE aid >= $start AND aid <= $end";
		$posts=$db->query($query);
			while ($post = $db->fetch_array($posts)){
				$query = $db->query("SELECT pid FROM {$tablepre}posts WHERE pid='".$post['pid']."'");
				if ($db->result($query, 0)) {
					} else {
						$convertedrows ++;
						$db->query("DELETE FROM {$tablepre}attachments WHERE aid='".$post['aid']."'");
						$attachmentdir = TOOLS_ROOT.'./attachments/';
						@unlink($attachmentdir.$post['attachment']);
					}
				$converted = 1;
				$totalrows ++;
		}
			if($converted || $end < $maxid) {
				continue_redirect();
			} else {
				stay_redirect();
			}
	} elseif ($step == '3'){
		if($start == 0) {
			validid('uid','memberfields');
		}
		$query = "SELECT uid FROM {$tablepre}memberfields WHERE uid >= $start AND uid <= $end";
		$posts=$db->query($query);
			while ($post = $db->fetch_array($posts)){
				$query = $db->query("SELECT uid FROM {$tablepre}members WHERE uid='".$post['uid']."'");
					if ($db->result($query, 0)) {
					} else {
						$convertedrows ++;
						$db->query("DELETE FROM {$tablepre}memberfields WHERE uid='".$post['uid']."'");
					}
				$converted = 1;
				$totalrows ++;
		}
			if($converted || $end < $maxid) {
				continue_redirect();
			} else {
				stay_redirect();
			}
	} elseif ($step == '4'){
		if($start == 0) {
			validid('fid','forumfields');
		}
		$query = "SELECT fid FROM {$tablepre}forumfields WHERE fid >= $start AND fid <= $end";
		$posts=$db->query($query);
			while ($post = $db->fetch_array($posts)){
				$query = $db->query("SELECT fid FROM {$tablepre}forums WHERE fid='".$post['fid']."'");
				if ($db->result($query, 0)) {
					} else {
						$convertedrows ++;
						$db->query("DELETE FROM {$tablepre}forumfields WHERE fid='".$post['fid']."'");
					}
				$converted = 1;
				$totalrows ++;
		}
			if($converted || $end < $maxid) {
				continue_redirect();
			} else {
				stay_redirect();
			}
	} elseif ($step == '5'){
		if($start == 0) {
			validid('tid','threads');
		}
		$query = "SELECT tid, subject FROM {$tablepre}threads WHERE tid >= $start AND tid <= $end";
		$posts=$db->query($query);
			while ($threads = $db->fetch_array($posts)){
				$query = $db->query("SELECT COUNT(*) FROM {$tablepre}posts WHERE tid='".$threads['tid']."' AND invisible='0'");
				$replynum = $db->result($query, 0) - 1;
				if ($replynum < 0) {
					$db->query("DELETE FROM {$tablepre}threads WHERE tid='".$threads['tid']."'");
				} else {
					$query = $db->query("SELECT a.aid FROM {$tablepre}posts p, {$tablepre}attachments a WHERE a.tid='".$threads['tid']."' AND a.pid=p.pid AND p.invisible='0' LIMIT 1");
					$attachment = $db->num_rows($query) ? 1 : 0;//�״_����
					$query  = $db->query("SELECT pid, subject, rate FROM {$tablepre}posts WHERE tid='".$threads['tid']."' AND invisible='0' ORDER BY dateline LIMIT 1");
					$firstpost = $db->fetch_array($query);
					$firstpost['subject'] = trim($firstpost['subject']) ? $firstpost['subject'] : $threads['subject']; //�w��Y���ഫ�L�Ӫ��׾ª��B�z
					$firstpost['subject'] = addslashes($firstpost['subject']);
					@$firstpost['rate'] = $firstpost['rate'] / abs($firstpost['rate']);//�״_�o��
					$query  = $db->query("SELECT author, dateline FROM {$tablepre}posts WHERE tid='".$threads['tid']."' AND invisible='0' ORDER BY dateline DESC LIMIT 1");
					$lastpost = $db->fetch_array($query);//�״_�̫�o��
					$db->query("UPDATE {$tablepre}threads SET subject='".$firstpost['subject']."', replies='$replynum', lastpost='".$lastpost['dateline']."', lastposter='".addslashes($lastpost['author'])."', rate='".$firstpost['rate']."', attachment='$attachment' WHERE tid='".$threads['tid']."'", 'UNBUFFERED');
					$db->query("UPDATE {$tablepre}posts SET first='1', subject='".$firstpost['subject']."' WHERE pid='".$firstpost['pid']."'", 'UNBUFFERED');
					$db->query("UPDATE {$tablepre}posts SET first='0' WHERE tid='".$threads['tid']."' AND pid<>'".$firstpost['pid']."'", 'UNBUFFERED');
					$convertedrows ++;
				}
				$converted = 1;
				$totalrows ++;
			}
			if($converted || $end < $maxid) {
				continue_redirect();
			} else {
				stay_redirect();
			}
	} elseif ($step=='6'){
		echo '<h4>�ƾڮw���l�ƾڲM�z</h4><table>
			  <tr><th>�������l�ƾڲM�z</th></tr><tr>
			  <td><br>�Ҧ��ƾڲM�z�ާ@����.&nbsp;�@�B�z<font color=red>'.$allconvertedrows.'</font>���ƾ�.<br><br></td></tr></table>';
	}
	htmlfooter();
	
} elseif ($action == 'dz_repair_auto') {//�״_�ۼW��ID
	@set_time_limit(0);
	htmlheader();
	echo '<h4>Discuz! �ۼW���r�q�״_ </h4>';
	$querysql = array(
		'activityapplies' => 'applyid',
		'adminnotes' => 'id',
		'advertisements' => 'advid',
		'announcements' => 'id',
		'attachments' => 'aid',
		'attachtypes' => 'id',
		'banned' => 'id',
		'bbcodes' => 'id',
		'crons' => 'cronid',
		'faqs' => 'id',
		'forumlinks' => 'id',
		'forums' => 'fid',
		'itempool' => 'id',
		'magicmarket' => 'mid',
		'magics' => 'magicid',
		'medals' => 'medalid',
		'members' => 'uid',
		'pluginhooks' => 'pluginhookid',
		'plugins' => 'pluginid',
		'pluginvars' => 'pluginvarid',
		'pms' => 'pmid',
		'pmsearchindex' => 'searchid',
		'polloptions' => 'polloptionid',
		'posts' => 'pid',
		'profilefields' => 'fieldid',
		'projects' => 'id',
		'ranks' => 'rankid',
		'searchindex' => 'searchid',
		'smilies' => 'id',
		'styles' => 'styleid',
		'stylevars' => 'stylevarid',
		'templates' => 'templateid',
		'threads' => 'tid',
		'threadtypes' => 'typeid',
		'words' => 'id'
	);
	$sqladd = array(
		'imagetypes' => 'typeid',
		'tradecomments' => 'id',
		'typemodels' => 'id',
		'typeoptions' => 'optionid'
	);
	define('IN_DISCUZ', TRUE);
	if(@include TOOLS_ROOT.'./discuz_version.php') {
		if(substr(DISCUZ_VERSION, 0, 1) == 6) {
			$querysql = array_merge($querysql, $sqladd);
		}else if(substr(DISCUZ_VERSION, 0, 3) != '5.5') {
			errorpage("<h4>�ܩ�p�A�ӥ\\��ثe�u���Discuz!5.5�����MDiscuz!6.0�����C</h4>",'',0);
		}
	}else {
		errorpage("./discuz_version.php��󤣦s�b�A�нT�w�Ӥ�󪺦s�b�C",'',0);
	}
	echo '<h5>�ˬd���G</h5>
	<table>
		<tr><th width="25%">�ƾڪ�W</th><th width="25%">�r�q�W</th><th width="25%">�ۼW�����A</th></tr>';
	foreach($querysql as $key => $keyfield) {
		$tablestate = '���`';
		echo '<tr><td width="25%">'.$tablepre.$key.'</td><td width="25%">'.$keyfield.'</td>';
		if($query = @mysql_query("Describe $tablepre$key $keyfield")) {
			if(@mysql_num_rows($query) > 0) {
				$field = @mysql_fetch_array($query);
				if($field[3] != 'PRI') {
					@mysql_query("ALTER TABLE $tablepre$key ADD PRIMARY KEY ($keyfield)");
					$tablestate = '<font color="green"><b>�w�g�״_</b></font>';
				}
				if(empty($field[5])) {
					mysql_query("ALTER TABLE $tablepre$key CHANGE $keyfield $keyfield $field[1] NOT NULL AUTO_INCREMENT");
					$tablestate = '<font color="green"><b>�w�g�״_</b></font>';
				}
			} else {
				$tablestate = '<font color="red">�r�q���s�b</font>';
			}
		} else {
			$tablestate = '<font color="red">���s�b</font>';
		}
		echo '<td width="25%">'.$tablestate.'</td></tr>';
	}
	echo '</table>';
	specialdiv();
	echo '<br />';
	htmlfooter();
	
} elseif ($action == 'dz_replace') {//���e����
	
	htmlheader();
	$rpp			=	"500"; //�C���B�z�h�ֱ��ƾ�
	$totalrows		=	isset($totalrows) ? $totalrows : 0;
	$convertedrows	=	isset($convertedrows) ? $convertedrows : 0;
	$start			=	isset($start) && $start > 0 ? $start : 0;
	$end			=	$start + $rpp - 1;
	$converted		=	0;
	$maxid			=	isset($maxid) ? $maxid : 0;
	$threads_mod	=	isset($threads_mod) ? $threads_mod : 0;
	$threads_banned =	isset($threads_banned) ? $threads_banned : 0;
	$posts_mod		=	isset($posts_mod) ? $posts_mod : 0;
	if($stop == 1) {
		echo "<h4>���l���e��q����</h4><table>
					<tr>
						<th>�Ȱ�����</th>
					</tr>";
		$threads_banned > 0 && print("<tr><td><br><li>".$threads_banned."�ӥD�D�Q��J�^����.</li><br></td></tr>");
		$threads_mod > 0 && print("<tr><td><br><li>".$threads_mod."�ӥD�D�Q��J�f�֦C��.</li><br></td></tr>");
		$posts_mod > 0 && print("<tr><td><br><li>".$posts_mod."�Ӧ^�_�Q��J�f�֦C��.</li><br></td></tr>");
		echo "<tr><td><br><li>�����F".$convertedrows."�ө��l</li><br><br></td></tr>";
		echo "<tr><td><br><a href='?action=dz_replace&step=".$step."&start=".($end + 1 - $rpp * 2)."&stay=$stay&totalrows=$totalrows&convertedrows=$convertedrows&maxid=$maxid&replacesubmit=1&threads_banned=$threads_banned&threads_mod=$threads_mod&posts_mod=$posts_mod'>�~��</a><br><br></td></tr>";
		echo "</table>";
		htmlfooter();
	}
	ob_implicit_flush();
	define('IN_DISCUZ', TRUE);
	if(@!include("./config.inc.php")) {
		if(@!include("./config.php")) {
			cexit("<h4>�Х��W��config���H�O�ұz���ƾڮw�ॿ�`�챵�I</h4>");
		}
	}
	require './include/db_'.$database.'.class.php';
	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	$db->select_db($dbname);
	$selectwords_cache = './forumdata/cache/selectwords_cache.php';
	if(isset($replacesubmit) || $start > 0) {
	if($maxid ==0) {
		validid('pid','posts');
	}
		if(!file_exists($selectwords_cache) || is_array($selectwords)){
			if(count($selectwords) < 1) {
				echo "<h4>���l���e��q����</h4><table><tr><th>���ܫH��</th></tr><tr><td>�z�٨S����ܭn�L�o�����y. &nbsp [<a href=tools.php?action=dz_replace>��^</a>]</td></tr></table>";
				htmlfooter();
			} else {
				$fp = @fopen($selectwords_cache,w);
				$content = "<?php \n";
				$selectwords = implode(',',$selectwords);
				$content .= "\$selectwords = '$selectwords';\n?>";
				if(!@fwrite($fp,$content)) {
					echo "�g�J�w�s���$selectwords_cache ���~,�нT�{���|�O�_�i�g. &nbsp [<a href=tools.php?action=dz_replace>��^</a>]";
					htmlfooter();
				} else {
					require_once "$selectwords_cache";
				}
				@fclose($fp);
			}
		} else {
			require_once "$selectwords_cache";
		}
		$array_find = $array_replace = $array_findmod = $array_findbanned = array();
		$query = $db->query("SELECT find,replacement from {$tablepre}words where id in($selectwords)");//��o�{���W�h{BANNED}��^���� {MOD}��i�f�֦C��
		while($row = $db->fetch_array($query)) {
			$find = preg_quote($row['find'], '/');
			$replacement = $row['replacement'];
			if($replacement == '{BANNED}') {
				$array_findbanned[] = $find;
			} elseif($replacement == '{MOD}') {
				$array_findmod[] = $find;
			} else {
				$array_find[] = $find;
				$array_replace[] = $replacement;
			}
		}
		function topattern_array($source_array) { //�N�Ʋե��h��
			$source_array = preg_replace("/\{(\d+)\}/",".{0,\\1}",$source_array);
			foreach($source_array as $key => $value) {
				$source_array[$key] = '/'.$value.'/i';
			}
			return $source_array;
		}
		$array_find = topattern_array($array_find);
		$array_findmod = topattern_array($array_findmod);
		$array_findbanned = topattern_array($array_findbanned);

		//�d��posts��ǳƴ���
		$sql = "SELECT pid, tid, first, subject, message from {$tablepre}posts where pid >= $start and pid <= $end";
		$query = $db->query($sql);
		while($row = $db->fetch_array($query)) {
			$pid = $row['pid'];
			$tid = $row['tid'];
			$subject = $row['subject'];
			$message = $row['message'];
			$first = $row['first'];
			$displayorder = 0;//  -2�f�� -1�^����
			if(count($array_findmod) > 0) {
				foreach($array_findmod as $value){
					if(preg_match($value,$subject.$message)){
						$displayorder = '-2';
						break;
					}
				}
			}
			if(count($array_findbanned) > 0) {
				foreach($array_findbanned as $value){
					if(preg_match($value,$subject.$message)){
						$displayorder = '-1';
						break;
					}
				}
			}
			if($displayorder < 0) {
				if($displayorder == '-2' && $first == 0) {//�p���ߴN����f�֦^�_
					$posts_mod ++;
					$db->query("UPDATE {$tablepre}posts SET invisible = '$displayorder' WHERE pid = $pid");
				} else {
					if($db->affected_rows($db->query("UPDATE {$tablepre}threads SET displayorder = '$displayorder' WHERE tid = $tid and displayorder >= 0")) > 0) {
						$displayorder == '-2' && $threads_mod ++;
						$displayorder == '-1' && $threads_banned ++;
					}
				}
			}
			$subject = preg_replace($array_find,$array_replace,addslashes($subject));
			$message = preg_replace($array_find,$array_replace,addslashes($message));
			if($subject != addslashes($row['subject']) || $message != addslashes($row['message'])) {
				if($db->query("UPDATE {$tablepre}posts SET subject = '$subject', message = '$message' WHERE pid = $pid")) {
					$convertedrows ++;
				}
			}
			$converted = 1;
		}
		if($converted  || $end < $maxid) {
			continue_redirect('dz_replace',"&replacesubmit=1&threads_banned=$threads_banned&threads_mod=$threads_mod&posts_mod=$posts_mod");
		} else {
			echo "<h4>���l���e��q����</h4><table>
						<tr>
							<th>��q��������</th>
						</tr>";
			$threads_banned > 0 && print("<tr><td><br><li>".$threads_banned."�ӥD�D�Q��J�^����.</li><br></td></tr>");
			$threads_mod > 0 && print("<tr><td><br><li>".$threads_mod."�ӥD�D�Q��J�f�֦C��.</li><br></td></tr>");
			$posts_mod > 0 && print("<tr><td><br><li>".$posts_mod."�Ӧ^�_�Q��J�f�֦C��.</li><br></td></tr>");
			echo "<tr><td><br><li>�����F".$convertedrows."�ө��l</li><br><br></td></tr>";
			echo "</table>";
			@unlink($selectwords_cache);
		}
	} else {
		if($db->version > '4.1'){
			$serverset = 'character_set_connection=gbk, character_set_results=gbk, character_set_client=binary';
			$serverset && $db->query("SET $serverset");
		}
		$query = $db->query("select * from {$tablepre}words");
		$i = 1;
		if($db->num_rows($query) < 1) {
			echo "<h4>���l���e��q����</h4><table><tr><th>���ܫH��</th></tr><tr><td><br>�藍�_,�{�b�٨S���L�o�W�h,��<a href=\"./admincp.php?action=censor\" target='_blank'>�i�J�׾«�x�]�m</a>.<br><br></td></tr></table>";
			htmlfooter();
		}
	?>
		<form method="post" action="tools.php?action=dz_replace">
		<script language="javascript">
			function checkall(form, prefix, checkall) {
				var checkall = checkall ? checkall : 'chkall';
				for(var i = 0; i < form.elements.length; i++) {
					var e = form.elements[i];
					if(e.name != checkall && (!prefix || (prefix && e.name.match(prefix)))) {
						e.checked = form.elements[checkall].checked;
					}
				}
			}
		</script>
				<h4>��q�������l���e</h4>
				<table>
					<tr>
						<th><input class="checkbox" name="chkall" onclick="checkall(this.form)" type="checkbox" checked>�Ǹ�</th>
						<th>���}���y</th>
						<th>������</th></tr>
					<?
						while($row = $db->fetch_array($query)) {
					?>
					<tr>
						<td><input class="checkbox" name="selectwords[]" value="<?=$row['id']?>" type="checkbox" checked>&nbsp <?=$i++?></td>
						<td>&nbsp <?=$row['find']?></td>
						<td>&nbsp <?=stripslashes($row['replacement'])?></td>
					</tr>
					<?}?>
				</table>
				<input type="submit" name=replacesubmit value="�}�l����">
		</form>
	<div class="specialdiv">
	<h6>�`�N�G</h6>
	<ul>
	<li>���{�Ƿ|���ӽ׾²{���L�o�W�h�ާ@�Ҧ����l���e.�p�ݭק��<a href="./admincp.php?action=censor" target='_blank'>�i�׾«�x</a>�C</li>
	<li>�W��C�X�F�z�׾·�e���L�o���y.</li>
	</ul></div><br><br>
	<?
	}
	htmlfooter();
} elseif ($action == 'dz_updatecache') {//��s�w�s
	$cachedir = array('cache','templates');
	$clearmsg = '';
	foreach($cachedir as $dir) {
		if($dh = dir('./forumdata/'.$dir)) {
			while (($file = $dh->read()) !== false) {
				if ($file != "." && $file != ".." && $file != "index.htm" && !is_dir($file)) {
					unlink('./forumdata/'.$dir.'/'.$file);
				}
			}
		} else {
			$clearmsg .= './forumdata/'.$dir.'�M������.<br>';
		}
	}
	htmlheader();
	echo '<h4>��s�w�s</h4><table><tr><th>���ܫH��</th></tr><tr><td>';
	if($clearmsg == '') $clearmsg = '��s�w�s����.';
	echo $clearmsg.'</td></tr></table>';
	htmlfooter();
} elseif ($action == 'all_setadmin') {//���m�޲z���b���K�X�A
	if($whereis == 'is_dz') {
		$sql_findadmin = "SELECT * FROM {$tablepre}members WHERE adminid=1";
		$sql_select = "SELECT uid FROM {$tablepre}members WHERE $_POST[loginfield] = '$_POST[where]'";		$username = 'username';
		$uid = 'uid';
		
		if(UC_CONNECT == 'mysql' || $dz_version < 610) {//�P�_�s��ucenter���覡�A�p�G�Omysql�覡�A�i�H�ק�K�X�A�_�h���ܥhuc��x�ק�K�X
			$rspw = 1;
			
		} else {
			$rspw = 0;
		}
		if($dz_version<700){//�O�_�s�b�w���ݵ� 7.0�H��w���ݵ���b�Τᤤ�ߤ�
			$secq = 1;
		}elseif($rspw){
			$secq = 1;
		}else{
			$secq = 0;
		}
	} elseif($whereis == 'is_uc') {
		$secq = 0;
		$rspw = 1;
	} elseif($whereis == 'is_ec') {
		$sql_findadmin = "SELECT * FROM {$tablepre}admin_user";
		$sql_select = "SELECT user_id FROM {$tablepre}admin_user WHERE $_POST[loginfield] = '$_POST[where]'";
		$sql_update = "";
		$sql_rspw = "UPDATE {$tablepre}admin_user SET password='".md5($_POST['password'])."' WHERE $_POST[loginfield] = '$_POST[where]' limit 1";
		$username = 'user_name';
		$uid = 'user_id';
		$secq = 0;
		$rspw = 1;
	} elseif($whereis == 'is_uch') {
		$sql_findadmin = "SELECT * FROM {$tablepre}space WHERE groupid = 1";
		$sql_select = "SELECT uid FROM {$tablepre}space WHERE $_POST[loginfield] = '$_POST[where]'";
		$sql_update = "UPDATE {$tablepre}space SET groupid='1' WHERE $_POST[loginfield] = '$_POST[where]'";
		$username = 'username';
		$uid = 'uid';
		$secq = 0;
		if(UC_CONNECT == 'mysql') {
			$rspw = 1;
		} else {
			$rspw = 0;
		}
	}elseif($whereis == 'is_ss' && $ss_version>=70){
		$sql_findadmin = "SELECT * FROM {$tablepre}members WHERE groupid = 1";
		$sql_select = "SELECT uid FROM {$tablepre}members WHERE $loginfield = '$where'";
		$sql_update = "UPDATE {$tablepre}members SET groupid='1' WHERE $loginfield = '$where'";
		$username = 'username';
		$uid = 'uid';
		$secq = 0;
		if(UC_CONNECT == 'mysql') {
			$rspw = 1;
		} else {
			$rspw = 0;
		}

	}
	$info = "";
	$info_uc = "";
	htmlheader();
	?>
	<h4>��^�޲z��</h4>
	<?php
		//�d�ߤw�g�s�b���޲z��
		if($whereis != 'is_uc') {
			$findadmin_query = mysql_query($sql_findadmin);
			$admins = '';
			while($findadmins = mysql_fetch_array($findadmin_query)) {
				$admins .= ' '.$findadmins[$username];
			}
		}
	if(!empty($_POST['loginsubmit'])) {
		if($whereis == 'is_uc') {
			define(ROOT_DIR,dirname(__FILE__)."/");
			$configfile = ROOT_DIR."./data/config.inc.php";
			$uc_password = $_POST["password"];
			$salt = substr(uniqid(rand()), 0, 6);
			if(!$uc_password){
				$info = "�K�X���ର��";
			}else{
				$md5_uc_password = md5(md5($uc_password).$salt);
				$config = file_get_contents($configfile);
				$config = preg_replace("/define\('UC_FOUNDERSALT',\s*'.*?'\);/i", "define('UC_FOUNDERSALT', '$salt');", $config);
				$config = preg_replace("/define\('UC_FOUNDERPW',\s*'.*?'\);/i", "define('UC_FOUNDERPW', '$md5_uc_password');", $config);
				$fp = @fopen($configfile, 'w');
				@fwrite($fp, $config);
				@fclose($fp);
				$info = "UCenter�Щl�H�K�X��令�\\���G$uc_password";
			}
		}else {
			if(@mysql_num_rows(mysql_query($sql_select)) < 1) {
				if($whereis == 'is_ec') {
					$info = '<font color="red">�L���޲z���Τ�I���ˬd�Τ�W�O�_���T�C</font>��<a href="?action=all_setadmin">���s��J</a> �޲z���b��.<br><br>';
				} else {
					$info = '<font color="red">�L���Τ�I���ˬd�Τ�W�O�_���T�C</font>��<a href="?action=all_setadmin">���s��J</a> �Ϊ̭��s���U.<br><br>';
				}
			} else {
				if($whereis == 'is_dz') {
					$sql_update1 = "UPDATE {$tablepre}members SET adminid='1', groupid='1' WHERE $_POST[loginfield] = '$_POST[where]' limit 1";
					$sql_update2 = "UPDATE {$tablepre}members SET adminid='1', groupid='1',secques='' WHERE $_POST[loginfield] = '$_POST[where]' limit 1";
					$sql_update = $_POST['issecques'] ? $sql_update2 : $sql_update1;
				}
				if($whereis == 'is_ss'){
					$sql_update1 = "UPDATE {$tablepre}members SET  groupid='1' WHERE $_POST[loginfield] = '$_POST[where]' limit 1";
					$sql_update =  $sql_update1;
				}
				if(mysql_query($sql_update)&& !$rspw) {
					$_POST[loginfield] = $_POST[loginfield] == $username ? '�Τ�W' : 'UID���X';
					$info = "�w�N$_POST[loginfield]�� $_POST[where] ���Τ�]�m���޲z���C<br><br>";
				}
				if($rspw) {
					if($whereis == 'is_ec') {
						if(mysql_query($sql_rspw)) {
							$mysql_affected_rows = mysql_affected_rows();
							$_POST[loginfield] = $_POST[loginfield] == $username ? '�Τ�W' : 'UID���X';
							$info .= "�w�N$_POST[loginfield]�� $_POST[where] ���޲z���K�X�]�m���G$_POST[password]<br><br>";
						} else {
							$info = '<font color="red">���ѽ��ˬdMysql�]�mconfig.inc.php</font>';
						}
					} elseif($whereis == 'is_dz') {
						if($dz_version < 610){
							$salt = substr(md5(time()), 0, 6);
							$psw = md5(md5($_POST['password']).$salt);						
							//$psw = md5($_POST['password']);
							 //mysql_query("update {$tablepre}members set password='$psw' where $_POST[loginfield] = '$_POST[where]' limit 1");
							 mysql_query("update {$tablepre}members set password='$psw' ,salt='$salt',secques='' where $_POST[loginfield] = '$_POST[where]' limit 1");
						}else{
							//�p�G�Odz�A�����n�s����uc�̭��M�����$sql_rspw�ק�K�X
							$salt = substr(md5(time()), 0, 6);
							$psw = md5(md5($_POST['password']).$salt);
							mysql_connect(UC_DBHOST, UC_DBUSER, UC_DBPW);
							if($_POST['issecques'] && $dz_version>=700){
								$sql_rspw = "UPDATE ".UC_DBTABLEPRE."members SET password='".$psw."',salt='".$salt."',secques='' WHERE username = '$_POST[where]' limit 1";
							}else{
								$sql_rspw = "UPDATE ".UC_DBTABLEPRE."members SET password='".$psw."',salt='".$salt."' WHERE username = '$_POST[where]' limit 1";
							}
							mysql_query($sql_rspw);
						}
						$info .= "�w�N$_POST[loginfield]�� $_POST[where] ���޲z���K�X�]�m���G$_POST[password]<br><br>";
					} elseif($whereis == 'is_uch') {
						$salt = substr(md5(time()), 0, 6);
						$psw = md5(md5($_POST['password']).$salt);
						mysql_connect(UC_DBHOST, UC_DBUSER, UC_DBPW);
						$sql_rspw = "UPDATE ".UC_DBTABLEPRE."members SET password='".$psw."',salt='".$salt."' WHERE username = '$_POST[where]' limit 1";
						mysql_query($sql_rspw);
						$info .="�w�N$_POST[loginfield]�� $_POST[where] ���޲z���K�X�]�m���G$_POST[password]<br><br>";
					}elseif($whereis == 'is_ss'){
						if($ss_version >=70){
							$salt = substr(md5(time()), 0, 6);
							$psw = md5(md5($_POST['password']).$salt);
							mysql_connect(UC_DBHOST, UC_DBUSER, UC_DBPW);
							$sql_rspw = "UPDATE ".UC_DBTABLEPRE."members SET password='".$psw."',salt='".$salt."' WHERE username = '$_POST[where]' limit 1";
							mysql_query($sql_rspw);
						}
						$info .= "�w�N$_POST[loginfield]�� $_POST[where] ���޲z���K�X�]�m���G$_POST[password]<br><br>";
					}
			} else {
				$info_rspw = "�޲z���K�X�еn��UC��x�h��C <a href=11 target='_blank'>�I���i�JUC��x</a>";
			}
			}
		}
		
		errorpage($info,'���m�޲z���b��',0,0);
	} else {
	?>
	<form action="?action=all_setadmin" method="post">
		<table>
			<?php
				if($whereis != 'is_uc') {
			?>
				<tr>
					<th>�w�s�b�޲z���C��</th>
					<td><?php echo $admins; ?></td>
				</tr>
				<tr>
					<th width="30%"><input class="radio" type="radio" name="loginfield" value="<?php echo $username; ?>" checked >�Τ�W<input class="radio" type="radio" name="loginfield" value="<?php echo $uid; ?>" >UID</th>
					<td width="70%"><input class="textinput" type="" name="where" size="25" maxlength="40">
					<?php if(!$rspw){
						echo '�i�H����w���Τᴣ�ɬ��޲z��';
					}?>
					</td>
				</tr>
			<?php
				}else {
					
				}
			?>
	
			<?php
				if($rspw) {
			?>
				<tr>
					<th width="30%">�п�J�K�X</th>
					<td width="70%"><input class="textinput" type="text" name="password" size="25"></td>
				</tr>
			<?php
				}else{
			?>
				<tr>
					<th width="30%">�K�X�קﴣ��</th>
					<td width="70%">�޲z���K�X�еn��UC��x�h��C<a href=11 target='_blank'>�I���i�JUC��x</a> </td>
				</tr>
			<?php
				}
				if($secq) {
			?>
				<tr>
					<th width="30%">�O�_�M���w������</th>
					<td width="70%"><input class="radio" name="issecques" value="1" checked="checked" type="radio">�O&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="issecques" value="" class="radio" type="radio">�_</td>
				</tr>
			<?php
				}
			?>
		</table>
		<input type="submit" name="loginsubmit" value="�� &nbsp; ��">
	</form>
	<?php
	}
	specialdiv();
	htmlfooter();
} elseif ($action == 'all_setlock') {//��w�u��c
	touch($lockfile);
	if(file_exists($lockfile)) {
		echo '<meta http-equiv="refresh" content="3 url=?">';
		errorpage("<h6>���\\�����u��c�I�j�P��ĳ�z�b���ݭn���{�Ǫ��ɭԤήɶi��R��</h6>",'��w�u��c');
	} else {
		errorpage('�`�N�z���ؿ��S���g�J�v���A�ڭ̵L�k���z���Ѧw���O�١A�ЧR���׾®ڥؿ��U��tool.php���I','��w�u��c');
	}
} elseif ($action == 'dz_moveattach') {//���ʪ���s��覡
	//��l�Ƽƾڮw�s���b��
	getdbcfg();
	//�s���ƾڮw
	define('IN_DISCUZ', TRUE);
	require_once TOOLS_ROOT."./config.inc.php";
	require_once TOOLS_ROOT."./include/db_mysql.class.php";
    $db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
	$dbuser = $dbpw = $dbname = $pconnect = NULL;
	htmlheader();
	echo "<h4>����O�s�覡</h4>";
	$atoption = array(
		'0' => '�з�(�����s�J�P�@�ؿ�)',
		'1' => '���׾¦s�J���P�ؿ�',
		'2' => '����������s�J���P�ؿ�',
		'3' => '������s�J���P�ؿ�',
		'4' => '���Ѧs�J���P�ؿ�',
	);
	if (!empty($_POST['moveattsubmit']) || $step == 1) {
		$rpp		=	"500"; //�C���B�z�h�ֱ��ƾ�
		$totalrows	=	isset($totalrows) ? $totalrows : 0;
		$convertedrows	=	isset($convertedrows) ? $convertedrows : 0;
		$start		=	isset($start) && $start > 0 ? $start : 0;
		$end		=	$start + $rpp - 1;
		$converted	=	0;
		$maxid		=	isset($maxid) ? $maxid : 0;
		$newattachsave	=	isset($newattachsave) ? $newattachsave : 0;
		$step		=	1;
		if ($start <= 1) {
			$db->query("UPDATE {$tablepre}settings SET value = '$newattachsave' WHERE variable = 'attachsave'");
			$cattachdir = $db->result($db->query("SELECT value FROM {$tablepre}settings WHERE variable = 'attachdir'"), 0);
			validid('aid', 'attachments');
		}
		$attachpath	=	isset($cattachdir) ? TOOLS_ROOT.$cattachdir : TOOLS_ROOT.'./attachments';
		$query = $db->query("SELECT aid, tid, dateline, filename, filetype, attachment, isimage, thumb FROM {$tablepre}attachments WHERE aid >= $start AND aid <= $end");
		while ($a = $db->fetch_array($query)) {
			$aid = $a['aid'];
			$tid = $a['tid'];
			$dateline = $a['dateline'];
			$filename = $a['filename'];
			$filetype = $a['filetype'];
			$attachment = $a['attachment'];
			$isimage = $a['isimage'];
			$thumb = $a['thumb'];
			$oldpath = $attachpath.'/'.$attachment;
			if (file_exists($oldpath)) {
				$realname = substr(strrchr('/'.$attachment, '/'), 1);
				if ($newattachsave == 1) {
					$fid = $db->result($db->query("SELECT fid FROM {$tablepre}threads WHERE tid = '$tid' LIMIT 1"), 0);
					$fid = $fid ? $fid : 0;
				} elseif ($newattachsave == 2) {
					$extension = strtolower(fileext($filename));
				}

				if ($newattachsave) {
					switch($newattachsave) {
						case 1: $attach_subdir = 'forumid_'.$fid; break;
						case 2: $attach_subdir = 'ext_'.$extension; break;
						case 3: $attach_subdir = 'month_'.gmdate('ym', $dateline); break;
						case 4: $attach_subdir = 'day_'.gmdate('ymd', $dateline); break;
					}
					$attach_dir = $attachpath.'/'.$attach_subdir;
					if(!is_dir($attach_dir)) {
						mkdir($attach_dir, 0777);
						@fclose(fopen($attach_dir.'/index.htm', 'w'));
					}
					$newattachment = $attach_subdir.'/'.$realname;
					
				} else {
					$newattachment = $realname;
				}
				$newpath = $attachpath.'/'.$newattachment;
				$asql1 = "UPDATE {$tablepre}attachments SET attachment = '$newattachment' WHERE aid = '$aid'";
				$asql2 = "UPDATE {$tablepre}attachments SET attachment = '$attachment' WHERE aid = '$aid'";
				if ($db->query($asql1)) {
					if (rename($oldpath, $newpath)) {
						if($isimage && $thumb) {
							$thumboldpath = $oldpath.'.thumb.jpg';
							$thumbnewpath = $newpath.'.thumb.jpg';
							rename($thumboldpath, $thumbnewpath);
						}
						$convertedrows ++;
					} else {
						$db->query($asql2);
					}
				}
				$totalrows ++;
			}
		}
		if($converted || $end < $maxid) {
			continue_redirect('dz_moveattach', '&newattachsave='.$newattachsave.'&cattachdir='.$cattachdir);
		} else {
			$msg = "$atoption[$newattachsave] ���ʪ��󧹲�<br><li>�@��".$totalrows."�Ӫ���ƾ�</li><br /><li>���ʤF".$convertedrows."�Ӫ���</li>";
			errorpage($msg,'',0,0);
		}

	} else {
		$attachsave = $db->result($db->query("SELECT value FROM {$tablepre}settings WHERE variable = 'attachsave' LIMIT 1"), 0);
		$checked[$attachsave] = 'checked';
		echo "<form method=\"post\" action=\"tools.php?action=dz_moveattach\" onSubmit=\"return confirm('�z�T�{�w�g�ƥ��n�ƾڮw�M����\\n�i�H�i����󲾰ʾާ@��H');\">
		<table>
		<tr>
		<th>���]�m�N���s�W�d�Ҧ����󪺦s��覡�C<font color=\"red\">�`�N�G������o�ͷN�~�A�Ъ`�N�ƥ��ƾڮw�M����C</font></th></tr><tr><td>";
		foreach($atoption as $key => $val){
			echo "<li style=\"list-style:none;\"><input class=\"radio\" name=\"newattachsave\" type=\"radio\" value=\"$key\" $checked[$key]>&nbsp; $val</input></li><br>";
		}
		echo "
		</td></tr></table>
		<input type=\"hidden\" id=\"oldattachsave\" name=\"oldattachsave\" style=\"display:none;\" value=\"$attachsave\">
		<input type=\"submit\" name=\"moveattsubmit\" value=\"�� &nbsp; ��\">
		</form>";
		specialdiv();
	}
	htmlfooter();
}elseif($action == 'dz_rplastpost'){//�״_�������̫�^�_
	//��l�Ƽƾڮw�s���b��
	getdbcfg();
	//�s���ƾڮw
	define('IN_DISCUZ', TRUE);
	require_once TOOLS_ROOT."./config.inc.php";
	require_once TOOLS_ROOT."./include/db_mysql.class.php";
    $db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
	$dbuser = $dbpw = $dbname = $pconnect = NULL;
  
	if($db->version > '4.1'){
			$serverset = 'character_set_connection=gbk, character_set_results=gbk, character_set_client=binary';
			$serverset && $db->query("SET $serverset");
	}
	$selectfid = $_POST['fid'];
	if($selectfid) {
			$i = 0;
			foreach($selectfid as $fid) {//�s�b�n��s��FID �����s
				$sql = "select t.tid, t.subject, p.subject AS psubject, p.dateline, p.author from {$tablepre}threads t,  {$tablepre}posts p where t.fid=$fid and p.tid=t.tid and t.displayorder>=0 and p.invisible=0 and p.status=0 order by p.dateline DESC limit 1";
				$query = $db->query($sql);
				$lastarray = array();
				if($lastarray = $db->fetch_array($query)){
					$lastarray['subject'] = $lastarray['psubject']?$lastarray['psubject']:$lastarray['subject'];
					$lastpoststr = $lastarray['tid']."\t".$lastarray['subject']."\t".$lastarray['dateline']."\t".$lastarray['author'];
					$db->query("update {$tablepre}forums set lastpost='$lastpoststr' where fid=$fid");
				}
			}
			htmlheader();
			show_tools_message("���m���\\", 'tools.php?action=dz_rplastpost');
			htmlfooter();

		}else {//���s�b��s��FID �i�J��ܬɭ�
			htmlheader();
				?>
				<h4>�״_�����̫�^�_ </h4>
				<?php echo "<div class=\"specialdiv\">�ާ@���ܡG<ul>
		<li>�i�H���w�ݭn�״_�������A�����{�Ƿ|���s�d�ߥX�������̫�^�ЫH���åB�״_</li>
		</ul></div>";
		?>

	<div class="tabbody">
		<script language="javascript">
				function checkall(form, prefix, checkall) {
					var checkall = checkall ? checkall : 'chkall';
					for(var i = 0; i < form.elements.length; i++) {
						var e = form.elements[i];
						if(e.name != checkall && (!prefix || (prefix && e.name.match(prefix)))) {
							e.checked = form.elements[checkall].checked;
						}
					}
				}
		</script>
	    <form action="tools.php?action=dz_rplastpost" method="post">
	
	        <h4 style="font-size:14px;">�׾ª����C��</h4>
			<style>table.re_forum_list { margin-left:0; width:30%;} .re_forum_list input { margin:0; margin-right:10px; border-style:none;}</style>
	        <table class="re_forum_list">
				<tr><th><input class="checkbox re_forum_input" name="chkall" onclick="checkall(this.form)" type="checkbox" ><strong>����</strong></th></tr>
	        	<?php
	            $sql = "SELECT fid,name FROM {$tablepre}forums WHERE type='forum' or type='sub'";
			    $query = mysql_query($sql);
			    $forum_array = array();
	            while($forumarray = mysql_fetch_array($query)) {
	            ?><tr><td><input name="fid[]" value="<?php echo $forumarray[fid];?>" type="checkbox" ><?php echo $forumarray['name']; ?></td></tr>
	            <?php
	           
				}
	            ?>
	        </table>
			<div class="opt">
			 <input type="submit" name="submit" value="����" tabindex="3" />
			</div>
	        
	    </form>
	</div>
	<?php
		}
} elseif ($action == 'dz_rpthreads') {//��q�״_�D�D
	//��l�Ƽƾڮw�s���b��
	getdbcfg();
	//�s���ƾڮw
	define('IN_DISCUZ', TRUE);
	require_once TOOLS_ROOT."./config.inc.php";
	require_once TOOLS_ROOT."./include/db_mysql.class.php";
    $db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
	$dbuser = $dbpw = $dbname = $pconnect = NULL;
  
	if($db->version > '4.1'){
			$serverset = 'character_set_connection=gbk, character_set_results=gbk, character_set_client=binary';
			$serverset && $db->query("SET $serverset");
	}
	if($rpthreadssubmit){
		  if(empty($start)){
			  $start = 0;
		  }
		if($fids){
			 if(is_array($fids)){
				$fidstr = implode(',', $fids);
			 }else{
				$fidstr = $fids;
			 }
			 $sql = "select tid from {$tablepre}threads where fid in (0,$fidstr) and displayorder>='0' limit $start, 500"; 
			 $countsql = "select count(*) from {$tablepre}threads where fid in (0,$fidstr) and displayorder>='0'";
		}else{
			 $sql =   "select tid from {$tablepre}threads where displayorder>='0' limit $start, 500";
			  $countsql = "select count(*) from {$tablepre}threads where displayorder>='0'";
		}
		$query = mysql_query($countsql);
		$threadnum = mysql_result($query,0);
		if($threadnum<$start){
			htmlheader();
			show_tools_message('���l�״_�����A�I�o�̪�^', 'tools.php?action=dz_rpthreads');
			htmlfooter();
			exit;
		}
		$query = mysql_query($sql);
		while($thread = mysql_fetch_array($query)){
			$tid = $thread['tid'];
			$processed = 1;
			$updatequery = mysql_query("SELECT COUNT(*) FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0'");
			$replies = mysql_result($updatequery, 0) - 1;
			$updatequery = mysql_query("SELECT a.aid FROM {$tablepre}posts p, {$tablepre}attachments a WHERE a.tid='$tid' AND a.pid=p.pid AND p.invisible='0' LIMIT 1");
			$attachment = mysql_num_rows($updatequery) ? 1 : 0;
			$updatequery  = mysql_query("SELECT pid, subject, rate FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' ORDER BY dateline LIMIT 1");
			$firstpost = mysql_fetch_array($updatequery);
			$firstpost['subject'] = addslashes(cutstr($firstpost['subject'], 79));
			@$firstpost['rate'] = $firstpost['rate'] / abs($firstpost['rate']);
			$updatequery  = mysql_query("SELECT author, dateline FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' ORDER BY dateline DESC LIMIT 1");
			$lastpost = mysql_fetch_array($updatequery);
			mysql_query("UPDATE {$tablepre}threads SET subject='$firstpost[subject]', replies='$replies', lastpost='$lastpost[dateline]', lastposter='".addslashes($lastpost['author'])."', rate='$firstpost[rate]', attachment='$attachment' WHERE tid='$tid'");
			mysql_query("UPDATE {$tablepre}posts SET first='1', subject='$firstpost[subject]' WHERE pid='$firstpost[pid]'");
			mysql_query("UPDATE {$tablepre}posts SET first='0' WHERE tid='$tid' AND pid<>'$firstpost[pid]'");
		}

		htmlheader();
		show_tools_message('���b�B�z�� '.$start.' ����� '.($start+500).' ���ƾ�', 'tools.php?action=dz_rpthreads&rpthreadssubmit=true&fids='.$fidstr.'&start='.($start+500));
		htmlfooter();
	}else{
	htmlheader();
	?>
	<h4>��q�״_�D�D </h4>
				<?php echo "<div class=\"specialdiv\">�ާ@���ܡG<ul>
		<li>���s���Y�ǩ��l����'���w�q�ާ@'�A�i�H���եΧ�q�״_�D�D���\\��i��״_</li>
		<li>�i�H���w�ݭn�״_�������A�����{�Ƿ|��q�״_���w�������D�D</li>
		<li>����Ϊ̥����ﳣ�|�״_�Ҧ��׾ª��D�D</li>
		</ul></div>";
?>
<div class="tabbody">
		<script language="javascript">
				function checkall(form, prefix, checkall) {
					var checkall = checkall ? checkall : 'chkall';
							
					for(var i = 0; i < form.elements.length; i++) {
						var e = form.elements[i];
						if(e.name != checkall && (!prefix || (prefix && e.name.match(prefix)))) {
							e.checked = form.elements[checkall].checked;
						}
					}
				}
		</script>
	<h4 style="font-size:14px;">�׾ª����C��</h4>
	<style>table.re_forum_list { margin-left:0; width:30%;} .re_forum_list input { margin:0; margin-right:10px; border-style:none;}</style>
	<form id="rpthreads" name="rpthreads" method="post"   action="tools.php?action=dz_rpthreads">
	<table class="re_forum_list">
	  <tr>
		<th><input type="checkbox" name="chkall" onclick="checkall(this.form)" class="checkbox re_forum_input" name="selectall" value="" />����</th>
	  </tr>
		<?php
	            $sql = "SELECT fid,name FROM {$tablepre}forums WHERE type='forum' or type='sub'";
			    $query = mysql_query($sql);
			    $forum_array = array();
	            while($forumarray = mysql_fetch_array($query)) {
	            ?><tr><td><input name="fids[]" value="<?php echo $forumarray[fid];?>" type="checkbox" ><?php echo $forumarray['name']; ?></td></tr>
	            <?php
	           
				}
	       ?>
	</table>
	
	<div class="opt">
	  <input type="submit" name="rpthreadssubmit" value="����" />
	</div>
	</form>
</div>
<?php
htmlfooter();
	}
} elseif ($action == 'all_logout') {//�h�X�n��
	setcookie('toolpassword', '', -86400 * 365);
	errorpage("<h6>�z�w���\\�h�X,�w��U���ϥ�.�j�P��ĳ�z�b���ϥήɧR�������.</h6>");
} elseif ($action == 'all_toolsback') {
	htmlheader();
?>
	<iframe style="margin-top:30px;" src="http://faq.comsenz.com/toolsback.php" width="600" height="500" frameborder="0"></iframe>
<?
	htmlfooter();
} else {
	htmlheader();
	?>
	<h4>�w��z�ϥ� Comsenz �t�κ��@�u��c</h4>
	<tr><td><br>
<?php
	if($installfile){
		echo '<font color="red" >�X��w���Ҽ{�A�p�G�w�˧����ЧR��'.$installfile."�׾¦w�ˤ��</font><br />";
	}
	if($upgradefile){
		echo '<font color="red" >�X��w���Ҽ{�A�p�G�ɯŧ����ЧR��'.$upgradefile." �׾¤ɯŤ��</font>";
	}

?>
	<h5>Comsenz �t�κ��@�u��c�\\��²���G</h5>
	<ul>
<?php
	foreach($functionall as  $value) {
		$apps = explode('_', $value['0']);
		if(in_array(substr($whereis, 3), $apps) || $value['0'] == 'all') {	
				echo '<li>'.$value[2].'�G'.$value[3].'</li>';
		}
	}
?>
	</ul>
	<?php
	specialdiv();
	htmlfooter();
}
//��Ʃw�q����   �{���޿�b��  
function cexit($message){
	echo $message;
	specialdiv();
	htmlfooter();
}
function checktable($table, $loops = 0) {
	global $db, $nohtml, $simple, $counttables, $oktables, $errortables, $rapirtables;
	$query = mysql_query("show create table $table");
	if($createarray = mysql_fetch_array($query)){
		if(strpos($createarray[1], 'TYPE=HEAP')){	
		   $counttables --;
			return ;
		}
	}
	$result = mysql_query("CHECK TABLE $table");
	if(!$result) {
		$counttables --;
		return ;
	}
	if(!$nohtml) {
		echo "<tr bgcolor='#CCCCCC'><td colspan=4 align='center'>�ˬd�ƾڪ� Checking table $table</td></tr>";
		echo "<tr><td>Table</td><td>Operation</td><td>Type</td><td>Text</td></tr>";
	} else {
		if(!$simple) {
			echo "\n>>>>>>>>>>>>>Checking Table $table\n";
			echo "---------------------------------<br>\n";
		}
	}
	$error = 0;
	while($r = mysql_fetch_row($result)) {
		if($r[2] == 'error') {
			if($r[3] == "The handler for the table doesn't support check/repair") {
				$r[2] = 'status';
				$r[3] = 'This table does not support check/repair/optimize';
				unset($bgcolor);
				$nooptimize = 1;
			} else {
				$error = 1;
				$bgcolor = 'red';
				unset($nooptimize);
			}
			$view = '���~';
			$errortables += 1;
		} else {
			unset($bgcolor);
			unset($nooptimize);
			$view = '���`';
			if($r[3] == 'OK') {
				$oktables += 1;
			}elseif($r[3] == 'The storage engine for the table doesn\'t support check'){
				$oktables += 1;
			}
		}
		if(!$nohtml) {
			echo "<tr><td>$r[0]</td><td>$r[1]</td><td bgcolor='$bgcolor'>$r[2]</td><td>$r[3] / $view </td></tr>";
		} else {
			if(!$simple) {
			echo "$r[0] | $r[1] | $r[2] | $r[3]<br>\n";
			}
		}
	}
	if($error) {
		if(!$nohtml) {
			echo "<tr><td colspan=4 align='center'>���b�״_�� / Repairing table $table</td></tr>";
		} else {
			if(!$simple) {
				echo ">>>>>>>>���b�״_�� / Repairing Table $table<br>\n";
			}
		}
		$result2=mysql_query("REPAIR TABLE $table");
		while($r2 = mysql_fetch_row($result2)) {
			if($r2[3] == 'OK') {
				$bgcolor='blue';
				$rapirtables += 1;
			} else {
				unset($bgcolor);
			}
			if(!$nohtml) {
				echo "<tr><td>$r2[0]</td><td>$r2[1]</td><td>$r2[2]</td><td bgcolor='$bgcolor'>$r2[3]</td></tr>";
			} else {
				if(!$simple) {
					echo "$r2[0] | $r2[1] | $r2[2] | $r2[3]<br>\n";
				}
			}
		}
	}
	if(($result2[3]=='OK'||!$error)&&!$nooptimize) {
		if(!$nohtml) {
			echo "<tr><td colspan=4 align='center'>�u�Ƽƾڪ� Optimizing table $table</td></tr>";
		} else {
			if(!$simple) {
			echo ">>>>>>>>>>>>>Optimizing Table $table<br>\n";
			}
		}
		$result3=mysql_query("OPTIMIZE TABLE $table");
		$error=0;
		while($r3=mysql_fetch_row($result3)) {
			if($r3[2]=='error') {
				$error=1;
				$bgcolor='red';
			} else {
				unset($bgcolor);
			}
			if(!$nohtml) {
				echo "<tr><td>$r3[0]</td><td>$r3[1]</td><td bgcolor='$bgcolor'>$r3[2]</td><td>$r3[3]</td></tr>";
			} else {
				if(!$simple) {
					echo "$r3[0] | $r3[1] | $r3[2] | $r3[3]<br><br>\n";
				}
			}
		}
	}
	if($error && $loops) {
		checktable($table,($loops-1));
	}
}
function checkfullfiles($currentdir) {
	global $db, $tablepre, $md5files, $cachelist, $templatelist, $lang, $nopass;
	
	$dir = @opendir(TOOLS_ROOT.$currentdir);
	while($entry = @readdir($dir)) {
		$file = $currentdir.$entry;
		$file = $currentdir != './' ? preg_replace('/^\.\//', '', $file) : $file;
		$mainsubdir = substr($file, 0, strpos($file, '/'));
		if($entry != '.' && $entry != '..') {
		
			echo "<script>parent.$('msg').innerHTML = '$lang[filecheck_fullcheck_current] ".addslashes(date('Y-m-d H:i:s')."<br>$lang[filecheck_fullcheck_file] $file")."';</script>\r\n";
			if(is_dir($file)) {
				
				checkfullfiles($file.'/');
			} elseif(is_file($file) && !in_array($file, $md5files)) {
				$pass = FALSE;
				if(in_array($file, array('./favicon.ico', './config.inc.php', './mail_config.inc.php', './robots.txt'))) {
					$pass = TRUE;
				}
				if($entry == 'index.htm' && filesize($file) < 5) {
					$pass = TRUE;
				}
				switch($mainsubdir) {
					case 'attachments' :
						if(!preg_match('/\.(php|phtml|php3|php4|jsp|exe|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)$/i', $entry)) {
							$pass = TRUE;
						}
					break;
					case 'images' :
						if(preg_match('/\.(gif|jpg|jpeg|png|ttf|wav|css)$/i', $entry)) {
							$pass = TRUE;
						}
					case 'customavatars' :
						if(preg_match('/\.(gif|jpg|jpeg|png)$/i', $entry)) {
							$pass = TRUE;
						}
					break;
					case 'mspace' :
						if(preg_match('/\.(gif|jpg|jpeg|png|css|ini)$/i', $entry)) {
							$pass = TRUE;
						}
					break;
					case 'forumdata' :
						$forumdatasubdir = str_replace('forumdata', '', dirname($file));
						if(substr($forumdatasubdir, 0, 8) == '/backup_') {
							if(preg_match('/\.(zip|sql)$/i', $entry)) {
								$pass = TRUE;
							}
						} else {
							switch ($forumdatasubdir) {
								case '' :
									if(in_array($entry, array('dberror.log', 'install.lock'))) {
										$pass = TRUE;
									}
								break;
								case '/templates':
									if(empty($templatelist)) {
										$query = mysql_query("SELECT templateid, directory FROM {$tablepre}templates");
										while($template = mysql_fetch_array($query)) {
											$templatelist[$template['templateid']] = $template['directory'];
										}
									}
									$tmp = array();
									$entry = preg_replace('/(\d+)\_(\w+)\.tpl\.php/ie', '$tmp = array(\1,"\2");', $entry);
									if(!empty($tmp) && file_exists($templatelist[$tmp[0]].'/'.$tmp[1].'.htm')) {
										$pass = TRUE;
									}
								break;
								case '/logs':
									if(preg_match('/(runwizardlog|\_cplog|\_errorlog|\_banlog|\_illegallog|\_modslog|\_ratelog|\_medalslog)\.php$/i', $entry)) {
										$pass = TRUE;
									}
								break;
								case '/cache':
									if(preg_match('/\.php$/i', $entry)) {
										if(empty($cachelist)) {
											$cachelist = checkcachefiles('forumdata/cache/');
											foreach($cachelist[1] as $nopassfile => $value) {
												$nopass++;
												echo "<script>parent.$('checkresult').innerHTML += '$nopassfile<br>';</script>\r\n";
											}
										}
										$pass = TRUE;
									} elseif(preg_match('/\.(css|log)$/i', $entry)) {
										$pass = TRUE;
									}
								break;
								case '/threadcaches':
									if(preg_match('/\.htm$/i', $entry)) {
										$pass = TRUE;
									}
								break;
							}
						}
					break;
					case 'templates' :
						if(preg_match('/\.(lang\.php|htm)$/i', $entry)) {
							$pass = TRUE;
						}
						
					break;
					case 'include' :
						if(preg_match('/\.table$/i', $entry)) {
							$pass = TRUE;
						}
					break;
					case 'ipdata' :
						if($entry == 'wry.dat' || preg_match('/\.txt$/i', $entry)) {
							$pass = TRUE;
						}
					break;
					case 'admin' :
						if(preg_match('/\.md5$/i', $entry)) {
							$pass = TRUE;
						}
					break;
				}

				if(!$pass) {
					$nopass++;
					
					echo "<script>parent.$('checkresult').innerHTML += '$file<br>';</script>\r\n";
				}
			}
			ob_flush();
			flush();
		}
	}
	return $nopass;
}
function checkdirs($currentdir) {
	global $dirlist;
	$dir = @opendir(TOOLS_ROOT.$currentdir);
	
	while($entry = @readdir($dir)) {
		$file = $currentdir.$entry;
		if($entry != '.' && $entry != '..') {
			if(is_dir($file)) {
				$dirlist[] = $file;
				checkdirs($file.'/');
			}
		}
	}
}
function checkcachefiles($currentdir) {
	global $authkey;
	$dir = opendir($currentdir);
	$exts = '/\.php$/i';
	$showlist = $modifylist = $addlist = array();
	while($entry = readdir($dir)) {
		$file = $currentdir.$entry;
		if($entry != '.' && $entry != '..' && preg_match($exts, $entry)) {
			@$fp = fopen($file, 'rb');
			@$cachedata = fread($fp, filesize($file));
			@fclose($fp);

			if(preg_match("/^<\?php\n\/\/Discuz! cache file, DO NOT modify me!\n\/\/Created: [\w\s,:]+\n\/\/Identify: (\w{32})\n\n(.+?)\?>$/s", $cachedata, $match)) {
				$showlist[$file] = $md5 = $match[1];
				$cachedata = $match[2];

				if(md5($entry.$cachedata.$authkey) != $md5) {
					$modifylist[$file] = $md5;
				}
			} else {
				$showlist[$file] = $addlist[$file] = '';
			}
		}

	}

	return array($showlist, $modifylist, $addlist);
}

function continue_redirect($action = 'dz_mysqlclear', $extra = '') {
	global $scriptname, $step, $actionnow, $start, $end, $stay, $convertedrows, $allconvertedrows, $totalrows, $maxid;
	if($action == 'doctor') {
		$url = "?action=$action{$extra}";
	} else {
		$url = "?action=$action&step=".$step."&start=".($end + 1)."&stay=$stay&totalrows=$totalrows&convertedrows=$convertedrows&maxid=$maxid&allconvertedrows=$allconvertedrows".$extra;
	}
	$timeout = $GLOBALS['debug'] ? 5000 : 2000;
	echo "<script>\r\n";
	echo "<!--\r\n";
	echo "function redirect() {\r\n";
	echo "	window.location.replace('".$url."');\r\n";
	echo "}\r\n";
	echo "setTimeout('redirect();', $timeout);\r\n";
	echo "-->\r\n";
	echo "</script>\r\n";
	
	if($action == 'doctor') {
		echo '<h4>�׾����</h4><br><table>
		<tr><th>���b�i���ˬd,�еy��</th></tr><tr><td>';
		echo "<br><a href=\"".$url."\">�p�G�z���s�������ɶ��S���۰ʸ���A���I���o�̡I</a><br><br>";
		echo '</td></tr></table>';
	} elseif($action == 'dz_replace') {
		echo '<h4>�ƾڳB�z��</h4><table>
		<tr><th>���b�i��'.$actionnow.'</th></tr><tr><td>';
		echo "���b�B�z $start ---- $end ���ƾ�[<a href='$url&stop=1' style='color:red'>����B��</a>]";
		echo "<br><br><a href=\"".$url."\">�p�G�z���s�������ɶ��S���۰ʸ���A���I���o�̡I</a>";
		echo '</td></tr></table>';
	} else {
		echo '<h4>�ƾڳB�z��</h4><table>
		<tr><th>���b�i��'.$actionnow.'</th></tr><tr><td>';
		echo "���b�B�z $start ---- $end ���ƾ�[<a href='?action=$action' style='color:red'>����B��</a>]";
		echo "<br><br><a href=\"".$url."\">�p�G�z���s�������ɶ��S���۰ʸ���A���I���o�̡I</a>";
		echo '</td></tr></table>';
	}
}

function dirsize($dir) {
	$dh = @opendir($dir);
	$size = 0;
	while($file = @readdir($dh)) {
		if ($file != '.' && $file != '..') {
			$path = $dir.'/'.$file;
			if (@is_dir($path)) {
				$size += dirsize($path);
			} else {
				$size += @filesize($path);
			}
		}
	}
	@closedir($dh);
	return $size;
}

function get_real_size($size) {
	$kb = 1024;
	$mb = 1024 * $kb;
	$gb = 1024 * $mb;
	$tb = 1024 * $gb;

	if($size < $kb) {
		return $size.' Byte';
	} else if($size < $mb) {
		return round($size/$kb,2).' KB';
	} else if($size < $gb) {
		return round($size/$mb,2).' MB';
	} else if($size < $tb) {
		return round($size/$gb,2).' GB';
	} else {
		return round($size/$tb,2).' TB';
	}
}

function htmlheader(){
	global $alertmsg, $whereis, $functionall,$dz_version,$ss_version;
	echo '<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=big5">
		<title>Comsenz �t�κ��@�u��c 3.0UC</title>
		<style type="text/css"><!--
		body {font-family: Arial, Helvetica, sans-serif, "�ө���";font-size: 12px;color:#000;line-height: 120%;padding:0;margin:0;background:#DDE0FF;overflow-x:hidden;word-break:break-all;white-space:normal;scrollbar-3d-light-color:#606BFF;scrollbar-highlight-color:#E3EFF9;scrollbar-face-color:#CEE3F4;scrollbar-arrow-color:#509AD8;scrollbar-shadow-color:#F0F1FF;scrollbar-base-color:#CEE3F4;}
        a:hover {color:#60F;}
		ul {padding:2px 0 10px 0;margin:0;}
		textarea,table,td,th,select{border:1px solid #868CFF;border-collapse:collapse;}
		input{margin:10px 0 0px 30px;border-width:1px;border-style:solid;border-color:#FFF #64A7DD #64A7DD #FFF;padding:2px 8px;background:#E3EFF9;}
			input.radio,input.checkbox,input.textinput,input.specialsubmit {margin:0;padding:0;border:0;padding:0;background:none;}
			input.textinput,input.specialsubmit {border:1px solid #AFD2ED;background:#FFF;height:24px;}
			input.textinput {padding:4px 0;} 			input.specialsubmit {border-color:#FFF #64A7DD #64A7DD #FFF;background:#E3EFF9;padding:0 5px;}
		option {background:#FFF;}
		select {background:#F0F1FF;}
		#header {height:60px;width:100%;padding:0;margin:0;}
		    h2 {font-size:24px;font-weight:bold;position:absolute;top:24px;left:20px;padding:10px;margin:0;}
		    h3 {font-size:14px;position:absolute;top:28px;right:20px;padding:10px;margin:0;}
		#content {height:510px;background:#F0F1FF;overflow-x:hidden;z-index:1000;}
		    #nav {top:60px;left:0;height:510px;width:180px;border-right:1px solid #DDE0FF;position:absolute;z-index:2000;}
		        #nav ul {padding:0 10px;padding-top:30px;}
		        #nav li {list-style:none;}
		        #nav li a {font-size:14px;line-height:180%;font-weight:400;color:#000;}
		        #nav li a:hover {color:#60F;}
		    #textcontent {padding-left:200px;height:510px;width:100%;line-height:160%;overflow-y:auto;overflow-x:hidden;}
			    h4,h5,h6 {padding:4px;font-size:16px;font-weight:bold;margin-top:20px;margin-bottom:5px;color:#006;}
				h5,h6 {font-size:14px;color:#000;}
				h6 {color:#F00;padding-top:5px;margin-top:0;}
				.specialdiv {width:70%;border:1px dashed #C8CCFF;padding:0 5px;margin-top:20px;background:#F9F9FF;}
				#textcontent ul {margin-left:30px;}
				textarea {width:78%;height:320px;text-align:left;border-color:#AFD2ED;}
				select {border-color:#AFD2ED;}
				table {width:74%;font-size:12px;margin-left:18px;margin-top:10px;}
				    table.specialtable,table.specialtable td {border:0;}
					td,th {padding:5px;text-align:left;}
				    caption {font-weight:bold;padding:8px 0;color:#3544FF;text-align:left;}
				    th {background:#D9DCFF;font-weight:600;}
					td.specialtd {text-align:left;}
				.specialtext {background:#FCFBFF;margin-top:20px;padding:5px 40px;width:64.5%;margin-bottom:10px;color:#006;}
		#footer p {padding:0 5px;text-align:center;}
		-->
		</style>
		</head>

		<body>
        <div id="header">
		<h2>Comsenz �t�κ��@�u��c '.VERSION.'</h2>
		<h3>[ <a href="?" target="_self">����</a> ]&nbsp;
		[ <a href="?action=all_setlock" target="_self">��w</a> ]&nbsp;
		[ <a href="?action=all_logout" target="_self">�h�X</a> ]&nbsp;</h3>
		</div>
		<div id="nav">';
		echo '<ul>';//�ɯ��椤�ھڤ��P���ؿ���ܤ��P
		foreach($functionall as  $value) {
			$apps = explode('_', $value['0']);
			if(in_array(substr($whereis, 3), $apps) || $value['0'] == 'all') {	
				if($whereis == 'is_ss' && $value[1] == 'all_setadmin' && $ss_version<70 ){
					continue;
				}
				echo '<li>[ <a href="?action='.$value[1].'" target="_self">'.$value[2].'</a> ]</li>';
			}
		}
		
		echo '</ul>';
		echo '</div>
		<div id="content">
		<div id="textcontent">';
}
function htmlfooter(){
	echo '
		</div></div>
		<div id="footer"><p>Comsenz �t�κ��@�u��c &nbsp;
		���v�Ҧ� &copy;2001-2008 <a href="http://www.comsenz.com" style="color: #888888; text-decoration: none">
		�d���зQ(�_��)��ަ������q Comsenz Inc.</a></font></td></tr><tr style="font-size: 0px; line-height: 0px; spacing: 0px; padding: 0px; background-color: #698CC3">
		</p></div>
		</body>
		</html>';
	exit;
}
function errorpage($message,$title = '',$isheader = 1,$isfooter = 1){
	if($isheader) {
		htmlheader();
	}
	!$isheader && $title = '';
	if($message == 'login'){
		$message ='<h4>�u��c�n��</h4>
				<form action="?" method="post">
					<table class="specialtable"><tr>
					<td width="20%"><input class="textinput" type="password" name="toolpassword"></input></td>
					<td><input class="specialsubmit" type="submit" value="�n ��"></input></td></tr></table>
					<input type="hidden" name="action" value="login">
				</form>';
	} else {
		$message = "<h4>$title</h4><br><br><table><tr><th>���ܫH��</th></tr><tr><td>$message</td></tr></table>";
	}
	echo $message;
	if($isfooter) {
		htmlfooter();
	}
}
function redirect($url) {
	echo "<script>";
	echo "function redirect() {window.location.replace('$url');}\n";
	echo "setTimeout('redirect();', 2000);\n";
	echo "</script>";
	echo "<br><br><a href=\"$url\">�p�G�z���s�����S���۰ʸ���A���I���o��</a>";
	cexit("");
}
/**
 * �ˬd�ؿ��̤U������v�����
 *
 * @param unknown_type $directory
 */
function getdirentry($directory) {
	global $entryarray;
	$dir = dir('./'.$directory);
	while($entry = $dir->read()) {
		if($entry != '.' && $entry != '..') {
			if(is_dir('./'.$directory.'/'.$entry)) {

				$entryarray[] = $directory.'/'.$entry;
				getdirentry($directory."/".$entry);
			} else {
				$entryarray[] = $directory.'/'.$entry;
			}
		}
	}
	$dir->close();
}
function splitsql($sql){
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query) {
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= $query[0] == "#" ? NULL : $query;
		}
		$num++;
	}
	return($ret);
}
function syntablestruct($sql, $version, $dbcharset) {

	if(strpos(trim(substr($sql, 0, 18)), 'CREATE TABLE') === FALSE) {
		return $sql;
	}
	if(substr(trim($sql), 0, 9) == 'SET NAMES' && !$version) {
        return '';
    } 
	$sqlversion = strpos($sql, 'ENGINE=') === FALSE ? FALSE : TRUE;

	if($sqlversion === $version) {

		return $sqlversion && $dbcharset ? preg_replace(array('/ character set \w+/i', '/ collate \w+/i', "/DEFAULT CHARSET=\w+/is"), array('', '', "DEFAULT CHARSET=$dbcharset"), $sql) : $sql;
	}

	if($version) {
		return preg_replace(array('/TYPE=HEAP/i', '/TYPE=(\w+)/is'), array("ENGINE=MEMORY DEFAULT CHARSET=$dbcharset", "ENGINE=\\1 DEFAULT CHARSET=$dbcharset"), $sql);

	} else {
		return preg_replace(array('/character set \w+/i', '/collate \w+/i', '/ENGINE=MEMORY/i', '/\s*DEFAULT CHARSET=\w+/is', '/\s*COLLATE=\w+/is', '/ENGINE=(\w+)(.*)/is'), array('', '', 'ENGINE=HEAP', '', '', 'TYPE=\\1\\2'), $sql);
	}
}
function stay_redirect() {
	global $action, $actionnow, $step, $stay, $convertedrows, $allconvertedrows;
	$nextstep = $step + 1;
	echo '<h4>�ƾڮw���l�ƾڲM�z</h4><table>
			<tr"><th>���b�i��'.$actionnow.'</th></tr><tr>
			<td>';
	if($stay) {
		$actions = isset($action[$nextstep]) ? $action[$nextstep] : '����';
		echo "$actionnow �ާ@����.�@�B�z<font color=red>{$convertedrows}</font>���ƾ�.".($stay == 1 ? "&nbsp;&nbsp;&nbsp;&nbsp;" : '').'<br><br>';
		echo "<a href='?action=dz_mysqlclear&step=".$nextstep."&stay=1'>�p�G�~��U�@�B�ާ@( $actions )�A���I���o�̡I</a><br>";
	} else {
		if(isset($action[$nextstep])) {
			echo '�Y�N�i�J�G'.$action[$nextstep].'......';
		}
		$allconvertedrows = $allconvertedrows + $convertedrows;
		$timeout = $GLOBALS['debug'] ? 5000 : 2000;
		echo "<script>\r\n";
		echo "<!--\r\n";
		echo "function redirect() {\r\n";
		echo "	window.location.replace('?action=dz_mysqlclear&step=".$nextstep."&allconvertedrows=".$allconvertedrows."');\r\n";
		echo "}\r\n";
		echo "setTimeout('redirect();', $timeout);\r\n";
		echo "-->\r\n";
		echo "</script>\r\n";
		echo "[<a href='?action=dz_mysqlclear' style='color:red'>����B��</a>]<br><br><a href=\"".$scriptname."?step=".$nextstep."\">�p�G�z���s�������ɶ��S���۰ʸ���A���I���o�̡I</a>";
	}
	echo '</td></tr></table>';
}

function loadtable($table, $force = 0) {	//�ˬd�ƾڮw��r�Ŷ����
	global $carray;
	$discuz_tablepre = $carray['tablepre'];
	static $tables = array();

	if(!isset($tables[$table])) {
		if(mysql_get_server_info() > '4.1') {
			$query = @mysql_query("SHOW FULL COLUMNS FROM {$discuz_tablepre}$table");
		} else {
			$query = @mysql_query("SHOW COLUMNS FROM {$discuz_tablepre}$table");
		}
		while($field = @mysql_fetch_assoc($query)) {
			$tables[$table][$field['Field']] = $field;
		}
	}
	return $tables[$table];
}

function validid($id, $table) {//��o�ƾڪ��̤j�M�̤p id ��
	global $start, $maxid, $db, $tablepre;
	$sql = $db->query("SELECT MIN($id) AS minid, MAX($id) AS maxid FROM {$tablepre}$table");
	$result = $db->fetch_array($sql);
	$start = $result['minid'] ? $result['minid'] - 1 : 0;
	$maxid = $result['maxid'];
}

function specialdiv() {
	echo '<div class="specialdiv">
		<h6>�`�N�G</h6>
		<ul>
		<li>��ƾڮw�ާ@�i��|�X�{�N�~�{�H���o�ͤί}�a�A�ҥH�Х��ƥ��n�ƾڮw�A�i��W�z�ާ@�I�t�~�бz��ܪA�Ⱦ����O����p���ɭԶi��@���u�ƾާ@�C</li>
		<li>��z�ϥΧ���Comsenz �t�κ��@�u��c��A���I����w�u��c�H�T�O�t�Ϊ��w���I�U���ϥΫe�u�ݭn�b/forumdata�ؿ��U�R��tool.lock���Y�i�}�l�ϥΡC</li></ul></div>';
}
function getplace() {
	global $lockfile, $cfgfile;
	$whereis = false;
	if(is_writeable('./config.inc.php') && is_writeable('./forumdata')) {//�P�_Discuz!�ؿ�
			$whereis = 'is_dz';
			$lockfile = './forumdata/tools.lock';
			$cfgfile = './config.inc.php';
	}
	if(is_writeable('./data/config.inc.php') && is_dir('./control')) {//�P�_UCenter�ؿ�
			$whereis = 'is_uc';
			$lockfile = './data/tools.lock';
			$cfgfile = './data/config.inc.php';
	}
	if(is_writeable('./config.php') && is_dir('source')) {//�P�_UCenter Home�ؿ�
			$whereis = 'is_uch';
			$lockfile = './data/tools.lock';
			$cfgfile = './config.php';
	}
	if(is_writeable('./config.php') && file_exists('./batch.common.php')) {//�P�_SupeSite�ؿ�
			$whereis = 'is_ss';
			$lockfile = './data/tools.lock';
			$cfgfile = './config.php';
	}
	if(is_writeable('./data/config.php')) {//�P�_ECShop�ؿ�
		$cfgpage = file_get_contents('./data/config.php');
		if(strpos($cfgpage, 'EC_CHARSET')) {
			$whereis = 'is_ec';
			$lockfile = './data/tools.lock';
			$cfgfile = './data/config.php';
		}
	}
	if(is_writeable('./data/inc.config.php')) {//�P�_ECmall�ؿ�
		$cfgpage = file_get_contents('./data/inc.config.php');
		if(strpos($cfgpage, 'ECM_KEY')) {
			$whereis = 'is_ecm';
			$lockfile = './data/tools.lock';
			$cfgfile = './data/inc.config.php';
		}
	}
	return $whereis;
}
function getdbcfg(){//��o�ƾڮw�t�m�H��
	global $dbhost, $dbuser, $dbpw, $dbname, $dbcfg, $whereis, $cfgfile, $tablepre, $dbcharset,$dz_version,$ss_version;
	if(@!include($cfgfile)) {
			htmlheader();
			cexit("<h4>�Х��W��config���H�O�ұz���ƾڮw�ॿ�`�챵�I</h4>");
	}
	switch($whereis) {
		case 'is_dz':
			$dbhost = $dbhost;
			$dbuser = $dbuser;
			$dbpw = $dbpw;
			$dbname = $dbname;	
			$tablepre =  $tablepre;
			$dbcharset = !$dbcharset ? (strtolower($charset) == 'utf-8' ? 'utf8' : $charset): $dbcharset;
			define('IN_DISCUZ',true);
			@require_once "./discuz_version.php";
			//$dz_version = DISCUZ_VERSION;
			$dz_version = '6.0.0';
			$dz_version = intval(str_replace('.','',$dz_version));
			break;
		case 'is_uc':
			$dbhost = UC_DBHOST;
			$dbuser = UC_DBUSER;
			$dbpw = UC_DBPW;
			$dbname = UC_DBNAME;	
			$tablepre =  UC_DBTABLEPRE;
			$dbcharset = !UC_DBCHARSET ? (strtolower(UC_CHARSET) == 'utf-8' ? 'utf8' : UC_CHARSET) : UC_DBCHARSET;
			break;
		case 'is_uch':
			$dbhost = $_SC["dbhost"];
			$dbuser = $_SC["dbuser"];
			$dbpw = $_SC["dbpw"];
			$dbname = $_SC["dbname"];	
			$tablepre =  $_SC["tablepre"];
			$dbcharset = !$_SC['dbcharset'] ? (strtolower($_SC["charset"]) == 'utf-8' ? 'utf8' : $_SC["charset"]) : $_SC['dbcharset'] ;
			break;
		case 'is_ss':
			$dbhost = $dbhost?$dbhos:$_SC['dbhost'];
			$dbuser = $dbuser?$dbuser:$_SC['dbuser'];
			$dbpw = $dbpw?$dbpw:$_SC['dbpw'];
			$dbname = $dbname?$dbname:$_SC['dbname'];	
			$tablepre =  $tablepre?$tablepre:$_SC['tablepre'];
			$dbcharset = !$dbcharset ? (strtolower($charset) == 'utf-8' ? 'utf8' : $charset) : $dbcharset;
			if(!$dbcharset){
				$dbcharset = !$_SC['dbcharset'] ? (strtolower($_SC['charset']) == 'utf-8' ? 'utf8' : $_SC['charset']) : $_SC['dbcharset'];			
			}
			if($_SC['dbhost'] || $_SC['dbuser']){
				$ss_version = 70;
			}
			break;
		case 'is_ec':
			$dbhost = $db_host;
			$dbuser = $db_user;
			$dbpw = $db_pass;
			$dbname = $db_name;	
			$tablepre =  $prefix;
			$dbcharset = strtolower(EC_CHARSET) == 'utf-8' ? 'utf8' : EC_CHARSET;
			break;
		case 'is_ecm':
			$dbcfgs = parse_url(DB_CONFIG); 
	 		$dbhost = $dbcfgs['host'];
	 		$dbuser = $dbcfgs['user'];
	 		$dbpw = $dbcfgs['pass'];
			$dbname = substr($dbcfgs['path'], 1);	
			$tablepre = DB_PREFIX;
			$dbcharset = substr(LANG, strpos(LANG, '-')+1);
			break;
		default:
			$dbhost=$dbuser=$dbpw=$dbname=$tablepre=$dbcharset='';
			break;
	}
}
function taddslashes($string, $force = 0) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = taddslashes($val, $force);
			}
		} else {
			$string = addslashes($string);
		}
	}
	return $string;
}
function pregcharset($charset,$color=0) {
		if(strpos('..'.strtolower($charset), 'gbk')) {
			if($color){
				return '<font color="#0000CC">gbk</font>';
			}else{
				return 'gbk';
			}
		}elseif(strpos('..'.strtolower($charset), 'latin1')) {
			if($color){
				return '<font color="#993399">latin1</font>';
			}else{
				return 'latin1';
			}
		}elseif(strpos('..'.strtolower($charset), 'utf8')) {
			if($color){
				return '<font color="#993300">utf8</font>';
			}else{
				return 'utf8';
			}
		}elseif(strpos('..'.strtolower($charset), 'big5')) {
			if($color){
				return '<font color="#006699">big5</font>';
			}else{
				return 'big5';	
			}
		}else{
	       return $charset;
		}
}
function show_tools_message($message, $url = 'tools.php') {
	echo "<script>";
	echo "function redirect() {window.location.replace('$url');}\n";
	echo "setTimeout('redirect();', 2000);\n";
	echo "</script>";
	echo "<h4>$title</h4><br><br><table><tr><th>���ܫH��</th></tr><tr><td>$message<br><a href=\"$url\">�p�G�z���s�����S���۰ʸ���A���I���o��</a></td></tr></table>";
	exit("");
}
function fileext($filename) {
	return trim(substr(strrchr($filename, '.'), 1, 10));
}
function cutstr($string, $length, $dot = ' ...') {
	global $charset;
	if(strlen($string) <= $length) {
		return $string;
	}
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
	$strcut = '';
	if(strtolower($charset) == 'utf-8') {
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t < 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}
	$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
	return $strcut.$dot;
}
?>