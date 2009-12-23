<?php

/**
本文件原版权：
[Discuz!] (C)2001-2009 Comsenz Inc.
This is NOT a freeware, use is subject to license terms

本文件修改者：
$Id: db_pdoMysql.class.php 20294 2009-12-23 15:17:00 horseluke $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class dbstuff {

	var $version = '';
	var $querynum = 0;
	var $link = null;
	var $pdostatementInstance = null;     //存储pdo query返回的PDOStatement结果


	/*PDO改造完成*/
	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE, $dbcharset2 = '') {
		/*
		//原代码
		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		if(!$this->link = @$func($dbhost, $dbuser, $dbpw, 1)) {
			$halt && $this->halt('Can not connect to MySQL server');
		} else {
			if($this->version() > '4.1') {
				global $charset, $dbcharset;
				$dbcharset = $dbcharset2 ? $dbcharset2 : $dbcharset;
				$dbcharset = !$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $this->link);
			}
			$dbname && @mysql_select_db($dbname, $this->link);
		}
		*/
		$dsn = "mysql:host={$dbhost};dbname={$dbname}";
		$PDOConfig = array( PDO::ATTR_PERSISTENT => false , PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION );
		if($pconnect){
			$PDOConfig[PDO::ATTR_PERSISTENT] = true;
		}
		try {
			$this->link = new PDO($dsn, $dbuser, $dbpw, $PDOConfig);

			if($this->version() > '4.1') {
				global $charset, $dbcharset;
				$dbcharset = $dbcharset2 ? $dbcharset2 : $dbcharset;
				$dbcharset = !$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && $this->link->exec("SET ".$serverset);
			}
		} catch (PDOException $e) {
			$this->halt('[PDOException] Can not connect to MySQL server', '', $e);
		}
	}

	/*PDO改造完成*/
	function select_db($dbname) {
		/*
		//原代码
		return mysql_select_db($dbname, $this->link);
		*/
		try{
			$this->link->query("use `$dbname`");
		}catch (PDOException $e) {
			$this->halt('[PDOException] MySQL SELECT DATABASE Error ', '', $e);
		}
	}

	/*PDO改造完成*/
	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		/*
		//原代码
		return mysql_fetch_array($query, $result_type);
		*/
		try{
			$result_type = str_replace('MYSQL_', 'FETCH_', strtoupper($result_type));
			if( false !== strpos($result_type, 'FETCH_' )){
				$query->setFetchMode(constant("PDO::{$result_type}"));
			}else{
				$query->setFetchMode(PDO::FETCH_ASSOC);
			}
			return $query->fetch();
		}catch (PDOException $e) {
			$this->halt('[PDOException] MySQL fetch_array Error ', '', $e);
		}
	}


	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}

	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}

	
	/*PDO改造完成*/
	function query($sql, $type = '') {
		/*
		//原代码
		global $debug, $discuz_starttime, $sqldebug, $sqlspenttimes;

		if(defined('SYS_DEBUG') && SYS_DEBUG) {
			@include_once DISCUZ_ROOT.'./include/debug.func.php';
			sqldebug($sql);
		}

		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
			'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql, $this->link))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				$this->close();
				require DISCUZ_ROOT.'./config.inc.php';
				$this->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
				return $this->query($sql, 'RETRY'.$type);
			} elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt('MySQL Query Error', $sql);
			}
		}

		$this->querynum++;
		return $query;
		*/
		global $debug, $discuz_starttime, $sqldebug, $sqlspenttimes;

		if(defined('SYS_DEBUG') && SYS_DEBUG) {
			@include_once DISCUZ_ROOT.'./include/debug.func.php';
			sqldebug($sql);
		}

		try{
			if($type == 'UNBUFFERED'){
				$this->link->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
			}else{
				$this->link->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
			}
			/*
			//发现有时会出错（特别是replace），所以还是算了
			$this->pdostatementInstance = $this->link->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$this->pdostatementInstance->execute();
			*/
			$this->pdostatementInstance = $this->link->query($sql);
			$this->querynum++;
			return $this->pdostatementInstance;
		}catch (PDOException $e) {
			//不考虑MYSQL errno为2006, 2013的retry问题
			$this->halt('[PDOException] MySQL Query Error ', $sql, $e);
		}
	}

	/*PDO改造完成*/
	function affected_rows() {
		/*
		//原代码
		return mysql_affected_rows($this->link);
		*/
		return $this->pdostatementInstance == null ? 0 : $this->pdostatementInstance->rowCount() ;
	}

	/*PDO改造完成*/
	function error() {
		/*
		//原代码
		return (($this->link) ? mysql_error($this->link) : mysql_error());
		*/
		$linkError = $pdostatementError = '';
		if( $this->link != null ){
			$linkError = $this->link->errorInfo();
			$linkError = (null == $linkError[0]) ? '' : $linkError[2];
		}elseif( $this->pdostatementInstance != null ){
			$pdostatementError = $this->pdostatementInstance->errorInfo();
			$pdostatementError = (null == $pdostatementError[0]) ? '' : $pdostatementError[2];
		}
		return $linkError.$pdostatementError;
	}

	/*PDO改造完成*/
	function errno() {
		/*
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
		*/
		$linkError = $pdostatementError =0;
		if( $this->link != null ){
			$linkError = $this->link->errorInfo();
			$linkError = (null == $linkError[0]) ? 0 : $linkError[1];
		}elseif( $this->pdostatementInstance != null ){
			$pdostatementError = $this->pdostatementInstance->errorInfo();
			$pdostatementError = (null == $pdostatementError[0]) ? 0 : $pdostatementError[1];
		}
		if( $linkError != 0 ){
			return $linkError;
		}elseif( $pdostatementError != 0 ){
			return $pdostatementError;
		}else{
			return 0;
		}
	}

	/*PDO改造完成*/
	function result($query, $row = 0) {
		/*
		$query = @mysql_result($query, $row);
		return $query;
		*/
		try{
			$query = $query->fetch(PDO::FETCH_NUM,PDO::FETCH_ORI_NEXT,$row);
			return $query[0];
		}catch (PDOException $e) {
			$this->halt('[PDOException] MySQL result Error ', '', $e);
		}
	}

	/*PDO改造完成*/
	function num_rows($query) {
		/*
		$query = mysql_num_rows($query);
		return $query;
		*/
		
		try{
			//方法一：失败。因为$query->fetchAll()会导致游标一直到最后关闭，影响后面的$query->fetch()（假如有的话）
			//return count($query->fetchAll());
			
			/*
			//方法二：失败，因为无法复制游标，$temp->fetchAll()的结果总为false
			$temp = clone $query;
			$countNumRows = count($temp->fetchAll());
			unset($temp);
			return $countNumRows;
			*/
			
			//php手册：If the last SQL statement executed by the associated PDOStatement was a SELECT statement, some databases may return the number of rows returned by that statement. However, this behaviour is not guaranteed for all databases and should not be relied on for portable applications.
			return $query->rowCount();
			
			
		}catch (PDOException $e) {
			$this->halt('[PDOException] MySQL num_rows Error ', '', $e);
		}
	}

	/*PDO改造完成*/
	function num_fields($query) {
		/*
		return mysql_num_fields($query);
		*/
		try{
			$query = $query->columnCount();
			return $query;
		}catch (PDOException $e) {
			$this->halt('[PDOException] MySQL num_fields Error ', '', $e);
		}
	}

	/*PDO改造完成*/
	function free_result($query) {
		/*
		return mysql_free_result($query);
		*/
		$query = null;
	}

	/*PDO改造完成*/
	function insert_id() {
		/*
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
		*/
		$lastInsertId = $this->link == null ? 0 : $this->link->lastInsertId();
		return $lastInsertId >= 0 ? $lastInsertId : $this->result( $this->query("SELECT last_insert_id()"), 0);
	}


	/*PDO改造完成*/
	function fetch_row($query) {
		/*
		$query = mysql_fetch_row($query);
		return $query;
		*/
		try{
			$query = $query->fetch(PDO::FETCH_NUM);
			return $query;
		}catch (PDOException $e) {
			$this->halt('[PDOException] MySQL fetch_row Error ', '', $e);
		}

		return $query;
	}


	function fetch_fields($query) {
		/*
		return mysql_fetch_field($query);
		*/
		$this->halt('[PDOException] MySQL fetch_fields Error ', '', new PDOException('NOT SUPPORT THIS METHOD YET', 99999));
	}

	/*PDO改造完成*/
	function version() {
		/*
		if(empty($this->version)) {
		$this->version = mysql_get_server_info($this->link);
		}
		return $this->version;
		*/
		if(empty($this->version)) {
			$this->version = $this->link->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
		}
		return $this->version;
	}

	/*PDO改造完成*/
	function close() {
		/*
		return mysql_close($this->link);
		*/
		try {
			//$this->link->__destruct();
			$this->link = null;
			$this->pdostatementInstance = null;
		}catch (PDOException $e) {
			$this->halt('[PDOException] MySQL CLOSE Error ', '', $e);
		}
	}

	/*PDO改造完成，增加一个$e传pdo exception*/
	function halt($message = '', $sql = '', $e = '') {
		define('CACHE_FORBIDDEN', TRUE);
		require_once DISCUZ_ROOT.'./include/db_pdoMysql_error.inc.php';
	}
}

?>