<?php
/*
	[Discuz!] Tools (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: tools.php 1761 2008-12-10 07:55:12 by xiaobozi $
*/
/**
 * **********************	配置区	*******************************
 */
$tool_password = ''; // ☆★☆★☆★ 请您设置一个工具包的高强度密码，不能为空！☆★☆★☆★
error_reporting(E_ERROR | E_WARNING | E_PARSE);	//E_ERROR | E_WARNING | E_PARSE
@set_time_limit(0);
define('TOOLS_ROOT', dirname(__FILE__)."/");
define('VERSION', '2009贺岁版');
$functionall = array(
	array('all', 'all_repair', '检查或修复数据库', '对所有数据表进行检查修复工作（支持所有Comsenz产品）。'),
	array('all', 'all_runquery', '数据库升级', '可以运行任意SQL语句，请慎用（支持所有Comsenz产品）。'),
	array('all', 'all_checkcharset', '编码检测修复', '对所有数据表进行编码检查和修复（支持所有Comsenz产品）。'),
	array('dz_uc_ss_uch', 'all_restore', '导入数据库备份', '一次性导入论坛数据备份（可以恢复Discuz!、UCenter、SupeSite、UCenter Home程序备份的数据）。'),
	array('uc_dz', 'uc_dz_deletepms', '清理短消息', '放在UCenter下可以'),
	array('dz_uc_uch_ec_ss', 'all_setadmin', '找回管理员', '将把您指定的会员设置为管理员，如果忘记管理员帐号密码，这是个不错的工具（支持Discuz!、SupeSite、UCenter、UCenter Home、ECshop）。'),
	array('dz', 'dz_doctor', '论坛医生', '自动检查您的论坛配置文件情况，系统环境信息以及错误报告（Discuz!论坛下使用）。'),
	array('dz', 'dz_filecheck', '搜索未知文件', '检查论坛程序目录下的非Discuz!官方文件（Discuz!论坛下使用）。'),
	array('dz', 'dz_rplastpost', '修复最后回复', '修复版块最后回复（Discuz!论坛下使用。)'),
	array('dz', 'dz_rpthreads', '批量修复主题', '某些帖子页面会出现未定义操作，可以用批量修复主题的功能修复下（Discuz!论坛下使用。'),
	array('dz', 'dz_mysqlclear', '数据库冗余数据清理', '对您的数据进行有效性检查，删除冗余数据信息（Discuz!论坛下使用）。'),
	array('dz', 'dz_moveattach', '附件保存方式', '将您现在的附件存储方式按照指定方式进行目录结构调整并重新存储（Discuz!论坛下使用）。'),
	array('dz', 'dz_replace', '帖子内容批量替换', '按照论坛后台中设置的词语过滤列表，可选择性的对所有帖子进行处理,帖子将按照过滤规则进行处理（Discuz!论坛下使用）。'),
	array('dz', 'dz_repair_auto', '字段自增长修复', '自动检索论坛所有的数据表，可修复自增字段丢失的问题（Discuz!论坛下使用）。'),
	array('dz', 'dz_updatecache', '更新缓存', '清除缓存（Discuz!论坛下使用）。'),
	array('all', 'all_toolsback', '<font color="red">反馈建议</font>', '您对Tools工具箱的建议和意见，以及使用过程中遇到的问题，可以及时的反馈给我们。')
);
//初始化
$lockfile = '';	//tools锁存放位置
$action = '';
$target_fsockopen = '0'; //使用何种方式进行连接服务器 0=域名, 1=IP （使用IP方式需要保证IP地址可以正常访问到您的站点）
$alertmsg = ' onclick="alert(\'点击确定开始运行,可能需要一段时间,请稍候\');"';

foreach(array('_COOKIE', '_POST', '_GET') as $_request) {  //释放变量到全局
	foreach($$_request as $_key => $_value) {
		($_key{0} != '_' && $_key != 'tool_password' && $_key != 'lockfile') && $$_key = taddslashes($_value);
	}
}
$whereis = getplace(); //判断文件位置
if($whereis == 'is_dz' && !defined('DISCUZ_ROOT')) {
	define('DISCUZ_ROOT', TOOLS_ROOT);
}
if(!$whereis && !in_array($whereis, array('is_dz', 'is_uc', 'is_uch', 'is_ss', 'is_ec', 'is_ecm'))) {
	$alertmsg = '';
	errorpage('<ul><li>工具箱必须放在Discuz!、UCenter、UCente Home、SupeSite、ECShop或者ECmall的根目录下才能正常使用。</li><li>如果你确实放在了上述程序目录下，请检查上述程序运行所需要设定的目录可读写权限是否正确</li>');
}
if(@file_exists($lockfile)) { //工具箱是否锁定
	$alertmsg = '';
	errorpage("<h6>工具箱已关闭，如需开启只要通过 FTP 删除 $lockfile 文件即可！ </h6>");
} elseif ($tool_password == ''){
	$alertmsg = '';
	errorpage('<h6>工具箱密码默认为空，第一次使用前请您修改本文件中$tool_password设置密码！</h6>');
}
if($action == 'login') {//登陆
	setcookie('toolpassword',md5($toolpassword), 0);
	echo '<meta http-equiv="refresh" content="2 url=?">';
	errorpage("<h6>请稍等，程序登录中！</h6>");
}
if(isset($toolpassword)) {
	if($toolpassword != md5($tool_password)) {
		$alertmsg = '';	//bug 有点多余？
		errorpage("login");
	}
} else {
	$alertmsg = '';
	errorpage("login");
}
// 判断是否含有升级或者安装文件，提示删除
if(file_exists(TOOLS_ROOT.'./install/index.php') && $whereis=='is_dz'){
	$installfile = './install/index.php';
}

for($ti=1;$ti<11;$ti++){
	if(file_exists(TOOLS_ROOT.'./upgrade'.$ti.'.php') && $whereis=='is_dz'){	
		$upgradefile = './upgrade'.$ti.'.php';
	}
}
getdbcfg();//获得数据库配置信息 连接数据库
mysql_connect($dbhost, $dbuser, $dbpw);
mysql_select_db($dbname);
$my_version = mysql_get_server_info();
if($my_version > '4.1'){
		$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
		$serverset .=$my_version > '5.0.1' ? ((empty($serverset))? '' : ',').'sql_mode=\'\'' : '';
		$serverset && mysql_query("SET $serverset");
}
//流程开始
if($action == 'all_repair') {//修复数据库开始
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
			echo '<h4>检查修复数据库</h4>
			    <h5>检查结果:</h5>
					<table>
						<tr><th>检查表(张)</th><th>正常表(张)</th><th>修复的表(张)</th><th>错误的表(个)</th></tr>
						<tr><td>'.$counttables.'</td><td>'.$oktables.'</td><td>'.$rapirtables.'</td><td>'.$errortables.'</td></tr>
					</table>
				<p>检查结果没有错误后请返回工具箱首页反之则继续修复</p>
				<p><b><a href="tools.php?action=all_repair">继续修复</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="tools.php">返回首页</a></b></p>
				</td></tr></table>';
			specialdiv();
		}
	} else {
		htmlheader();
		echo "<h4>检查修复数据库</h4>
		<div class='specialdiv'>
				操作提示：
				<ul>
				<li>您可以通过下面的方式修复已经损坏的数据库。点击后请耐心等待修复结果！</li>
				<li>本程序可以修复常见的数据库错误，但无法保证可以修复所有的数据库错误。(需要 MySQL 3.23+)</li>
				</ul>
				</div>
				<h5>操作：</h5>
				<ul>
				<li><a href=\"?action=all_repair&check=1&nohtml=1&simple=1\">检查并尝试修复数据库1次</a>
				<li><a href=\"?action=all_repair&check=1&iterations=5&nohtml=1&simple=1\">检查并尝试修复数据库5次</a> (因为数据库读写关系可能有时需要多修复几次才能完全修复成功)
				</ul>";
		specialdiv();
	}
	htmlfooter();
}elseif($action == 'all_restore') {//导入数据库备份
	ob_implicit_flush();
	$backdirarray = array( //不同的程序存放备份文件的目录是不同的
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
	?><h4>数据库恢复实用工具 </h4><?php
	echo "<div class=\"specialdiv\">操作提示：<ul>
		<li>只能恢复存放在服务器(远程或本地)上的数据文件,如果您的数据不在服务器上,请用 FTP 上传</li>
		<li>数据文件必须为 Discuz! 导出格式,并设置相应属性使 PHP 能够读取</li>
		<li>请尽量选择服务器空闲时段操作,以避免超时.如程序长久(超过 10 分钟)不反应,请刷新</li></ul></div>";
	if($file) {
		if(strtolower(substr($file, 0, 7)) == "http://") {
			echo "从远程数据库恢复数据 - 读取远程数据:<br><br>";
			echo "从远程服务器读取文件 ... ";
			$sqldump = @fread($fp, 99999999);
			@fclose($fp);
			if($sqldump) {
				echo "成功<br><br>";
			} elseif (!$multivol) {
				cexit("失败<br><br><b>无法恢复数据</b>");
			}
		} else {
			echo "<div class=\"specialtext\">从本地恢复数据 - 检查数据文件:<br><br>";
			if(file_exists($file)) {
				echo "数据文件 $file 存在检查 ... 成功<br><br>";
			} elseif (!$multivol) {
				cexit("数据文件 $file 存在检查 ... 失败<br><br><br><b>无法恢复数据</b></div>");
			}
			if(is_readable($file)) {
				echo "数据文件 $file 可读检查 ... 成功<br><br>";
				@$fp = fopen($file, "r");
				@flock($fp, 3);
				$sqldump = @fread($fp, filesize($file));
				@fclose($fp);
				echo "从本地读取数据 ... 成功<br><br>";
			} elseif (!$multivol) {
				cexit("数据文件 $file 可读检查 ... 失败<br><br><br><b>无法恢复数据</b></div>");
			}
		}
		if($multivol && !$sqldump) {
			cexit("分卷备份范围检查 ... 成功<br><br><b>恭喜您,数据已经全部成功恢复!安全起见,请务必删除本程序.</b></div>");
		}
		echo "数据文件 $file 格式检查 ... ";
		if($whereis == 'is_uc') {
			
			$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", substr($sqldump, 0, 256))));		
			$method = 'multivol';
			$volume = $identify[2];
		}else{
			@list(,,,$method, $volume) = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", preg_replace("/^(.+)/", "\\1", substr($sqldump, 0, 256)))));
		}
		if($method == 'multivol' && is_numeric($volume)) {
			echo "成功<br><br>";
		} else {
			cexit("失败<br><br><b>数据非 Discuz! 分卷备份格式,无法恢复</b></div>");
		}
		if($onlysave == "yes") {
			echo "将数据文件保存到本地服务器 ... ";
			$filename = TOOLS_ROOT.'./'.$backdirarray[$whereis].strrchr($file, "/");
			@$filehandle = fopen($filename, "w");
			@flock($filehandle, 3);
			if(@fwrite($filehandle, $sqldump)) {
				@fclose($filehandle);
				echo "成功<br><br>";
			} else {
				@fclose($filehandle);
				die("失败<br><br><b>无法保存数据</b>");
			}
			echo "成功<br><br><b>恭喜您,数据已经成功保存到本地服务器 <a href=\"".strstr($filename, "/")."\">$filename</a>.安全起见,请务必删除本程序.</b></div>";
		} else {
			$sqlquery = splitsql($sqldump);
			echo "拆分操作语句 ... 成功<br><br>";
			unset($sqldump);

			echo "正在恢复数据,请等待 ... </div>";
			foreach($sqlquery as $sql) {
				$dbversion = mysql_get_server_info();
				$sql = syntablestruct(trim($sql), $dbversion > '4.1', $dbcharset);
				if(trim($sql)) {
					@mysql_query($sql);
				}
			}
		if($auto == 'off'){
			$nextfile = str_replace("-$volume.sql", '-'.($volume + 1).'.sql', $file);
			cexit("<ul><li>数据文件 <b>$volume#</b> 恢复成功,如果有需要请继续恢复其他卷数据文件</li><li>请点击<b><a href=\"?action=all_restore&file=$nextfile&multivol=yes\">全部恢复</a></b>	或许单独恢复下一个数据文件<b><a href=\"?action=all_restore&file=$nextfile&multivol=yes&auto=off\">单独恢复下一数据文件</a></b></li></ul>");
		} else {
			$nextfile = str_replace("-$volume.sql", '-'.($volume + 1).'.sql', $file);
			echo "<ul><li>数据文件 <b>$volume#</b> 恢复成功,现在将自动导入其他分卷备份数据.</li><li><b>请勿关闭浏览器或中断本程序运行</b></li></ul>";
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

			$exportinfo = '<h5>数据备份信息</h5><table><caption>&nbsp;&nbsp;&nbsp;数据库文件夹</caption><tr><th>备份项目</th><th>版本</th><th>时间</th><th>类型</th><th>查看</th><th>操作</th></tr>';
			foreach($exportlog as $dateline => $info) {
				$info['dateline'] = is_int($dateline) ? gmdate("Y-m-d H:i", $dateline + 8*3600) : '未知';
					switch($info['type']) {
						case 'full':
							$info['type'] = '全部备份';
							break;
						case 'standard':
							$info['type'] = '标准备份(推荐)';
							break;
						case 'mini':
							$info['type'] = '最小备份';
							break;
						case 'custom':
							$info['type'] = '自定义备份';
							break;
					}
				$info['volume'] = $info['method'] == 'multivol' ? $info['volume'] : '';
				$info['method'] = $info['method'] == 'multivol' ? '多卷' : 'shell';
				$info['url'] = str_replace(".sql", '', str_replace("-$info[volume].sql", '', substr(strrchr($info['filename'], "/"), 1)));
				$exportinfo .= "<tr>\n".
					"<td>".$info['url']."</td>\n".
					"<td>$info[version]</td>\n".
					"<td>$info[dateline]</td>\n".
					"<td>$info[type]</td>\n";
				if($info['bakentry']){
				$exportinfo .= "<td><a href=\"?action=all_restore&bakdirname=".$info['url']."\">查看</a></td>\n".
					"<td><a href=\"?action=all_restore&file=$info[bakentry]&importsubmit=yes\">[全部导入]</a></td>\n</tr>\n";
				} else {
				$exportinfo .= "<td><a href=\"?action=all_restore&filedirname=".$info['url']."\">查看</a></td>\n".
					"<td><a href=\"?action=all_restore&file=$info[filename]&importsubmit=yes\">[全部导入]</a></td>\n</tr>\n";
				}
			}
		$exportinfo .= '</table>';
		echo $exportinfo;
		unset($exportlog);
		unset($exportinfo);
		echo "<br>";
	//查看目录里的备份文件列表，一级目录下
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
								<caption>&nbsp;&nbsp;&nbsp;数据库文件列表</caption>
								<tr>
								<th>文件名</th><th>版本</th>
								<th>时间</th><th>类型</thd>
								<th>大小</th><td>方式</th>
								<th>卷号</th><th>操作</th></tr>';
				foreach($exportlog as $dateline => $info) {
					$info['dateline'] = is_int($dateline) ? gmdate("Y-m-d H:i", $dateline + 8*3600) : '未知';
						switch($info['type']) {
							case 'full':
								$info['type'] = '全部备份';
								break;
							case 'standard':
								$info['type'] = '标准备份(推荐)';
								break;
							case 'mini':
								$info['type'] = '最小备份';
								break;
							case 'custom':
								$info['type'] = '自定义备份';
								break;
						}
					$info['volume'] = $info['method'] == 'multivol' ? $info['volume'] : '';
					$info['method'] = $info['method'] == 'multivol' ? '多卷' : 'shell';
					$exportinfo .= "<tr>\n".
						"<td><a href=\"$info[filename]\" name=\"".substr(strrchr($info['filename'], "/"), 1)."\">".substr(strrchr($info['filename'], "/"), 1)."</a></td>\n".
						"<td>$info[version]</td>\n".
						"<td>$info[dateline]</td>\n".
						"<td>$info[type]</td>\n".
						"<td>".get_real_size($info[size])."</td>\n".
						"<td>$info[method]</td>\n".
						"<td>$info[volume]</td>\n".
						"<td><a href=\"?action=all_restore&file=$info[filename]&importsubmit=yes&auto=off\">[导入]</a></td>\n</tr>\n";
				}
			$exportinfo .= '</table>';
			echo $exportinfo;
		}
	// 查看目录里的备份文件列表， 二级目录下，其中二级目录是随机产生的
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
					<caption>&nbsp;&nbsp;&nbsp;数据库文件列表</caption>
					<tr>
					<th>文件名</th><th>版本</th>
					<th>时间</th><th>类型</th>
					<th>大小</th><th>方式</th>
					<th>卷号</th><th>操作</th></tr>';
			foreach($exportlog as $dateline => $info) {
				$info['dateline'] = is_int($dateline) ? gmdate("Y-m-d H:i", $dateline + 8*3600) : '未知';
				switch($info['type']) {
					case 'full':
						$info['type'] = '全部备份';
						break;
					case 'standard':
						$info['type'] = '标准备份(推荐)';
						break;
					case 'mini':
						$info['type'] = '最小备份';
						break;
					case 'custom':
						$info['type'] = '自定义备份';
						break;
				}
				$info['volume'] = $info['method'] == 'multivol' ? $info['volume'] : '';
				$info['method'] = $info['method'] == 'multivol' ? '多卷' : 'shell';
				$exportinfo .= "<tr>\n".
						"<td><a href=\"$info[filename]\" name=\"".substr(strrchr($info['filename'], "/"), 1)."\">".substr(strrchr($info['filename'], "/"), 1)."</a></td>\n".
						"<td>$info[version]</td>\n".
						"<td>$info[dateline]</td>\n".
						"<td>$info[type]</td>\n".
						"<td>".get_real_size($info[size])."</td>\n".
						"<td>$info[method]</td>\n".
						"<td>$info[volume]</td>\n".
						"<td><a href=\"?action=all_restore&file=$info[filename]&importsubmit=yes&auto=off\">[导入]</a></td>\n</tr>\n";
			}
			$exportinfo .= '</table>';
			echo $exportinfo;
		}
		echo "<br>";
		cexit("");
	}
} elseif ($action == 'uc_dz_deletepms') {//清理短消息
	htmlheader();
		if($dz_version > 600){
			errorpage('<ul><li>Discuz!6.0.0以上版本的论坛请把tools工具箱放在UCenter目录下执行才能使用此功能</li><li>Discuz!6.0.0及其之前的版本可以直接放在Discuz!程序目录下执行使用此功能</li>','','');
			exit;
		}
	echo '<h4>清理短消息 </h4>';
	echo "<div class=\"specialdiv\">操作提示：<ul><li>清理短消息前一定要备份数据库,包括论坛的数据库和UCenter的数据库</li><li>使用本程序清理短消息是直接清理的，对于造成的短消息数据丢失本程序不负任何责任</li></ul></div>";
	if($step == 'search'){
		$sqladd = '';
		if($suid){//发送者的uid
			$sqladd .= $sqladd? ' and msgfromid='.$suid : 'msgfromid='.$suid;	
		}
		if($tuid){//接收者的uid	
			$sqladd .= $sqladd? ' and msgtoid='.$tuid :'msgtoid='.$tuid;		
		}
		if($starttime){//开始的时间
			$starttime = strtotime($starttime);
			$sqladd .= $sqladd? ' and dateline>'.$starttime : 'dateline>'.$starttime;
		}
		
		if($endtime){//结束的时间
			$endtime = strtotime($endtime);
			$sqladd .= $sqladd? ' and dateline<'.$endtime : 'dateline<'.$endtime;
		}
		
		if($notdeletenew){//删除未读短消息
		}else{
			$sqladd .= $sqladd?' and new=0':'new=0';
		}
		if(!$sqladd){
			errorpage('必须填写筛选短消息的条件','','');		
		}
		$sql = "select count(*) from {$tablepre}pms ".($sqladd?"where ".$sqladd:'');
		$query = mysql_query($sql);
		$deletenums = mysql_result($query, 0);
		echo $deletenums."条短消息等待删除";
		echo '<form action="tools.php?action=uc_dz_deletepms&step=delete" method="post" >';
		echo '<input type="hidden" name="sqladd" value="'.$sqladd.'" />';
		echo '<input type="submit" />';
		echo '</form>';
		htmlfooter();
		exit;
	}elseif($step == 'delete'){	
		if(!$sqladd){	
			errorpage('参数不正确');
			htmlfooter();
		}
		$sql = "delete from {$tablepre}pms ".($sqladd?"where ".$sqladd:'');
		$query = mysql_query($sql);
		$deletednums = mysql_affected_rows();

		echo "<table><tr><th>提示信息</th></tr><tr><td>清理短消息成功，共清理掉 $deletednums 短消息</td></tr></table>";
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
	s += '<tr align="center" class="calheader"><td class="calheader"><a href="###" onclick="refreshcalendar(yy, mm-1)" title="上一月">《</a></td><td colspan="5" style="text-align: center" class="calheader"><a href="###" onclick="showdiv(\'year\');doane(event)" title="点击选择年份" id="year"></a>&nbsp; - &nbsp;<a id="month" title="点击选择月份" href="###" onclick="showdiv(\'month\');doane(event)"></a></td><td class="calheader"><A href="###" onclick="refreshcalendar(yy, mm+1)" title="下一月">》</A></td></tr>';
	s += '<tr class="category calendar_header"><td>日</td><td>一</td><td>二</td><td>三</td><td>四</td><td>五</td><td>六</td></tr>';
	for(var i = 0; i < 6; i++) {
		s += '<tr class="altbg2">';
		for(var j = 1; j <= 7; j++)
			s += "<td id=d" + (i * 7 + j) + " height=\"19\">0</td>";
		s += "</tr>";
	}
	s += '<tr id="hourminute"><td colspan="7" align="center"><input class="textinput" style="width:30px;" type="text" size="1" value="" id="hour" onKeyUp=\'this.value=this.value > 23 ? 23 : zerofill(this.value);controlid.value=controlid.value.replace(/\\d+(\:\\d+)/ig, this.value+"$1")\'> 点 <input class="textinput" style="width:30px;" type="text" size="1" value="" id="minute" onKeyUp=\'this.value=this.value > 59 ? 59 : zerofill(this.value);controlid.value=controlid.value.replace(/(\\d+\:)\\d+/ig, "$1"+this.value)\'> 分</td></tr>';
	s += '</table></div></div>';
	s += '<div id="calendar_year" onclick="doane(event)" style="display: none"><div class="col">';
	for(var k = 1930; k <= 2019; k++) {
		s += k != 1930 && k % 10 == 0 ? '</div><div class="col">' : '';
		s += '<a href="###" onclick="refreshcalendar(' + k + ', mm);$(\'calendar_year\').style.display=\'none\'"><span' + (today.getFullYear() == k ? ' class="calendar_today"' : '') + ' id="calendar_year_' + k + '">' + k + '</span></a><br />';
	}
	s += '</div></div>';
	s += '<div id="calendar_month" onclick="doane(event)" style="display: none">';
	for(var k = 1; k <= 12; k++) {
		s += '<a href="###" onclick="refreshcalendar(yy, ' + (k - 1) + ');$(\'calendar_month\').style.display=\'none\'"><span' + (today.getMonth()+1 == k ? ' class="calendar_today"' : '') + ' id="calendar_month_' + k + '">' + k + ( k < 10 ? '&nbsp;' : '') + ' 月</span></a><br />';
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
			dd.firstChild.title = '今天';
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
			dd.firstChild.title = '今天';
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
		<table><tr><th>发送者UID</th><td><input class="textinput" type="text" name="suid"></td></tr>
		<tr><th>接收者UID</th><td><input class="textinput" type="text" name="tuid" value=""></td></tr>
		<tr><th>发送时间早于</th><td><input class="textinput" type="text" onclick="showcalendar(event, this, true)" id="endtime" name="endtime" value=""></td></tr>
		<tr><th>发送时间晚于</th><td><input class="textinput" type="text" onclick="showcalendar(event, this, true)" id="starttime" name="starttime" value=""></td></tr>
		</table>
		<input type="submit" style="margin-right:10px;" value="开始清理" /><input type="radio" class="radio" name="notdeletenew" value="1" />删除未读短消息
		</form>
		</div>
<?php
		specialdiv();
		htmlfooter();
	}
} elseif ($action == 'all_runquery') {//运行sql
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
			errorpage($sqlerror? $sqlerror : "数据库升级成功,影响行数: &nbsp;$affected_rows",'数据库升级');
			if(strpos($queries,'settings') && $whereis == 'is_dz') {
				require_once './include/cache.func.php';
				updatecache('settings');
			}
		}
		htmlheader();
		echo "<h4>数据库升级</h4>
			<form method=\"post\" action=\"tools.php?action=all_runquery\">
			<h5>请将数据库升级语句粘贴在下面</h4>";
		if($whereis == 'is_dz') {
			echo "<select name=\"queryselect\" onChange=\"queries.value = this.value\">
				<option value = ''>可选择TOOLS内置升级语句</option>
				<option value = \"REPLACE INTO ".$tablepre."settings (variable, value) VALUES ('seccodestatus', '0')\">关闭所有验证码功能</option>
				<option value = \"REPLACE INTO ".$tablepre."settings (variable, value) VALUES ('supe_status', '0')\">关闭论坛中的supersite功能</option>
				<option value = \"TRUNCATE TABLE ".$tablepre."failedlogins\">清空登陆错误记录</option>
			</select>";
		}
			echo "<br />
			<br /><textarea name=\"queries\">$queries</textarea><br />
			<input type=\"submit\" name=\"sqlsubmit\" value=\"提 &nbsp; 交\">
			</form>";
	
	htmlfooter();
	
		
}elseif($action == 'all_checkcharset') {//编码检测
	$maincharset = $dbcharset;
	if($my_version > '4.1') {
		if($repairsubmit){
			htmlheader();
			echo '<h4>编码检查</h4>';
			echo "<div class=\"specialdiv\">操作提示：<ul>
			<li>MySQL版本在4.1以上才有字符集的设定，所以数据库4.1版本以上的才能使用本功能</li>
			<li>如果某些字段的字符集不一致，有可能会导致程序中出现乱码，尽量把字符集不一致的字段转换成统一字符集</li>
			<li>有关MySQL编码机制可以参考 <a href='http://www.discuz.net/viewthread.php?tid=1022673' target='_blank'>点击查看</a></li>
			<li>一些关于MySQL编码方面的<a href='http://www.discuz.net/viewthread.php?tid=1070306' target='_blank'>教程</a></li>
			<li><font color=red>Tools工具箱只是尝试帮你修复数据库的字段编码，修复前请先备份你的数据库，以免造成不必要的损失，如果因为你没有备份数据库造成的损失与本程序无关</font></li>
			<li><font color=red>不能修复latin1字符集，可以尝试使用其他方法进行转码</font></li>
			</ul></div>";
			if(!is_array($repair)){
				$repair=array();
				show_tools_message('没有修复任何字段', 'tools.php?action=all_checkcharset');
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
			show_tools_message('修复完毕', 'tools.php?action=all_checkcharset');
			htmlfooter();
			exit;
		}else{
			$sql = "SELECT `TABLE_NAME` AS `Name`, `TABLE_COLLATION` AS `Collation` FROM `information_schema`.`TABLES` WHERE   ".(strpos("php".PHP_OS,"WIN")?"":"BINARY")."`TABLE_SCHEMA` IN ('$dbname') AND TABLE_NAME like '$tablepre%'";
			$query = @mysql_query($sql);
			$dbtable = array();
			$chars = array('gbk'=>0,'big5'=>0,'utf8'=>0,'latin1'=>0);//各个编码的字段数量 
			if(!$query){
				htmlheader();
				errorpage('您当前的数据库版本无法检查字符集设定，可能是由于版本过低不支持检查语句导致', '', 0, 0);
				htmlfooter();
				exit;
			}
			while($dbdetail = mysql_fetch_array($query)){
				
				$dbtable[$dbdetail["Name"]]["Collation"] = pregcharset($dbdetail["Collation"],1); //获得每个表的编码
				$dbtable[$dbdetail["Name"]]["tablename"] = $dbdetail["Name"]; //数据库所有的表名
				
				$tablequery = mysql_query("SHOW FULL FIELDS FROM `".$dbdetail["Name"]."`");//取出每个表的结构
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
	echo '<h4>编码检查</h4>';
	echo "<div class=\"specialdiv\">操作提示：<ul>
			<li>MySQL版本在4.1以上才有字符集的设定，所以数据库4.1版本以上的才能使用本功能</li>
			<li>如果某些字段的字符集不一致，有可能会导致程序中出现乱码，尽量把字符集不一致的字段转换成统一字符集</li>
			<li>有关MySQL编码机制可以参考 <a href='http://www.discuz.net/viewthread.php?tid=1022673' target='_blank'>点击查看</a></li>
			<li>一些关于MySQL编码方面的<a href='http://www.discuz.net/viewthread.php?tid=1070306' target='_blank'>教程</a></li>
			<li><font color=red>Tools工具箱只是尝试帮你修复数据库的字段编码，修复前请先备份你的数据库，以免造成不必要的损失，如果因为你没有备份数据库造成的损失与本程序无关</font></li>
			<li><font color=red>不能修复latin1字符集，可以尝试使用其他方法进行转码</font></li>
			
			</ul></div>";
	if($my_version > '4.1') {
	echo'<div class="tabbody">
		<style>.tabbody p em { color:#09C; padding:0 10px;} .char_div { margin-top:30px; margin-bottom:30px;} .char_div h4, .notice h4 { font-weight:600; font-size:16px; margin:0; padding:0; margin-bottom:10px;}</style>
		<div class="char_div"><h5>数据库('.$dbname.')的字符集统计：</h5>
		<table style="width:40%; margin:0; margin-bottom:20px;"><tr><th>gbk字段</th><th>big5字段</th><th>utf8字段</th><th>latin1字段</th></tr><tr><td>'.$chars[gbk].'&nbsp;</td><td>'.$chars[big5].'&nbsp;</td><td>'.$chars[utf8].'&nbsp;</td><td>'.$chars[latin1].'&nbsp;</td></tr></table>
		<div class="notice">
			<h5>下列字段可能存在编码设置异常：</h5>';
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
								<th><input type="checkbox" id="tables[]" style="border-style:none;"  name="chkall"  onclick="setrepaircheck(this, this.form, \''.$tablename.'\', \''.$char.'\');"  value="'.$tablename.'">全选</th>
								<th width=60%><strong>'.$tablename.'</strong> <font color="red">表异常的字段</font></th>
								<th>编码</th>
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
		  echo '<input type="submit" value="把指定的字段编码转换为'.$maincharset.'" name="repairsubmit" onclick="javascript:if (confirm(\'Tools工具箱只是尝试帮你修复数据库字段字符集，修复前请先备份你的数据库，以免造成不必要的损失，如果因为你没有备份数据库造成的损失与本程序无关\'));else return false;"></form>';
		echo'<br /><br /><br /></div> </div>';
	}else {
		errorpage('MySQL数据库版本在4.1以下，没有字符集设定，无需检测', '', 0, 0);
	}
		htmlfooter();
} elseif ($action == 'dz_doctor') {//论坛医生
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
		//得到返回的结果
		$contents = "";
		while (!feof($fp)) {
			$contents .= fread($fp, 1024);
		}
		fclose($fp);
		$array = split("\n\r", $contents, "2");
		return trim($array[1]);
	}
	//论坛模式样式代码变量
	$ok_style_s = '[color=RoyalBlue][b]';
	$error_style_s = '[color=Red][b]';
	$style_e = '[/b][/color]';
	$title_style_s = '[b]';
	$title_style_e = '[/b]';
	
	$phpfile_array = array('discuzroot', 'templates', 'cache');//文件错误检查中的目录及对应名称($dir_array)
	$dir_array = array('论坛根目录', '模板缓存目录(forumdata/templates)', '其它缓存目录(forumdata/cache)');
	$doctor_top = count($phpfile_array) - 1;
	//$doctor_step = isset($_REQUEST['doctor_step']) ? intval($_REQUEST['doctor_step']) : '';
	
	if(@!include("./config.inc.php")) {
		if(@!include("./config.php")) {
			cexit("<h4>请先上传config文件以保证您的数据库能正常链接！</h4>");
		}
	}
	if($doctor_step == $doctor_top) {
	
		//检查Config.inc.php文件配置
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
		'dbhost' => '数据库服务器',
		'dbuser' => '数据库用户名',
		'dbpw' => '数据库密码',
		'dbname' => '数据库名',
		'pconnect' => '数据库是否持久连接',
		'cookiepre' => 'cookie 前缀',
		'cookiedomain' => 'cookie 作用域',
		'cookiepath' => 'cookie 作用路径',
		'tablepre' => '表名前缀',
		'dbcharset' => 'MySQL链接字符集',
		'charset' => '论坛字符集',
		'headercharset' => '强制论坛页面使用默认字符集',
		'tplrefresh' => '论坛风格模板自动刷新开关',
		'forumfounders' => '论坛创始人uid',
		'dbreport' => '是否发送错误报告给管理员',
		'errorreport' => '是否屏蔽程序错误信息',
		'attackevasive' => '论坛防御级别',
		'admincp[\'forcesecques\']' => '管理人员是否必须设置安全提问才能进入系统设置',
		'admincp[\'checkip\']' => '后台管理操作是否验证管理员的 IP',
		'admincp[\'tpledit\']' => '是否允许在线编辑论坛模板',
		'admincp[\'runquery\']' => '是否允许后台运行 SQL 语句',
		'admincp[\'dbimport\']' => '是否允许后台恢复论坛数据',
		);
		$comment = array(
		'pconnect' => '非持久连接',
		'cookiepre' => '不检测',
		'cookiepath' => '不检测',
		'charset' => '不检测',
		'adminemail' => '不检测',
		'admincp' => '非设置项',
		);
		@mysql_connect($carray['dbhost'], $carray['dbuser'], $carray['dbpw']) or $mysql_errno = mysql_errno();
		!$mysql_errno && @mysql_select_db($carray['dbname']) or $mysql_errno = mysql_errno();
		$comment_error = "{$error_style_s}出错{$style_e}";
		if ($mysql_errno == '2003') {
			$comment['dbhost'] = "{$error_style_s}端口设置出错{$style_e}";
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
		$comment['pconnect'] = '非持久链接';
		$carray['pconnect'] == 1 && $comment['pconnect'] = '持久连接';
		if ($carray['cookiedomain'] && substr($carray['cookiedomain'], 0, 1) != '.') {
			$comment['cookiedomain'] = "{$error_style_s}请以 . 开头,不然同步登录会出错{$style_e}";
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
				$carray['dbcharset'] .= $error_style_s.'出错，您的论坛数据库字符集为 '.$dzdbcharset.' ，请将本项设置成 '.$dzdbcharset.$style_e;
			}
		}
		if(!in_array($carray['charset'],array('gbk', 'big5', 'utf-8'))) {
			$carray['charset'] .= $error_style_s."  出错，目前字符集只支持'gbk', 'big5', 'utf-8'".$style_e;
		}
		if ($carray['headercharset'] == 0) {
			$comment['headercharset'] = $title_style_s.'未开启'.$title_style_e;
		} else {
			$comment['headercharset'] = $ok_style_s.'开启'.$style_e;
		}
		if ($carray['tplrefresh'] == 0) {
			$comment['tplrefresh'] = $title_style_s.'关闭'.$title_style_e;
		} else {
			$comment['tplrefresh'] = $ok_style_s.'开启'.$style_e;
		}
		if (preg_match('/[^\d,]/i', str_replace(' ', '', $carray['forumfounders']))) {
			$comment['forumfounders'] = $error_style_s.'出错：含有非法字符，该项设置只能含有数字和半角逗号 ,'.$style_e;
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
					$comment['forumfounders'] = $error_style_s.'出错：创始人中无管理员'.$style_e;
				} elseif ($notadmin) {
					$comment['forumfounders'] = $error_style_s.'警告：创始人中有非管理员，uid如下：'.$notadminids.$style_e;
				}
			} else {
				$comment['forumfounders'] = $error_style_s.'警告：创始人设置为空，多个管理员将可能有安全问题'.$style_e;
			}
		}
		$comment['dbreport'] = $carray['dbreport'] == 0 ? '不发送错误报告' : '发送错误报告';
		$comment['errorreport'] = $carray['errorreport'] == 1 ? '屏蔽程序错误' : '不屏蔽程序错误';
		if (preg_match('/[^\d|]/i', str_replace(' ', '', $carray['attackevasive']))) {
			$carray['attackevasive'] .= $error_style_s.'出错：含有非法字符,该项设置只能含有数字和半角逗号,'.$style_e;
		} else {
			if (preg_match('/[8]/i', $carray['attackevasive']) && @mysql_result(@mysql_query("SELECT COUNT(*) FROM {$carray[tablepre]}members")) < 1) {
				$carray['attackevasive'] .= $error_style_s.'出错：您设置了回答问题(8)，但未添加验证问题和答案 ,'.$style_e;
			}
		}
		$comment_admincp_error = "否 > {$error_style_s}警告：有安全隐患{$style_e}";
		$comment_admincp_ok = "是 > {$error_style_s}警告：有安全隐患{$style_e}";
		if ($carray['admincp[\'forcesecques\']'] == 1) {
			$comment['admincp[\'forcesecques\']'] = "{$ok_style_s}是{$style_e}";
		} else {
			$comment['admincp[\'forcesecques\']'] = $comment_admincp_error;
		}
		if ($carray['admincp[\'checkip\']'] == 0) {
			$comment['admincp[\'checkip\']'] = $comment_admincp_error;
		} else {
			$comment['admincp[\'checkip\']'] = "{$ok_style_s}是{$style_e}";
		}
		if ($carray['admincp[\'tpledit\']'] == 1) {
			$comment['admincp[\'tpledit\']'] = $comment_admincp_ok;
		} else {
			$comment['admincp[\'tpledit\']'] = "{$title_style_s}否{$title_style_e}";
		}
		if ($carray['admincp[\'runquery\']'] == 1) {
			$comment['admincp[\'runquery\']'] = $comment_admincp_ok;
		} else {
			$comment['admincp[\'runquery\']'] = "{$title_style_s}否{$title_style_e}";
		}
		if ($carray['admincp[\'dbimport\']'] == 1) {
			$comment['admincp[\'dbimport\']'] = $comment_admincp_ok;
		} else {
			$comment['admincp[\'dbimport\']'] = "{$title_style_s}否{$title_style_e}";
		}
		foreach($carray as $key => $keyfield) {
			$clang[$key] == '' && $clang[$key] = '&nbsp;';
			strpos('comma'.$comment[$key], '警告') && $comment[$key] = $comment[$key];
			strpos('comma'.$comment[$key], '出错') && $comment[$key] = $comment[$key];
			$comment[$key] == '' && $comment[$key] = "{$ok_style_s}正常{$style_e}";
			if(in_array($key, array('dbuser', 'dbpw'))) {
				$keyfield = '**隐藏**';
			}
			$keyfield == '' && $keyfield = '空';
			if(!in_array($key, array('dbhost','dbuser','dbpw','dbname'))) {
				if(in_array($key, array('pconnect', 'headercharset', 'tplrefresh', 'dbreport', 'errorreport', 'admincp[\'forcesecques\']', 'admincp[\'checkip\']', 'admincp[\'tpledit\']', 'admincp[\'runquery\']', 'admincp[\'dbimport\']'))) {
					$doctor_config .= "\n\t{$title_style_s}$key{$title_style_e} ---> $clang[$key] ---> $comment[$key]\n";
				} elseif(in_array($key, array('cookiepre', 'cookiepath', 'cookiedomain', 'charset', 'dbcharset', 'attackevasive'))) {
					$doctor_config .= "\n\t{$title_style_s}$key{$title_style_e} ---> $clang[$key] ---> $keyfield\n";
				} else {
					$doctor_config .= "\n\t{$title_style_s}$key{$title_style_e} ---> $clang[$key] ---> $keyfield ---> $comment[$key]\n";
				}
			} else {
				if(strstr($comment[$key], '出错')) {
					strstr($doctor_config_db, '正常') && $doctor_config_db = '';
					$doctor_config_db .= "{$title_style_s}$key{$title_style_e} ---> $clang[$key] ---> $comment[$key]";
				} else {
					if(empty($doctor_config_db)) {
						$doctor_config_db ="\n\t{$ok_style_s}数据库正常链接.{$style_e}";
					}
				}
			}
	
		}
		$doctor_config = "\n".$doctor_config_db.$doctor_config;
		//校验环境是否支持DZ/SS，查看数据库和表的字符集，敏感信息 charset,dbcharset, php,mysql,zend,php 短标记
	
		$msg = '';
		$curr_os = PHP_OS;
	
		if(!function_exists('mysql_connect')) {
			$curr_mysql = $error_style_s.'不支持'.$style_e;
			$msg .= "您的服务器不支持MySql数据库，无法安装论坛程序";
			$quit = TRUE;
		} else {
			if(@mysql_connect($dbhost, $dbuser, $dbpw)) {
				$curr_mysql =  mysql_get_server_info();
			} else {
				$curr_mysql = $ok_style_s.'支持'.$style_e;
			}
		}
		if(function_exists('mysql_connect')) {
			$authkeylink = @mysql_connect($dbhost, $dbuser, $dbpw);
			mysql_select_db($dbname, $authkeylink);
			$authkeyresult = mysql_result(mysql_query("SELECT `value` FROM {$tablepre}settings WHERE `variable`='authkey'", $authkeylink), 0);
			if($authkeyresult) {
				$authkeyexist = $ok_style_s.'存在'.$style_e;
			} else {
				$authkeyexist = $error_style_s.'不存在'.$style_e;
			}
		}
		$curr_php_version = PHP_VERSION;
		if($curr_php_version < '4.0.6') {
			$msg .= "您的 PHP 版本小于 4.0.6, 无法使用 Discuz! / SuperSite。";
		}
	
		if(ini_get('allow_url_fopen')) {
			$allow_url_fopen = $ok_style_s.'允许'.$style_e;
		} else {
			$allow_url_fopen = $title_style_s.'不允许'.$title_style_e;
		}
		$max_execution_time = get_cfg_var('max_execution_time');
		$max_execution_time == 0 && $max_execution_time = '不限制';
	
		$memory_limit = get_cfg_var('memory_limit');
	
		$curr_server_software = $_SERVER['SERVER_SOFTWARE'];
	
		if(function_exists('ini_get')) {
			if(!@ini_get('short_open_tag')) {
				$curr_short_tag = $title_style_s.'不允许'.$title_style_e;
				$msg .='请将 php.ini 中的 short_open_tag 设置为 On，否则无法使用论坛。';
			} else {
				$curr_short_tag = $ok_style_s.'允许'.$style_e;
			}
			if(@ini_get(file_uploads)) {
				$max_size = @ini_get(upload_max_filesize);
				$curr_upload_status = '您可以上传附件的最大尺寸: '.$max_size;
			} else {
				$msg .= "附件上传或相关操作被服务器禁止。";
			}
		} else {
			$msg .= 'php.ini中禁用了ini_get()函数.部分环境参数无法检测.';
		}
	
		if(!defined('OPTIMIZER_VERSION')) define('OPTIMIZER_VERSION','没有安装或版本较低');
		if(OPTIMIZER_VERSION < 3.0) {
			$msg .="您的ZEND版本低于3.0,将无法使用SuperSite.";
		}
		//临时目录的检查
		if(@is_writable(@ini_get('upload_tmp_dir'))){
			$tmpwritable = $ok_style_s.'可写'.$style_e;
		} elseif(!@ini_get('upload_tmp_dir') & @is_writable($_ENV[TEMP])) {
			$tmpwritable = $ok_style_s.'可写'.$style_e;
		} else {
			$tmpwritable = $title_style_s.'不可写'.$title_style_e;
		}
	
		if(@ini_get('safe_mode') == 1) {
			$curr_safe_mode = $ok_style_s.'开启'.$style_e;
		} else {
			$curr_safe_mode = $title_style_s.'关闭'.$title_style_e;
		}
		if(@diskfreespace('.')) {
			$curr_disk_space = intval(diskfreespace('.') / (1024 * 1024)).'M';
		} else {
			$curr_disk_space = '无法检测';
		}
		if(function_exists('xml_parser_create')) {
			$curr_xml = $ok_style_s.'可用'.$style_e;
		} else {
			$curr_xml = $title_style_s.'不可用'.$title_style_e;
		}
	
		if(function_exists('file')) {
			$funcexistfile = $ok_style_s.'存在'.$style_e;
		} else {
			$funcexistfile = $title_style_s.'不存在'.$title_style_e;
		}
	
		if(function_exists('fopen')) {
			$funcexistfopen = $ok_style_s.'存在'.$style_e;
		} else {
			$funcexistfopen = $title_style_s.'不存在'.$title_style_e;
		}
	
		if(@ini_get('display_errors')) {
			$curr_display_errors = $ok_style_s.'开启'.$style_e;
		} else {
			$curr_display_errors = $title_style_s.'关闭'.$title_style_e;
		}
		if(!function_exists('ini_get')) {
			$curr_display_errors = $tmpwritable = $curr_safe_mode = $curr_upload_status = $curr_short_tag = '无法检测';
		}
		//目录权限检查
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
					$dir_perm .= "\n\t\t".(is_dir($fullentry) ? '目录' : '文件')." ./$entry {$error_style_s}无法写入.{$style_e}";
					$msg .= "\n\t\t".(is_dir($fullentry) ? '目录' : '文件')." ./$entry {$error_style_s}无法写入.{$style_e}";
					$fault = 1;
				}
			}
		}
		$dir_perm .= $fault ? '' : $ok_style_s.'文件及目录属性全部正确'.$style_e;
	
		/**
		 * gd库所需函数的检查
		 */
		$gd_check = '';
		if(!extension_loaded('gd')) {
			$gd_check .= '您的php.ini未开启extension=php_gd2.dll(windows)或者未编译gd库(linux).';
		} elseif(!function_exists('gd_info') && phpversion() < '4.3') {
			$gd_check .= 'php版本低于4.3.0，不支持高版本的gd库，请升级您的php版本.';
		} else {
			$ver_info = gd_info();
			preg_match('/([0-9\.]+)/', $ver_info['GD Version'], $match);
			if($match[0] < '2.0') {
				$gd_check .= "\n\t\tgd版本低于2.0,请升级您的gd版本以支持gd的验证码和水印.";
			} elseif(!(function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) ) {
				$gd_check .= "\n\t\tgd版本不支持jpeg的验证码和水印.";
			} elseif(!(function_exists('imagecreatefromgif') && function_exists('imagegif')) ) {
				$gd_check .= "\n\t\tgd版本不支持gif的验证码和水印.";
			} elseif(!(function_exists('imagecreatefrompng') && function_exists('imagepng')) ) {
				$gd_check .= "\n\t\tgd版本不支持png的验证码和水印.";
			} else {
				$gd_check .= '正常开启';
			}
		}
		if($gd_check != '正常开启') {
			$gd_check = $error_style_s.$gd_check.$style_e;
		} else {
			$gd_check = $ok_style_s.$gd_check.$style_e;
		}
	
		/**
		 * 检查ming库，目的为检查是否支持flash验证码
		 */
		$ming_check = '';
		if(extension_loaded('ming')) {
			if(substr($curr_os,0,3) == 'WIN') {
				$ming_check .= '您的php.ini未开启extension=php_ming.dll，所以无法支持flash验证码';
			} else {
				$ming_check .= '您未编译ming库，所以无法支持flash验证码';
			}
		} else {
			$ming_check .= '您的系统支持flash验证码，如果还无法使用flash验证码的话，有可能是您的php版本太低';
		}
	
		/**
		 *检查系统是否可以执行ImageMagick的命令
		 */
		$imagemagick_check = '';
		if(!function_exists('exec')) {
			$imagemagick_check .='您的php.ini里或者空间商禁止了使用exec函数，无法使用ImageMagick';
		} else {
			$imagemagick_check .='您现在只需安装好ImageMagick，然后配置好相关参数就可以使用ImageMagick(使用之前请先使用后台的预览功能来检查您的ImageMagick是否安装好)';
		}
		if($msg == '') {
			$msg = "{$ok_style_s}没有发现系统环境问题.{$style_e}";
		} else {
			$msg = $error_style_s.$msg.$style_e;
		}
		$doctor_env = "
			操作系统--->$curr_os
	
			WEB 引擎 --->$curr_server_software
	
			PHP 版本--->$curr_php_version
	
			MySQL 版本--->$curr_mysql
	
			Zend 版本--->".OPTIMIZER_VERSION."
	
			程序最长运行时间(max_execution_time)--->{$max_execution_time}秒
	
			内存大小(memory_limit)--->$memory_limit
	
			是否允许打开远程文件(allow_url_fopen)--->$allow_url_fopen
	
			是否允许使用短标记(short_open_tag)--->$curr_short_tag
	
			安全模式(safe_mode)--->$curr_safe_mode
	
			错误提示(display_errors)--->$curr_display_errors
	
			XML 解析器--->$curr_xml
	
			authkey 是否存在--->$authkeyexist
	
			系统临时目录--->$tmpwritable
	
			磁盘空间--->$curr_disk_space
	
			附件上传--->$curr_upload_status
	
			函数 file()--->$funcexistfile
	
			函数 fopen()--->$funcexistfopen
	
			目录权限---$dir_perm
	
			GD 库--->$gd_check
	
			ming 库--->$ming_check
	
			ImageMagick --->$imagemagick_check
	
			系统环境错误提示\r\n\t$msg";
	}
	if(!$doctor_step) {
		$doctor_step = '0';
		@unlink('./forumdata/doctor_cache.cache');
	}
	//php错误检查
	$dberrnomsg = array (
		'1008' => '数据库不存在，删除数据库失败',
		'1016' => '无法打开数据文件',
		'1041' => '系统内存不足',
		'1045' => '连接数据库失败，用户名或密码错误',
		'1046' => '选择数据库失败，请正确配置数据库名称',
		'1044' => '当前用户没有访问数据库的权限',
		'1048' => '字段不能为空',
		'1049' => '数据库不存在',
		'1051' => '数据表不存在',
		'1054' => '字段不存在',
		'1062' => '字段值重复，入库失败',//不中断
		'1064' => '可能原因：1.数据超长或类型不匹配；2.数据库记录重复',//不中断
		'1065' => '无效的SQL语句，SQL语句为空',//不中断
		'1081' => '不能建立Socket连接',
		'1129' => '数据库出现异常，请重启数据库',
		'1130' => '连接数据库失败，没有连接数据库的权限',
		'1133' => '数据库用户不存在',
		'1141' => '当前用户无权访问数据库',
		'1142' => '当前用户无权访问数据表',
		'1143' => '当前用户无权访问数据表中的字段',
		'1146' => '数据表不存在',
		'1149' => 'SQL语句语法错误',
		'1169' => '字段值重复，更新记录失败',//不中断
		'2003' => '请检查数据库服务器端口设置是否正确，默认端口为 3306',
		'2005' => '数据库服务器不存在',
		'1114' => 'Forum onlines reached the upper limit',
	);
	$display_errorall = '';
	$tempdir = $phpfile_array[$doctor_step];
	$dirname = $dir_array[$doctor_step];
	//foreach($phpfile_array as $tempdir=>$dirname) {
	$display_error = '';
	$mtime = explode(' ', microtime());
	$time_start = $mtime[1] + $mtime[0];
	if(!in_array($tempdir, array('templates', 'cache', 'discuzroot'))) exit('参数错误');
	
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
							$display_error .= "\t{$error_style_s}$file ---错误:{$style_e}";
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
			echo "$dir目录不存在或不可读取.";
		}
	}
	@unlink('./forumdata/checkfile.php');
	if($display_error == '') {
		$dot = '缓存文件';
		$dir == './' && $dot = 'php文件';
		$display_errorall .= "\n---------{$ok_style_s}{$dirname}{$style_e}下没有检测到有错误的$dot.\n";
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
	$records_style = "\n\n==={$title_style_s}配置文件检查{$title_style_e}=================================================$doctor_config\n==={$title_style_s}系统环境检查{$title_style_e}=================================================\n$doctor_env\n==={$title_style_s}文件错误检查{$title_style_e}=================================================\n$display_errorall\n==={$title_style_s}检查完毕{$title_style_e}=====================================================";
	$search_style_all = array($error_style_s, $style_e, $ok_style_s, $title_style_s, $title_style_e);
	$replace_style_all = array('', '', '', '', '');
	$records = str_replace($search_style_all, '', $records_style);
	echo "<h4>论坛医生诊断结果</h4><br /><p id=records style=\"display:\"><textarea name=\"contents\" readonly=\"readonly\">$records</textarea><br><br><input value=\"论坛样式代码\" onclick=\"records.style.display='none';records_style.style.display='';\"  type=\"button\">  <input value=\"将代码复制到我的剪切板\" onclick=\"copytoclip($('contents'))\" type=\"button\"></p>
	<p id=records_style style=\"display:none\"><textarea name=\"contents_style\" readonly=\"readonly\">$records_style</textarea><br><br><input value=\"清除样式代码\" onclick=\"records_style.style.display='none';records.style.display='';\"  type=\"button\"> <input value=\"将代码复制到我的剪切板\" onclick=\"copytoclip($('contents_style'))\" type=\"button\"></p>
	";
	htmlfooter();
} elseif ($action == 'dz_filecheck') {//搜索未知文件
		//搜索未知文件功能
	if(!file_exists("./config.inc.php") && !file_exists("config.php")) {
		htmlheader();
		cexit("<h4>请先上传config文件以保证您的数据库能正常链接！</h4>");
	}
	$do = isset($_GET['do']) ? $_GET['do'] : 'advance';
	
	$lang = array(
		'filecheck_fullcheck' => '搜索未知文件',
		'filecheck_fullcheck_select' => '搜索未知文件 - 选择需要搜索的目录',
		'filecheck_fullcheck_selectall' => '[搜索全部目录]',
		'filecheck_fullcheck_start' => '开始时间:',
		'filecheck_fullcheck_current' => '当前时间:',
		'filecheck_fullcheck_end' => '结束时间:',
		'filecheck_fullcheck_file' => '当前文件:',
		'filecheck_fullcheck_foundfile' => '发现未知文件数: ',
		'filecheck_fullcheck_nofound' => '没有发现任何未知文件'
	);
	if(!$discuzfiles = @file('./admin/discuzfiles.md5')) {
		show_tools_message('没有找到文件的MD5值');
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
		echo '<h4>搜索未知文件</h4>
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
} elseif ($action == 'dz_mysqlclear') {//数据库清理
	ob_implicit_flush();
	define('IN_DISCUZ', TRUE);
	if(@!include("./config.inc.php")) {
		if(@!include("./config.php")) {
			htmlheader();
			cexit("<h4>请先上传config文件以保证您的数据库能正常链接！</h4>");
		}
	}
	require './include/db_'.$database.'.class.php';
	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	$db->select_db($dbname);

	if(!get_cfg_var('register_globals')) {
		@extract($_GET, EXTR_SKIP);
	}
	$rpp			=	"1000"; //每次处理多少条数据
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
						'1'=>'冗余回复数据清理',
						'2'=>'冗余附件数据清理',
						'3'=>'冗余会员数据清理',
						'4'=>'冗余板块数据清理',
						'5'=>'主题信息清理',
						'6'=>'完成数据冗余清理'
					);
	$steps			=	count($action);
	$actionnow		=	isset($action[$step]) ? $action[$step] : '结束';
	$maxid			=	isset($maxid) ? $maxid : 0;
	$tableid		=	isset($tableid) ? $tableid : 1;
	htmlheader();
	if($step==0){
	?>
		<h4>数据库冗余数据清理</h4>
		<h5>清理项目详细信息</h5>
		<table>
		<tr><th width="30%">Posts表的清理</th><td>[<a href="?action=dz_mysqlclear&step=1&stay=1">单步清理</a>]</td></tr>
		<tr><th width="30%">Attachments表的清理</th><td>[<a href="?action=dz_mysqlclear&step=2&stay=1">单步清理</a>]</td></tr>
		<tr><th width="30%">Members表的清理</th><td>[<a href="?action=dz_mysqlclear&step=3&stay=1">单步清理</a>]</td></tr>
		<tr><th width="30%">Forums表的清理</th><td>[<a href="?action=dz_mysqlclear&step=4&stay=1">单步清理</a>]</td></tr>
		<tr><th width="30%">Threads表的清理</th><td>[<a href="?action=dz_mysqlclear&step=5&stay=1">单步清理</a>]</td></tr>
		<tr><th width="30%">所有表的清理</th><td>[<a href="?action=dz_mysqlclear&step=1&stay=0">全部清理</a>]</td></tr>
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
					$attachment = $db->num_rows($query) ? 1 : 0;//修复附件
					$query  = $db->query("SELECT pid, subject, rate FROM {$tablepre}posts WHERE tid='".$threads['tid']."' AND invisible='0' ORDER BY dateline LIMIT 1");
					$firstpost = $db->fetch_array($query);
					$firstpost['subject'] = trim($firstpost['subject']) ? $firstpost['subject'] : $threads['subject']; //针对某些转换过来的论坛的处理
					$firstpost['subject'] = addslashes($firstpost['subject']);
					@$firstpost['rate'] = $firstpost['rate'] / abs($firstpost['rate']);//修复发帖
					$query  = $db->query("SELECT author, dateline FROM {$tablepre}posts WHERE tid='".$threads['tid']."' AND invisible='0' ORDER BY dateline DESC LIMIT 1");
					$lastpost = $db->fetch_array($query);//修复最后发帖
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
		echo '<h4>数据库冗余数据清理</h4><table>
			  <tr><th>完成冗余数据清理</th></tr><tr>
			  <td><br>所有数据清理操作完毕.&nbsp;共处理<font color=red>'.$allconvertedrows.'</font>条数据.<br><br></td></tr></table>';
	}
	htmlfooter();
	
} elseif ($action == 'dz_repair_auto') {//修复自增长ID
	@set_time_limit(0);
	htmlheader();
	echo '<h4>Discuz! 自增长字段修复 </h4>';
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
			errorpage("<h4>很抱歉，该功能目前只支持Discuz!5.5版本和Discuz!6.0版本。</h4>",'',0);
		}
	}else {
		errorpage("./discuz_version.php文件不存在，请确定该文件的存在。",'',0);
	}
	echo '<h5>检查结果</h5>
	<table>
		<tr><th width="25%">数据表名</th><th width="25%">字段名</th><th width="25%">自增长状态</th></tr>';
	foreach($querysql as $key => $keyfield) {
		$tablestate = '正常';
		echo '<tr><td width="25%">'.$tablepre.$key.'</td><td width="25%">'.$keyfield.'</td>';
		if($query = @mysql_query("Describe $tablepre$key $keyfield")) {
			if(@mysql_num_rows($query) > 0) {
				$field = @mysql_fetch_array($query);
				if($field[3] != 'PRI') {
					@mysql_query("ALTER TABLE $tablepre$key ADD PRIMARY KEY ($keyfield)");
					$tablestate = '<font color="green"><b>已经修复</b></font>';
				}
				if(empty($field[5])) {
					mysql_query("ALTER TABLE $tablepre$key CHANGE $keyfield $keyfield $field[1] NOT NULL AUTO_INCREMENT");
					$tablestate = '<font color="green"><b>已经修复</b></font>';
				}
			} else {
				$tablestate = '<font color="red">字段不存在</font>';
			}
		} else {
			$tablestate = '<font color="red">表不存在</font>';
		}
		echo '<td width="25%">'.$tablestate.'</td></tr>';
	}
	echo '</table>';
	specialdiv();
	echo '<br />';
	htmlfooter();
	
} elseif ($action == 'dz_replace') {//内容替换
	
	htmlheader();
	$rpp			=	"500"; //每次处理多少条数据
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
		echo "<h4>帖子内容批量替换</h4><table>
					<tr>
						<th>暂停替换</th>
					</tr>";
		$threads_banned > 0 && print("<tr><td><br><li>".$threads_banned."个主题被放入回收站.</li><br></td></tr>");
		$threads_mod > 0 && print("<tr><td><br><li>".$threads_mod."个主题被放入审核列表.</li><br></td></tr>");
		$posts_mod > 0 && print("<tr><td><br><li>".$posts_mod."个回复被放入审核列表.</li><br></td></tr>");
		echo "<tr><td><br><li>替换了".$convertedrows."个帖子</li><br><br></td></tr>";
		echo "<tr><td><br><a href='?action=dz_replace&step=".$step."&start=".($end + 1 - $rpp * 2)."&stay=$stay&totalrows=$totalrows&convertedrows=$convertedrows&maxid=$maxid&replacesubmit=1&threads_banned=$threads_banned&threads_mod=$threads_mod&posts_mod=$posts_mod'>继续</a><br><br></td></tr>";
		echo "</table>";
		htmlfooter();
	}
	ob_implicit_flush();
	define('IN_DISCUZ', TRUE);
	if(@!include("./config.inc.php")) {
		if(@!include("./config.php")) {
			cexit("<h4>请先上传config文件以保证您的数据库能正常链接！</h4>");
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
				echo "<h4>帖子内容批量替换</h4><table><tr><th>提示信息</th></tr><tr><td>您还没有选择要过滤的词语. &nbsp [<a href=tools.php?action=dz_replace>返回</a>]</td></tr></table>";
				htmlfooter();
			} else {
				$fp = @fopen($selectwords_cache,w);
				$content = "<?php \n";
				$selectwords = implode(',',$selectwords);
				$content .= "\$selectwords = '$selectwords';\n?>";
				if(!@fwrite($fp,$content)) {
					echo "写入缓存文件$selectwords_cache 错误,请确认路径是否可写. &nbsp [<a href=tools.php?action=dz_replace>返回</a>]";
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
		$query = $db->query("SELECT find,replacement from {$tablepre}words where id in($selectwords)");//获得现有规则{BANNED}放回收站 {MOD}放进审核列表
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
		function topattern_array($source_array) { //将数组正则化
			$source_array = preg_replace("/\{(\d+)\}/",".{0,\\1}",$source_array);
			foreach($source_array as $key => $value) {
				$source_array[$key] = '/'.$value.'/i';
			}
			return $source_array;
		}
		$array_find = topattern_array($array_find);
		$array_findmod = topattern_array($array_findmod);
		$array_findbanned = topattern_array($array_findbanned);

		//查询posts表准备替换
		$sql = "SELECT pid, tid, first, subject, message from {$tablepre}posts where pid >= $start and pid <= $end";
		$query = $db->query($sql);
		while($row = $db->fetch_array($query)) {
			$pid = $row['pid'];
			$tid = $row['tid'];
			$subject = $row['subject'];
			$message = $row['message'];
			$first = $row['first'];
			$displayorder = 0;//  -2审核 -1回收站
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
				if($displayorder == '-2' && $first == 0) {//如成立就移到审核回复
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
			echo "<h4>帖子内容批量替换</h4><table>
						<tr>
							<th>批量替换完毕</th>
						</tr>";
			$threads_banned > 0 && print("<tr><td><br><li>".$threads_banned."个主题被放入回收站.</li><br></td></tr>");
			$threads_mod > 0 && print("<tr><td><br><li>".$threads_mod."个主题被放入审核列表.</li><br></td></tr>");
			$posts_mod > 0 && print("<tr><td><br><li>".$posts_mod."个回复被放入审核列表.</li><br></td></tr>");
			echo "<tr><td><br><li>替换了".$convertedrows."个帖子</li><br><br></td></tr>";
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
			echo "<h4>帖子内容批量替换</h4><table><tr><th>提示信息</th></tr><tr><td><br>对不起,现在还没有过滤规则,请<a href=\"./admincp.php?action=censor\" target='_blank'>进入论坛后台设置</a>.<br><br></td></tr></table>";
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
				<h4>批量替换帖子内容</h4>
				<table>
					<tr>
						<th><input class="checkbox" name="chkall" onclick="checkall(this.form)" type="checkbox" checked>序号</th>
						<th>不良词语</th>
						<th>替换为</th></tr>
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
				<input type="submit" name=replacesubmit value="开始替换">
		</form>
	<div class="specialdiv">
	<h6>注意：</h6>
	<ul>
	<li>本程序会按照论坛现有过滤规则操作所有帖子内容.如需修改请<a href="./admincp.php?action=censor" target='_blank'>进论坛后台</a>。</li>
	<li>上表列出了您论坛当前的过滤词语.</li>
	</ul></div><br><br>
	<?
	}
	htmlfooter();
} elseif ($action == 'dz_updatecache') {//更新缓存
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
			$clearmsg .= './forumdata/'.$dir.'清除失败.<br>';
		}
	}
	htmlheader();
	echo '<h4>更新缓存</h4><table><tr><th>提示信息</th></tr><tr><td>';
	if($clearmsg == '') $clearmsg = '更新缓存完毕.';
	echo $clearmsg.'</td></tr></table>';
	htmlfooter();
} elseif ($action == 'all_setadmin') {//重置管理员帐号密码，
	if($whereis == 'is_dz') {
		$sql_findadmin = "SELECT * FROM {$tablepre}members WHERE adminid=1";
		$sql_select = "SELECT uid FROM {$tablepre}members WHERE $_POST[loginfield] = '$_POST[where]'";		$username = 'username';
		$uid = 'uid';
		
		if(UC_CONNECT == 'mysql' || $dz_version < 610) {//判断连接ucenter的方式，如果是mysql方式，可以修改密码，否则提示去uc后台修改密码
			$rspw = 1;
			
		} else {
			$rspw = 0;
		}
		if($dz_version<700){//是否存在安全问答 7.0以后安全问答放在用户中心中
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
	<h4>找回管理员</h4>
	<?php
		//查询已经存在的管理员
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
				$info = "密码不能为空";
			}else{
				$md5_uc_password = md5(md5($uc_password).$salt);
				$config = file_get_contents($configfile);
				$config = preg_replace("/define\('UC_FOUNDERSALT',\s*'.*?'\);/i", "define('UC_FOUNDERSALT', '$salt');", $config);
				$config = preg_replace("/define\('UC_FOUNDERPW',\s*'.*?'\);/i", "define('UC_FOUNDERPW', '$md5_uc_password');", $config);
				$fp = @fopen($configfile, 'w');
				@fwrite($fp, $config);
				@fclose($fp);
				$info = "UCenter创始人密码更改成功为：$uc_password";
			}
		}else {
			if(@mysql_num_rows(mysql_query($sql_select)) < 1) {
				if($whereis == 'is_ec') {
					$info = '<font color="red">无此管理员用户！请检查用户名是否正确。</font>请<a href="?action=all_setadmin">重新输入</a> 管理员帐号.<br><br>';
				} else {
					$info = '<font color="red">无此用户！请检查用户名是否正确。</font>请<a href="?action=all_setadmin">重新输入</a> 或者重新注册.<br><br>';
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
					$_POST[loginfield] = $_POST[loginfield] == $username ? '用户名' : 'UID号码';
					$info = "已将$_POST[loginfield]为 $_POST[where] 的用户设置成管理员。<br><br>";
				}
				if($rspw) {
					if($whereis == 'is_ec') {
						if(mysql_query($sql_rspw)) {
							$mysql_affected_rows = mysql_affected_rows();
							$_POST[loginfield] = $_POST[loginfield] == $username ? '用户名' : 'UID号码';
							$info .= "已将$_POST[loginfield]为 $_POST[where] 的管理员密码设置为：$_POST[password]<br><br>";
						} else {
							$info = '<font color="red">失败请检查Mysql设置config.inc.php</font>';
						}
					} elseif($whereis == 'is_dz') {
						if($dz_version < 610){
							$salt = substr(md5(time()), 0, 6);
							$psw = md5(md5($_POST['password']).$salt);						
							//$psw = md5($_POST['password']);
							 //mysql_query("update {$tablepre}members set password='$psw' where $_POST[loginfield] = '$_POST[where]' limit 1");
							 mysql_query("update {$tablepre}members set password='$psw' ,salt='$salt',secques='' where $_POST[loginfield] = '$_POST[where]' limit 1");
						}else{
							//如果是dz，首先要连接到uc里面然后执行$sql_rspw修改密码
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
						$info .= "已将$_POST[loginfield]为 $_POST[where] 的管理员密码设置为：$_POST[password]<br><br>";
					} elseif($whereis == 'is_uch') {
						$salt = substr(md5(time()), 0, 6);
						$psw = md5(md5($_POST['password']).$salt);
						mysql_connect(UC_DBHOST, UC_DBUSER, UC_DBPW);
						$sql_rspw = "UPDATE ".UC_DBTABLEPRE."members SET password='".$psw."',salt='".$salt."' WHERE username = '$_POST[where]' limit 1";
						mysql_query($sql_rspw);
						$info .="已将$_POST[loginfield]为 $_POST[where] 的管理员密码设置为：$_POST[password]<br><br>";
					}elseif($whereis == 'is_ss'){
						if($ss_version >=70){
							$salt = substr(md5(time()), 0, 6);
							$psw = md5(md5($_POST['password']).$salt);
							mysql_connect(UC_DBHOST, UC_DBUSER, UC_DBPW);
							$sql_rspw = "UPDATE ".UC_DBTABLEPRE."members SET password='".$psw."',salt='".$salt."' WHERE username = '$_POST[where]' limit 1";
							mysql_query($sql_rspw);
						}
						$info .= "已将$_POST[loginfield]为 $_POST[where] 的管理员密码设置为：$_POST[password]<br><br>";
					}
			} else {
				$info_rspw = "管理员密码请登录UC后台去改。 <a href=11 target='_blank'>点击进入UC后台</a>";
			}
			}
		}
		
		errorpage($info,'重置管理员帐号',0,0);
	} else {
	?>
	<form action="?action=all_setadmin" method="post">
		<table>
			<?php
				if($whereis != 'is_uc') {
			?>
				<tr>
					<th>已存在管理员列表</th>
					<td><?php echo $admins; ?></td>
				</tr>
				<tr>
					<th width="30%"><input class="radio" type="radio" name="loginfield" value="<?php echo $username; ?>" checked >用户名<input class="radio" type="radio" name="loginfield" value="<?php echo $uid; ?>" >UID</th>
					<td width="70%"><input class="textinput" type="" name="where" size="25" maxlength="40">
					<?php if(!$rspw){
						echo '可以把指定的用户提升为管理员';
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
					<th width="30%">请输入密码</th>
					<td width="70%"><input class="textinput" type="text" name="password" size="25"></td>
				</tr>
			<?php
				}else{
			?>
				<tr>
					<th width="30%">密码修改提示</th>
					<td width="70%">管理员密码请登录UC后台去改。<a href=11 target='_blank'>点击进入UC后台</a> </td>
				</tr>
			<?php
				}
				if($secq) {
			?>
				<tr>
					<th width="30%">是否清除安全提问</th>
					<td width="70%"><input class="radio" name="issecques" value="1" checked="checked" type="radio">是&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="issecques" value="" class="radio" type="radio">否</td>
				</tr>
			<?php
				}
			?>
		</table>
		<input type="submit" name="loginsubmit" value="提 &nbsp; 交">
	</form>
	<?php
	}
	specialdiv();
	htmlfooter();
} elseif ($action == 'all_setlock') {//锁定工具箱
	touch($lockfile);
	if(file_exists($lockfile)) {
		echo '<meta http-equiv="refresh" content="3 url=?">';
		errorpage("<h6>成功关闭工具箱！强烈建议您在不需要本程序的时候及时进行删除</h6>",'锁定工具箱');
	} else {
		errorpage('注意您的目录没有写入权限，我们无法给您提供安全保障，请删除论坛根目录下的tool.php文件！','锁定工具箱');
	}
} elseif ($action == 'dz_moveattach') {//移动附件存放方式
	//初始化数据库连接帐号
	getdbcfg();
	//连接数据库
	define('IN_DISCUZ', TRUE);
	require_once TOOLS_ROOT."./config.inc.php";
	require_once TOOLS_ROOT."./include/db_mysql.class.php";
    $db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
	$dbuser = $dbpw = $dbname = $pconnect = NULL;
	htmlheader();
	echo "<h4>附件保存方式</h4>";
	$atoption = array(
		'0' => '标准(全部存入同一目录)',
		'1' => '按论坛存入不同目录',
		'2' => '按文件类型存入不同目录',
		'3' => '按月份存入不同目录',
		'4' => '按天存入不同目录',
	);
	if (!empty($_POST['moveattsubmit']) || $step == 1) {
		$rpp		=	"500"; //每次处理多少条数据
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
			$msg = "$atoption[$newattachsave] 移动附件完毕<br><li>共有".$totalrows."个附件数据</li><br /><li>移动了".$convertedrows."个附件</li>";
			errorpage($msg,'',0,0);
		}

	} else {
		$attachsave = $db->result($db->query("SELECT value FROM {$tablepre}settings WHERE variable = 'attachsave' LIMIT 1"), 0);
		$checked[$attachsave] = 'checked';
		echo "<form method=\"post\" action=\"tools.php?action=dz_moveattach\" onSubmit=\"return confirm('您确认已经备份好数据库和附件\\n可以进行附件移动操作么？');\">
		<table>
		<tr>
		<th>本设置将重新规范所有附件的存放方式。<font color=\"red\">注意：为防止发生意外，请注意备份数据库和附件。</font></th></tr><tr><td>";
		foreach($atoption as $key => $val){
			echo "<li style=\"list-style:none;\"><input class=\"radio\" name=\"newattachsave\" type=\"radio\" value=\"$key\" $checked[$key]>&nbsp; $val</input></li><br>";
		}
		echo "
		</td></tr></table>
		<input type=\"hidden\" id=\"oldattachsave\" name=\"oldattachsave\" style=\"display:none;\" value=\"$attachsave\">
		<input type=\"submit\" name=\"moveattsubmit\" value=\"提 &nbsp; 交\">
		</form>";
		specialdiv();
	}
	htmlfooter();
}elseif($action == 'dz_rplastpost'){//修复版块的最后回复
	//初始化数据库连接帐号
	getdbcfg();
	//连接数据库
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
			foreach($selectfid as $fid) {//存在要更新的FID 执行更新
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
			show_tools_message("重置成功", 'tools.php?action=dz_rplastpost');
			htmlfooter();

		}else {//不存在更新的FID 进入选择界面
			htmlheader();
				?>
				<h4>修复版块最后回复 </h4>
				<?php echo "<div class=\"specialdiv\">操作提示：<ul>
		<li>可以指定需要修复的版块，提交后程序会重新查询出版块的最后回复信息并且修复</li>
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
	
	        <h4 style="font-size:14px;">论坛版块列表</h4>
			<style>table.re_forum_list { margin-left:0; width:30%;} .re_forum_list input { margin:0; margin-right:10px; border-style:none;}</style>
	        <table class="re_forum_list">
				<tr><th><input class="checkbox re_forum_input" name="chkall" onclick="checkall(this.form)" type="checkbox" ><strong>全选</strong></th></tr>
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
			 <input type="submit" name="submit" value="提交" tabindex="3" />
			</div>
	        
	    </form>
	</div>
	<?php
		}
} elseif ($action == 'dz_rpthreads') {//批量修复主题
	//初始化数据库连接帐号
	getdbcfg();
	//连接数据库
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
			show_tools_message('帖子修复完毕，点这里返回', 'tools.php?action=dz_rpthreads');
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
		show_tools_message('正在处理第 '.$start.' 条到第 '.($start+500).' 条数据', 'tools.php?action=dz_rpthreads&rpthreadssubmit=true&fids='.$fidstr.'&start='.($start+500));
		htmlfooter();
	}else{
	htmlheader();
	?>
	<h4>批量修复主题 </h4>
				<?php echo "<div class=\"specialdiv\">操作提示：<ul>
		<li>当浏览某些帖子提示'未定义操作'，可以尝试用批量修复主题的功能进行修复</li>
		<li>可以指定需要修复的版块，提交后程序会批量修复指定版块的主题</li>
		<li>全选或者全不选都会修复所有论坛的主题</li>
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
	<h4 style="font-size:14px;">论坛版块列表</h4>
	<style>table.re_forum_list { margin-left:0; width:30%;} .re_forum_list input { margin:0; margin-right:10px; border-style:none;}</style>
	<form id="rpthreads" name="rpthreads" method="post"   action="tools.php?action=dz_rpthreads">
	<table class="re_forum_list">
	  <tr>
		<th><input type="checkbox" name="chkall" onclick="checkall(this.form)" class="checkbox re_forum_input" name="selectall" value="" />全选</th>
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
	  <input type="submit" name="rpthreadssubmit" value="提交" />
	</div>
	</form>
</div>
<?php
htmlfooter();
	}
} elseif ($action == 'all_logout') {//退出登陆
	setcookie('toolpassword', '', -86400 * 365);
	errorpage("<h6>您已成功退出,欢迎下次使用.强烈建议您在不使用时删除此文件.</h6>");
} elseif ($action == 'all_toolsback') {
	htmlheader();
?>
	<iframe style="margin-top:30px;" src="http://faq.comsenz.com/toolsback.php" width="600" height="500" frameborder="0"></iframe>
<?
	htmlfooter();
} else {
	htmlheader();
	?>
	<h4>欢迎您使用 Comsenz 系统维护工具箱</h4>
	<tr><td><br>
<?php
	if($installfile){
		echo '<font color="red" >出于安全考虑，如果安装完毕请删除'.$installfile."论坛安装文件</font><br />";
	}
	if($upgradefile){
		echo '<font color="red" >出于安全考虑，如果升级完毕请删除'.$upgradefile." 论坛升级文件</font>";
	}

?>
	<h5>Comsenz 系统维护工具箱功能简介：</h5>
	<ul>
<?php
	foreach($functionall as  $value) {
		$apps = explode('_', $value['0']);
		if(in_array(substr($whereis, 3), $apps) || $value['0'] == 'all') {	
				echo '<li>'.$value[2].'：'.$value[3].'</li>';
		}
	}
?>
	</ul>
	<?php
	specialdiv();
	htmlfooter();
}
//函数定义部分   程序逻辑在后  
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
		echo "<tr bgcolor='#CCCCCC'><td colspan=4 align='center'>检查数据表 Checking table $table</td></tr>";
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
			$view = '错误';
			$errortables += 1;
		} else {
			unset($bgcolor);
			unset($nooptimize);
			$view = '正常';
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
			echo "<tr><td colspan=4 align='center'>正在修复中 / Repairing table $table</td></tr>";
		} else {
			if(!$simple) {
				echo ">>>>>>>>正在修复中 / Repairing Table $table<br>\n";
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
			echo "<tr><td colspan=4 align='center'>优化数据表 Optimizing table $table</td></tr>";
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
		echo '<h4>论坛医生</h4><br><table>
		<tr><th>正在进行检查,请稍候</th></tr><tr><td>';
		echo "<br><a href=\"".$url."\">如果您的浏览器长时间没有自动跳转，请点击这里！</a><br><br>";
		echo '</td></tr></table>';
	} elseif($action == 'dz_replace') {
		echo '<h4>数据处理中</h4><table>
		<tr><th>正在进行'.$actionnow.'</th></tr><tr><td>';
		echo "正在处理 $start ---- $end 条数据[<a href='$url&stop=1' style='color:red'>停止运行</a>]";
		echo "<br><br><a href=\"".$url."\">如果您的浏览器长时间没有自动跳转，请点击这里！</a>";
		echo '</td></tr></table>';
	} else {
		echo '<h4>数据处理中</h4><table>
		<tr><th>正在进行'.$actionnow.'</th></tr><tr><td>';
		echo "正在处理 $start ---- $end 条数据[<a href='?action=$action' style='color:red'>停止运行</a>]";
		echo "<br><br><a href=\"".$url."\">如果您的浏览器长时间没有自动跳转，请点击这里！</a>";
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
		<meta http-equiv="Content-Type" content="text/html; charset=gbk">
		<title>Comsenz 系统维护工具箱 3.0UC</title>
		<style type="text/css"><!--
		body {font-family: Arial, Helvetica, sans-serif, "宋体";font-size: 12px;color:#000;line-height: 120%;padding:0;margin:0;background:#DDE0FF;overflow-x:hidden;word-break:break-all;white-space:normal;scrollbar-3d-light-color:#606BFF;scrollbar-highlight-color:#E3EFF9;scrollbar-face-color:#CEE3F4;scrollbar-arrow-color:#509AD8;scrollbar-shadow-color:#F0F1FF;scrollbar-base-color:#CEE3F4;}
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
		<h2>Comsenz 系统维护工具箱 '.VERSION.'</h2>
		<h3>[ <a href="?" target="_self">首页</a> ]&nbsp;
		[ <a href="?action=all_setlock" target="_self">锁定</a> ]&nbsp;
		[ <a href="?action=all_logout" target="_self">退出</a> ]&nbsp;</h3>
		</div>
		<div id="nav">';
		echo '<ul>';//导航菜单中根据不同的目录显示不同
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
		<div id="footer"><p>Comsenz 系统维护工具箱 &nbsp;
		版权所有 &copy;2001-2008 <a href="http://www.comsenz.com" style="color: #888888; text-decoration: none">
		康盛创想(北京)科技有限公司 Comsenz Inc.</a></font></td></tr><tr style="font-size: 0px; line-height: 0px; spacing: 0px; padding: 0px; background-color: #698CC3">
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
		$message ='<h4>工具箱登录</h4>
				<form action="?" method="post">
					<table class="specialtable"><tr>
					<td width="20%"><input class="textinput" type="password" name="toolpassword"></input></td>
					<td><input class="specialsubmit" type="submit" value="登 录"></input></td></tr></table>
					<input type="hidden" name="action" value="login">
				</form>';
	} else {
		$message = "<h4>$title</h4><br><br><table><tr><th>提示信息</th></tr><tr><td>$message</td></tr></table>";
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
	echo "<br><br><a href=\"$url\">如果您的浏览器没有自动跳转，请点击这里</a>";
	cexit("");
}
/**
 * 检查目录里下的文件权限函数
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
	echo '<h4>数据库冗余数据清理</h4><table>
			<tr"><th>正在进行'.$actionnow.'</th></tr><tr>
			<td>';
	if($stay) {
		$actions = isset($action[$nextstep]) ? $action[$nextstep] : '结束';
		echo "$actionnow 操作完毕.共处理<font color=red>{$convertedrows}</font>条数据.".($stay == 1 ? "&nbsp;&nbsp;&nbsp;&nbsp;" : '').'<br><br>';
		echo "<a href='?action=dz_mysqlclear&step=".$nextstep."&stay=1'>如果继续下一步操作( $actions )，请点击这里！</a><br>";
	} else {
		if(isset($action[$nextstep])) {
			echo '即将进入：'.$action[$nextstep].'......';
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
		echo "[<a href='?action=dz_mysqlclear' style='color:red'>停止运行</a>]<br><br><a href=\"".$scriptname."?step=".$nextstep."\">如果您的浏览器长时间没有自动跳转，请点击这里！</a>";
	}
	echo '</td></tr></table>';
}

function loadtable($table, $force = 0) {	//检查数据库表字符集函数
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

function validid($id, $table) {//获得数据表的最大和最小 id 值
	global $start, $maxid, $db, $tablepre;
	$sql = $db->query("SELECT MIN($id) AS minid, MAX($id) AS maxid FROM {$tablepre}$table");
	$result = $db->fetch_array($sql);
	$start = $result['minid'] ? $result['minid'] - 1 : 0;
	$maxid = $result['maxid'];
}

function specialdiv() {
	echo '<div class="specialdiv">
		<h6>注意：</h6>
		<ul>
		<li>对数据库操作可能会出现意外现象的发生及破坏，所以请先备份好数据库再进行上述操作！另外请您选择服务器压力比较小的时候进行一些优化操作。</li>
		<li>当您使用完毕Comsenz 系统维护工具箱后，请点击锁定工具箱以确保系统的安全！下次使用前只需要在/forumdata目录下删除tool.lock文件即可开始使用。</li></ul></div>';
}
function getplace() {
	global $lockfile, $cfgfile;
	$whereis = false;
	if(is_writeable('./config.inc.php') && is_writeable('./forumdata')) {//判断Discuz!目录
			$whereis = 'is_dz';
			$lockfile = './forumdata/tools.lock';
			$cfgfile = './config.inc.php';
	}
	if(is_writeable('./data/config.inc.php') && is_dir('./control')) {//判断UCenter目录
			$whereis = 'is_uc';
			$lockfile = './data/tools.lock';
			$cfgfile = './data/config.inc.php';
	}
	if(is_writeable('./config.php') && is_dir('source')) {//判断UCenter Home目录
			$whereis = 'is_uch';
			$lockfile = './data/tools.lock';
			$cfgfile = './config.php';
	}
	if(is_writeable('./config.php') && file_exists('./batch.common.php')) {//判断SupeSite目录
			$whereis = 'is_ss';
			$lockfile = './data/tools.lock';
			$cfgfile = './config.php';
	}
	if(is_writeable('./data/config.php')) {//判断ECShop目录
		$cfgpage = file_get_contents('./data/config.php');
		if(strpos($cfgpage, 'EC_CHARSET')) {
			$whereis = 'is_ec';
			$lockfile = './data/tools.lock';
			$cfgfile = './data/config.php';
		}
	}
	if(is_writeable('./data/inc.config.php')) {//判断ECmall目录
		$cfgpage = file_get_contents('./data/inc.config.php');
		if(strpos($cfgpage, 'ECM_KEY')) {
			$whereis = 'is_ecm';
			$lockfile = './data/tools.lock';
			$cfgfile = './data/inc.config.php';
		}
	}
	return $whereis;
}
function getdbcfg(){//获得数据库配置信息
	global $dbhost, $dbuser, $dbpw, $dbname, $dbcfg, $whereis, $cfgfile, $tablepre, $dbcharset,$dz_version,$ss_version;
	if(@!include($cfgfile)) {
			htmlheader();
			cexit("<h4>请先上传config文件以保证您的数据库能正常链接！</h4>");
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
	echo "<h4>$title</h4><br><br><table><tr><th>提示信息</th></tr><tr><td>$message<br><a href=\"$url\">如果您的浏览器没有自动跳转，请点击这里</a></td></tr></table>";
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