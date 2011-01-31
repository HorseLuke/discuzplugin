<?php
/**
 * Discuz! Plugin XMLFILE CHECKER FOR X1.5
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2010
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id$
 * 
 */

define('APPTYPEID', 98999);
define('CURSCRIPT', 'pluginXMLChecker');

require_once './source/class/class_core.php';
require_once './source/function/function_home.php';

$discuz = & discuz_core::instance();
$discuz->init();

require_once libfile('class/xml');

$xmlname = 'Discuz! Plugin';
$count = array('count' => 0, 'success' => 0, 'failure' => 0 );

$rootDir = DISCUZ_ROOT.'./source/plugin';
$rootDirInstance = dir($rootDir);

while(false !== ($entry = $rootDirInstance->read())) {
	$subDir = $rootDir.'/'.$entry;
	if(in_array($entry, array('.', '..')) || !is_dir($subDir)) {
		continue;
	}
	
	$d = dir($subDir);

	while(false !== ($subentry = $d->read())){
		if(preg_match('/^discuz\_plugin\_(\w+)?\.xml$/', $subentry)) {
			$count['count']++;
			$success = 0;
			$subfilepath = $subDir. '/'. $subentry;
			$subfilepath_Display = str_replace(DISCUZ_ROOT, '', $subfilepath);
			$xmldata = xml2array(file_get_contents($subfilepath));
			if(!is_array($xmldata) || !$xmldata){
				echo "<b>WARINING: CAN NOT PARSE THE XML FILE: {$subfilepath_Display}</b><br />";
			}elseif($xmldata['Title'] != $xmlname){
				echo "<b>WARINING: THE XML FILE DOES NOT CONTAIN XMLNAME '{$xmlname}': {$subfilepath_Display}</b><br />";
			}else{
				//echo "PARSE THE XML FILE OK: {$subfilepath_Display}<br />";
				$success = 1;
			}
			if($success == 1){
				$count['success']++;
			}else{
				$count['failure']++;
			}
		}
	}
}

echo '<br />-----------------------------------<br />';
echo $xmlname. ' XML CHECK DONE. RESULT:<br />';
echo nl2br(var_export($count, true));
