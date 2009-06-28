<?php 
/**
  Copyright 2009 Horse Luke（竹节虚）.

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.

* Output the HTML debugging string to HtmlFile in color coded glory for a sql query 
* For Discuz! SQLDEBUG ONLY!
* @Special Thans to Daevid Vincent [daevid@LockdownNetworks.com] and renothing[FreeDiscuz!], 044003[FreeDiscuz!]
* @version    0.0.2 Build 20090628 Rev 53 
* @date       2009-6-28
*/ 


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function sqldebug( $query ) 
{ 
   if( $query == '' ) return false; 

   global $SQL_INT; 
   if( !isset($SQL_INT) ){
	   //一个新的数据库请求周期产生了
	   $SQL_INT = 0;
	   echo "<div style=\"text-align:left;background-color:#FFC\"><b>注意：现在你已经打开了SQLDebug调试（简单写入日志版本），请随时留意并自行删除过时的SQLDebug HTML日志（位于文件夹/forumdata/logs/下）。</b></div>";
	   
	   //往日志内写入当前的url
	   $CurrentURL = htmlentities("http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$_SERVER['REQUEST_URI']}");
	   $str =  "\n\n<div><br />-----------------------------------------------------------<br /><b>URL:</b>{$CurrentURL}</div>";
	   sqlDebugWrite($str);
   }

   //[dv] this has to come first or you will have goofy results later. 
   $query = preg_replace("/['\"]([^'\"]*)['\"]/i", "'<FONT COLOR='#FF6600'>$1</FONT>'", $query, -1); 

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
                                   'OR ', //[dv] note the space. otherwise you match to 'COLOR' ;-) 
                                   'DESC', 
                                   'ASC', 
                                   'ON ' 
                                 ), 
                           array ( 
                                   "<FONT COLOR='#FF6600'><B>*</B></FONT>", 
                                   "<FONT COLOR='#00AA00'><B>SELECT</B> </FONT>", 
                                   "<FONT COLOR='#00AA00'><B>UPDATE</B> </FONT>", 
                                   "<FONT COLOR='#00AA00'><B>DELETE</B> </FONT>", 
                                   "<FONT COLOR='#00AA00'><B>INSERT</B> </FONT>", 
                                   "<FONT COLOR='#00AA00'><B>INTO</B></FONT>", 
                                   "<FONT COLOR='#00AA00'><B>VALUES</B></FONT>", 
                                   "<FONT COLOR='#00AA00'><B>FROM</B></FONT>", 
                                   "<FONT COLOR='#00CC00'><B>LEFT</B></FONT>", 
                                   "<FONT COLOR='#00CC00'><B>JOIN</B></FONT>", 
                                   "<FONT COLOR='#00AA00'><B>WHERE</B></FONT>", 
                                   "<FONT COLOR='#AA0000'><B>LIMIT</B></FONT>", 
                                   "<FONT COLOR='#00AA00'><B>ORDER BY</B></FONT>", 
                                   "<FONT COLOR='#0000AA'><B>AND</B></FONT>", 
                                   "<FONT COLOR='#0000AA'><B>OR</B> </FONT>", 
                                   "<FONT COLOR='#0000AA'><B>DESC</B></FONT>", 
                                   "<FONT COLOR='#0000AA'><B>ASC</B></FONT>", 
                                   "<FONT COLOR='#00DD00'><B>ON</B> </FONT>" 
                                 ), 
                           $query 
                         );

   if(function_exists(date_default_timezone_set)){
        date_default_timezone_set("PRC");
   }

   $time = date("Y-m-d h:i:s A");

   $query = "<div><FONT COLOR='#0000FF'><B>{$time} - SQL[".$SQL_INT."]:</B> ".$query."<FONT COLOR='#FF0000'>;</FONT></FONT></div>\n";
   sqlDebugWrite($query);

   $SQL_INT++;

} //SQL_DEBUG 



function sqlDebugWrite($str){
    $fileName = date('Y-m-d').'-SQLDebug.html';
	$logDir = DISCUZ_ROOT.'/forumdata/logs/';
	
	$fileHandle = @fopen("{$logDir}{$fileName}",'a');
    @fwrite($fileHandle,$str);
    @fclose($fileHandle);

}



?>