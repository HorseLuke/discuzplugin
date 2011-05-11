<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); } class mini_Controller{ public function __construct(){ } public function checkpost( $id ){ if (!submitcheck($id)) { showmessage('POST CHECK FAIL! ACTION IS TERMINATED!', NULL, 'HALTED'); exit; } } public function assign($name,$value=''){ if(is_array($name)){ foreach ($name as $k => $v){ if(isset($_REQUEST[$k]) || !isset($GLOBALS[$k])){ $GLOBALS[$k] = $v; } } }else{ if(isset($_REQUEST[$name]) || !isset($GLOBALS[$name])){ $GLOBALS[$name] = $value; } } } public function display($tplFileName){ define ('APP_TPL_FILENAME',$tplFileName); } } if(!defined('IN_DISCUZ')) { exit('Access Denied'); } class mini_Model{ public $db; protected $_tableName = ''; protected $_realtableName = ''; protected $_field = array( 'id', '_pk' => 'id', '_autoinc' => true, ); protected $_defaultparam = array( 'field' => '*', 'table' => '', 'before_where' => '', 'where' => '', 'after_where' => '', 'order' => '', 'limit_start' => 0, 'limit_offset' => 0, 'sql_extra' => '', ); public $data = array(); public $lastsql = ''; public function __construct(){ $this->db = $GLOBALS['db']; $this->settableName(); } public function setProperty( $property, $value = '' ){ if( is_array($property) ){ foreach ( $property as $k => $v ){ $this->setProperty( $k, $v ); } }else{ if( $property == 'tableName' ){ $this->settableName( $value ); }else{ $property = '_'. $property; $this->$property = $value; } } return $this; } public function settableName( $name = '' ){ if( $name !== '' ){ $this->_tableName = $name; } if( $name !== '' || $this->_realtableName === '' ){ if( $this->_tableName === '' ){ $this->_tableName = str_replace( array('_Model', 'Model'), '', get_class($this) ); } $this->_realtableName = $GLOBALS['tablepre']. $this->_tableName; } return $this; } public function __set( $name, $value ){ throw new Exception('No such property can you set: '.$property ); } public function find( $value, $param = array() ){ if ( isset( $this->_field['_pk'] ) && !empty( $this->_field['_pk'] ) ) { $param['where'] = $this->_field['_pk']. "='". $value. "' " ; $param['limit_offset'] = 1; return $this->select($param); }else{ return false; } } public function select( $param = array(), $indexkey = null ){ $this->_parseParam( $param ); $sql = 'SELECT '. $param['field']. ' FROM '. $param['table']. ' ' .$param['sql_extra']; $datalist = $this->query($sql, $indexkey); if( $param['limit_offset'] == 1 && !empty($datalist) ){ $this->data = end($datalist); return $this->data; } return $datalist; } public function insert( $data = array(), $param = array(), $type = 'INSERT' ){ if( empty($data) ){ if( !empty($this->data) ){ $data = $this->data; }else{ return false; } } $this->_parseParam( $param ); $type = ($type === 'REPLACE') ? $type : 'INSERT'; $data = $this->datafilterBYfield( $data , $param['field'], $type ); $sql = $sqlfields = $sqldata = ''; foreach ($data as $key => $value){ $sqlfields .= '`'. $key. '`,' ; $sqldata .= '\''. $value. '\',' ; } if( $sqlfields !== '' && $sqldata !== '' ){ $sqlfields = '('. substr($sqlfields, 0, -1). ')'; $sqldata = '('. substr($sqldata, 0, -1). ')'; $sql = $type. ' INTO '. $param['table']. ' '. $sqlfields. ' VALUES '. $sqldata; return $this->execute( $sql ); }else{ return false; } } public function update( $data = array() , $param = array() ){ if( empty($data) ){ if( !empty($this->data) ){ $data = $this->data; }else{ return false; } } if( !isset($param['where']) && isset( $this->_field['_pk'] ) && !empty( $this->_field['_pk'] ) && isset( $data[$this->_field['_pk']] ) ){ $param['where'] = (string)$this->_field['_pk']. "='". (string)$data[$this->_field['_pk']]. "' " ; } $this->_parseParam( $param ); $data = $this->datafilterBYfield( $data , $param['field'], 'UPDATE' ); foreach ($data as $key => $value){ if( preg_match('/[\+\-\*\/]+/', $value) > 0 ){ $data[$key] = $key. ' = '. $value; }else{ $data[$key] = $key. " = '". $value. "'"; } } if( !empty($data) ){ $sql = 'UPDATE '. $param['table']. ' SET '. implode(',', $data). ' '. $param['sql_extra']; return $this->execute($sql); }else{ return false; } } public function delete( $param = array() ){ if( empty($param) && isset( $this->_field['_pk'] ) && !empty( $this->_field['_pk'] ) && isset($data[$this->_field['_pk']]) && !empty($data[$this->_field['_pk']]) ){ $param['where'] = $this->_field['_pk']. "='". common::addslashes( $data[$this->_field['_pk']], 1, true ). "'" ; }else{ return false; } $this->_parseParam( $param ); $sql = 'DELETE FROM '. $param['table']. ' ' .$param['sql_extra']; return $this->execute( $sql ); } public function query( $sql, $indexkey = null, $return_res = false ){ $sql = str_replace( '__TABLEPRE__', $GLOBALS['tablepre'], $sql ); $this->lastsql = $sql; $query = $this->db->query( $sql ); if( $return_res == true ){ return $query; }else{ $result = array(); if( $indexkey === true && isset($this->param['_pk']) &&!empty($this->param['_pk']) ){ $indexkey = $this->param['_pk']; } while( $current = $this->db->fetch_array($query) ) { if ( $indexkey === null || !isset($current[$indexkey]) ) { $result[] = $current; }else{ $result[$current[$indexkey]] = $current; } } return $result; } } public function execute( $sql, $type = '' ){ $sql = str_replace( '__TABLEPRE__', $GLOBALS['tablepre'], $sql ); $this->lastsql = $sql; $this->db->query($sql, $type ); $sqlpre = strtoupper( substr( trim($sql), 0, 3 ) ); switch ($sqlpre){ case 'INS': return $this->db->insert_id(); break; default: return intval( $this->db->affected_rows() ); break; } } protected function _parseParam( &$param ){ $param = array_merge( $this->_defaultparam, $param ); if( $param['table'] === '' ){ $param['table'] = $this->_realtableName; } $param['sql_extra'] = ' '. $param['before_where']. ' '; if( $param['where'] !== '' ){ $param['sql_extra'] .= ' WHERE '. $param['where']; } $param['sql_extra'] .= ' '.$param['after_where']. ' '; if( $param['order'] !== '' ){ $param['sql_extra'] .= ' ORDER BY '. $param['order']; } if( $param['limit_offset'] > 0 ){ $param['sql_extra'] .= ' LIMIT '. $param['limit_start']. ','. $param['limit_offset']; } } public function datafilterBYfield( $data , $field = '*', $sqltype = 'REPLACE' ){ if( $field !== '*' ){ $field = preg_replace( '/\,[ ]+/', ',', $field ); $field = explode( ',', $field ); }else{ $field = $this->_field; if( $sqltype !== 'REPLACE' && isset( $this->_field['_pk'] ) && $this->_field['_pk'] !== '' && $this->_field['_autoinc'] === true ){ unset($data[$this->_field['_pk']]); } unset( $field['_pk'], $field['_autoinc'] ); } $field = array_flip( $field ); foreach( $data as $key => $value ){ if( !array_key_exists( $key, $field ) || !is_scalar($value) ){ unset($data[$key]); } } return $data; } } if(!defined('IN_DISCUZ')) { exit('Access Denied'); } class common{ protected static $_objectInstance = array(); public static $config = array(); public static function getInstanceOf( $classname , $index = null, $filepath = null ){ if( null === $index ){ $index = $classname; } if( isset( self::$_objectInstance[$index] ) ){ $instance = self::$_objectInstance[$index]; if( !($instance instanceof $classname) ){ throw new Exception( "Key {$index} has been tied to other thing." ); } }else{ if( null !== $filepath && !class_exists($classname) ){ if( !is_file( $filepath ) ){ @trigger_error('No such controller file can PHP find:'.$filepath, 512 ); exit( 'No such controller file can PHP find' ); } require( realpath($filepath) ); } $instance = new $classname(); self::$_objectInstance[$index] = $instance; } return $instance; } public static function input($k, $var='GET', $default = null, $emptyCheck = false ) { $var = '_'. $var; $result = $default; if( isset($GLOBALS[$var][$k]) && isset($GLOBALS[$k]) ){ $result = $GLOBALS[$k]; } if( true === $emptyCheck && empty($result) ){ $result = $default; } return $result; } public static function addslashes($string, $force = 0, $strip = FALSE) { if(!ini_get('magic_quotes_gpc') || $force) { if(is_array($string)) { $temp = array(); foreach($string as $key => $val) { $key = addslashes($strip ? stripslashes($key) : $key); $temp[$key] = self::addslashes($val, $force, $strip); } $string = $temp; unset($temp); } else { $string = addslashes($strip ? stripslashes($string) : $string); } } return $string; } public static function config( $type, $value ){ switch ($type){ case 'get': return isset(self::$config[$value]) ? self::$config[$value] : null; break; case 'set': self::$config = array_merge(self::$config, $value); return true; break; } } }
/*$Id$*/