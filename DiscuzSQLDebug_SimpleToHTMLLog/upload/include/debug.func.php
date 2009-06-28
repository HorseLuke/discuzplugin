<?php 
/**
  Copyright 2009 Horse Luke & 044003

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.

* Output the debugging string to File(HTML or LOG type)
* For Discuz! SQLDEBUG ONLY!
* @Special Thans to Daevid Vincent [daevid@LockdownNetworks.com] and renothing[FreeDiscuz!], 044003[FreeDiscuz!]
* @version    0.0.3 Build 20090629 Rev 54
* @date       2009-6-29
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//define whether output the logs to html file or not. if define it to false ,it will output to plain text(log suffix).  
define('LOG_TO_HTML', true); 


if(function_exists(date_default_timezone_set)) {
    date_default_timezone_set("PRC");
}



//Function: Main SqlDebug Function
function sqldebug($query)
{
   if($query == '') return false;

   global $SQL_INT; 
   if(!isset($SQL_INT)) {
		//A new database request generated
		$SQL_INT = 0;
        $fileName = getSqlDebugLogFileName();
		echo "<div style=\"text-align:left;background-color:#FFC;font-weight:bold;color:#000\">Note: SQLDebug(SimpleToHTMLLog) Mode is ON. Current log filename: <span style=\"color:#FF6600;font-weight:bold\">{$fileName}</span><br />Please keep an eye on the SQLDebug log files which are located in the folder '/forumdata/logs/' , and delete the outdated files when necessary.</div>";

		//Write the current url to log
		$currentURL = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$_SERVER['REQUEST_URI']}";
		if(true === LOG_TO_HTML){
            $currentURL =  "\n\n<div><br />-----------------------------------------------------------<br /><b>URL:</b>".htmlentities($currentURL)."</div>";
		}else{
            $currentURL = "\n\n-----------------------------------------------------------\nURL: {$currentURL}\n";
		}
		sqlDebugWrite($currentURL);
   }


	$time = date("Y-m-d h:i:s A");

	if(true === LOG_TO_HTML){
        $query = sqlStringToHTML($query);
	    $query = "<div><span style=\"color:#0000FF;font-weight:bold\">{$time} - SQL[{$SQL_INT}]:</span> {$query}<span style=\"color:#FF0000\">;</span></div>\n";
	}else{
	    $query = "{$time} - SQL[{$SQL_INT}]: {$query};\n";
	}

	sqlDebugWrite($query);

	$SQL_INT++;

} //SQL_DEBUG



//Function: Convert a sql query string to the color coded glory
function sqlStringToHTML($query){
	$query = preg_replace("/['\"]([^'\"]*)['\"]/i", "'<span style=\"color:#FF6600\">$1</span>'", $query, -1); 

	$query = str_ireplace(
		array (
			'*',
			'SELECT ',
			'UPDATE ',
			'DELETE ',
			'INSERT ',
			'INTO',
			'VALUES',
			'FROM',
			'LEFT', 
			'JOIN',
			'WHERE',
			'LIMIT',
			'ORDER BY',
			'AND',
			'OR ',
			'DESC',
			'ASC',
			'ON '
		),
		array (
			"<span style=\"color:#FF6600;font-weight:bold\">*</span>",
			"<span style=\"color:#00AA00;font-weight:bold\">SELECT </span>",
			"<span style=\"color:#00AA00;font-weight:bold\">UPDATE </span>",
			"<span style=\"color:#00AA00;font-weight:bold\">DELETE </span>", 
			"<span style=\"color:#00AA00;font-weight:bold\">INSERT </span>",
			"<span style=\"color:#00AA00;font-weight:bold\">INTO</span>",
			"<span style=\"color:#00AA00;font-weight:bold\">VALUES</span>",
			"<span style=\"color:#00AA00;font-weight:bold\">FROM</span>",
			"<span style=\"color:#00CC00;font-weight:bold\">LEFT</span>",
			"<span style=\"color:#00CC00;font-weight:bold\">JOIN</span>", 
			"<span style=\"color:#00AA00;font-weight:bold\">WHERE</span>",
			"<span style=\"color:#AA0000;font-weight:bold\">LIMIT</span>",
			"<span style=\"color:#00AA00;font-weight:bold\">ORDER BY</span>",
			"<span style=\"color:#0000AA;font-weight:bold\">AND</span>",
			"<span style=\"color:#0000AA;font-weight:bold\">OR </span>",
			"<span style=\"color:#0000AA;font-weight:bold\">DESC</span>",
			"<span style=\"color:#0000AA;font-weight:bold\">ASC</span>",
			"<span style=\"color:#00DD00;font-weight:bold\">ON </span>"
		),
		$query
	);
	return $query;
}


//Function: Write logs to file
function sqlDebugWrite($str) {
	$fileName = getSqlDebugLogFileName();
	$logDir = DISCUZ_ROOT.'/forumdata/logs/';

	$fileHandle = @fopen("{$logDir}{$fileName}",'a');
	@fwrite($fileHandle,$str);
	@fclose($fileHandle);

}


//Function: define and return the log filename
function getSqlDebugLogFileName(){
    if(true === LOG_TO_HTML){
        $fileName = date('Y-m-d').'-SQLDebug.html';
	}else{
        $fileName = date('Y-m-d').'-SQLDebug.log';
	}
	return $fileName;
}

?>