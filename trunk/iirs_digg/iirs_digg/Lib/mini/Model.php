<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * 模型（M）基类，所有新模型需要进行extends Mini_Model
 * 代码思路曾经参考过以下程序，在此一并致谢：
 *     - PHP框架ThinkPHP 2.0 {@link http://www.thinkphp.cn}
 *     - ECMALL {@link http://ecmall.shopex.cn}
 * 
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id$
 * @package iirs_mini_framework_Discuz_7.1
 */
class mini_Model{
    public $db;
    
    //模型对应的数据表名称
    protected $_tableName = '';
    
    //模型的真实数据表名称。当设置此项时，模型对应的数据表名称将无效
    protected $_realtableName = '';
    
    //表列，进行insert/update时将用此进行检查（除非在$param中对field进行了指定）。
    protected $_field = array(
                                  'id',
                                  '_pk' => 'id',
                                  '_autoinc' => true,
                              );
    
    //数据SQL操作默认参数
    protected $_defaultparam = array(
                                  'field' => '*',              //要查询的表列
                                  'table' => '',               //真实数据表名称，不填将自动套用$this->_realtableName。若遇到表前缀可使用__TABLEPRE__代替
                                  'before_where' => '',                //SQL语句，附加在where之前，比如JOIN
                                  'where' => '',                //SQL语句，where主语句。里面不用写where
                                  'after_where' => '',                //SQL语句，附加在where之后，比如GROUP BY/HAVING等
                                  'order' => '',               //进行排序的列
                                  'limit_start' => 0,          //限制条数的开始位置
                                  'limit_offset' => 0,          //限制取出的条数。若为0则表示不限制
                                  'sql_extra' => '',            //经过$this->_parseParam后得出的sql附加语句。此部分将自动完成。在此索引添加的任何东西将会被在运行中自动删除
                              );
    
    //单条数据时进行的数据存储
    public $data = array();
    
    //最后一次运行的sql语句
    public $lastsql = '';

    /**
     * 构建函数，将模型和数据库实例联接起来，同时自动进行对应的数据表名称赋值
     *
     */
    public function __construct(){
        $this->db = $GLOBALS['db'];
        $this->settableName();
        
    }
    
    /**
     * 设置该类的protected属性，无须在属性名下面加_
     *
     * @param mixed $property 属性名，也可以是属性名和属性值的关联数组
     * @param mixed $value 属性值
     */
    public function setProperty( $property, $value = '' ){
        if( is_array($property) ){
            foreach ( $property as $k => $v ){
                $this->setProperty( $k, $v );
            }
        }else{
            if( $property == 'tableName' ){
                $this->settableName( $value );
            }else{
                $property = '_'. $property;
                $this->$property = $value;
            }
        }

        return $this;
    }
    
    /**
     * 进行对应的数据表名称赋值
     * 若没有设置真实表名，或者使用该函数传入表名，则自动进行真实表名的赋值
     *
     * @param string $name 表名。建议只有在实例化该基础类（即mini_Model）后才使用该函数，以便设置基础类的特定作用数据表。
     */
    public function settableName( $name = '' ){
        if( $name !== '' ){
            $this->_tableName = $name;
        }
        
        if( $name !== '' || $this->_realtableName === '' ){
            if( $this->_tableName === '' ){
                $this->_tableName = str_replace( array('_Model', 'Model'), '', get_class($this) );
            }
            $this->_realtableName = $GLOBALS['tablepre']. $this->_tableName;
        }
        return $this;
    }
    
    
    /**
     * 魔术设置类的属性值，直接禁止。
     *
     * @param string $name
     * @param value $value
     */
    public function __set( $name, $value ){
        throw new Exception('No such property can you set: '.$property );
    }
    

    
    /**
     * 根据$this->_field['_pk']的设定进行对应单条数据的查询
     *
     * @param string $value
     * @param array $param
     * @return unknown
     */
    public function find( $value, $param = array() ){
        if ( isset( $this->_field['_pk'] )  &&  !empty( $this->_field['_pk'] ) ) {
        	$param['where'] = $this->_field['_pk']. "='". $value. "' " ;
        	$param['limit_offset'] = 1;
        	return $this->select($param);
        }else{
            return false;
        }
        
    }
    
    /**
     * 进行数据库查询
     *
     * @param array $param 参数
     * @param string $indexkey 输出结果是否按照指定的indexkey进行数据排序？true则表示使用预定义的$this->param['_pk']
     */
    public function select( $param = array(), $indexkey = null ){
        $this->_parseParam( $param );
        $sql = 'SELECT '. $param['field']. ' FROM '. $param['table']. ' ' .$param['sql_extra'];
        $datalist = $this->query($sql, $indexkey);
        if( $param['limit_offset'] == 1 && !empty($datalist) ){
            $this->data = end($datalist);
            return $this->data;
        }
        return $datalist;
    }
    
    /**
     * 进行数据库插入或者替换
     *
     * @param array $data 需要插入的数据
     * @param array $param 参数
     * @param array $type 类型。默认为'INSERT'，可选值为'REPLACE'
     * @param mixed 成功则返回主键值(仅当使用SQL INSERT时)或者影响行数(使用SQL REPLACE等时);失败则返回false
     */
    public function insert( $data = array(), $param = array(), $type = 'INSERT' ){
        if( empty($data) ){
            if( !empty($this->data) ){
                $data = $this->data;
            }else{
                return false;
            }
        }
        
        $this->_parseParam( $param );
        $type = ($type === 'REPLACE') ? $type : 'INSERT';
        $data = $this->datafilterBYfield( $data , $param['field'], $type );
        $sql = $sqlfields = $sqldata = '';
        foreach ($data as $key => $value){
            $sqlfields .= '`'. $key. '`,' ;
            $sqldata .= '\''. $value. '\',' ;
        }
        
        if( $sqlfields !== '' && $sqldata !== '' ){
            $sqlfields = '('. substr($sqlfields, 0, -1). ')';
            $sqldata = '('. substr($sqldata, 0, -1). ')';
            $sql = $type. ' INTO '. $param['table']. ' '. $sqlfields. ' VALUES '. $sqldata;
            return $this->execute( $sql );
        }else{
            return false;
        }
    }
    
    /**
     * 进行数据库更新
     *
     * @param array $data 需要插入的数据
     * @param array $param 参数
     * @param mixed 成功则返回主键值(仅当使用SQL INSERT时)或者影响行数(使用SQL REPLACE等时);失败则返回false
     */
    public function update( $data = array() , $param = array() ){
        if( empty($data) ){
            if( !empty($this->data) ){
                $data = $this->data;
            }else{
                return false;
            }
        }
        
        //如果没有传入参数，则表示更新当前$this->data的主键所在数据行
        if( !isset($param['where']) && isset( $this->_field['_pk'] ) && !empty( $this->_field['_pk'] ) && isset( $data[$this->_field['_pk']] ) ){
            $param['where'] = (string)$this->_field['_pk']. "='". (string)$data[$this->_field['_pk']]. "' " ;
        }

        
        $this->_parseParam( $param );
        $data = $this->datafilterBYfield( $data , $param['field'], 'UPDATE' );
        
        foreach ($data as $key => $value){
            if(  preg_match('/[\+\-\*\/]+/', $value) > 0  ){
                $data[$key] = $key. ' = '. $value;    //因为此处的存在,因此需要程序员自行控制程序的健壮性
            }else{
                $data[$key] = $key. " = '". $value. "'";
            }
        }
        if( !empty($data) ){
            $sql = 'UPDATE '. $param['table']. ' SET '. implode(',', $data). ' '. $param['sql_extra'];
            return $this->execute($sql);
        }else{
            return false;
        }
    }
    
    /**
     * 进行数据库删除。
     *
     * @param array $param 参数
     */
    public function delete( $param = array() ){
        //如果没有传入参数，则表示删除当前$this->data的主键所在数据行
        if( empty($param) && isset( $this->_field['_pk'] )  &&  !empty( $this->_field['_pk'] ) && isset($data[$this->_field['_pk']]) && !empty($data[$this->_field['_pk']])  ){
            $param['where'] =  $this->_field['_pk']. "='". common::addslashes( $data[$this->_field['_pk']], 1, true ). "'" ;
        }else{
            return false;
        }
        
        $this->_parseParam( $param );
        $sql = 'DELETE FROM '. $param['table']. ' ' .$param['sql_extra'];
        return $this->execute( $sql );
    }
    
    /**
     * 直接进行数据库SQL查询
     *
     * @param string $sql SQL语句。遇到表前缀可使用__TABLEPRE__代替
     * @param string $indexkey 输出结果是否按照指定的indexkey进行数据排序？true则表示使用预定义的$this->param['_pk']
     * @param boolen $return_res 是否返回一个资源集合以供自行循环处理？默认为false。
     * @return mixed 返回查询结果（$return_res为false时，默认）；或者返回查询资源集合（$return_res为true时）
     */
    public function query( $sql, $indexkey = null, $return_res = false ){
        $sql = str_replace( '__TABLEPRE__', $GLOBALS['tablepre'], $sql );
        $this->lastsql = $sql;
        $query = $this->db->query( $sql );
        
        if( $return_res == true ){
            return $query;
            
        }else{
            $result = array();
            if( $indexkey === true && isset($this->param['_pk']) &&!empty($this->param['_pk']) ){
                $indexkey = $this->param['_pk'];
            }
            while( $current = $this->db->fetch_array($query) ) {
                if ( $indexkey === null || !isset($current[$indexkey]) ) {
                    $result[] = $current;
                }else{
                    $result[$current[$indexkey]] = $current;
                }
            }
            return $result;
        }
    }
    
    /**
     * 直接进行数据库SQL执行
     *
     * @param string $sql SQL语句。遇到表前缀可使用__TABLEPRE__代替
     * @param string $type SQL查询类型，可选值为UNBUFFERED
     */
    public function execute( $sql, $type = '' ){
        $sql = str_replace( '__TABLEPRE__', $GLOBALS['tablepre'], $sql );
        $this->lastsql = $sql;
        $this->db->query($sql, $type );
        
        $sqlpre = strtoupper( substr( trim($sql), 0, 3 ) );
        switch ($sqlpre){
            case 'INS':
                return $this->db->insert_id();
                break;
            default:
                return intval( $this->db->affected_rows() );
                break;
        }
    }
    
    /**
     * 对数据SQL操作参数进行解释，以便形成SQL语句
     *
     * @param array $param 参数（引用）
     */
    protected function _parseParam( &$param ){
        $param = array_merge( $this->_defaultparam, $param );    //合并默认的参数
        
        if( $param['table'] === '' ){
            $param['table'] = $this->_realtableName;
        }
        
        $param['sql_extra'] = ' '. $param['before_where']. ' ';
        
        if( $param['where'] !== '' ){
            $param['sql_extra'] .= ' WHERE '. $param['where'];
        }
        $param['sql_extra'] .= ' '.$param['after_where']. ' ';
        
        if( $param['order'] !== '' ){
            $param['sql_extra'] .= ' ORDER BY '. $param['order'];
        }
        
        if( $param['limit_offset'] > 0 ){
            $param['sql_extra'] .=  ' LIMIT '. $param['limit_start']. ','. $param['limit_offset'];
        }
        
        

    }
    
    /**
     * 使用预先设定的表列对数据进行简单过滤，保证插入的数据对应的列在目标数据表存在
     *
     * @param array $data 要过滤的数据
     * @param string $field 用于过滤的表列条件。若为'*'，则使用$this->_field完成过滤。
     * @param string $sqltype SQL类型。可选值为REPLACE/INSERT/UPDATE。后两者将在使用$this->_field时自动删除$data数组中主键为$this->_field['_pk']的值 
     */
    public function datafilterBYfield( $data , $field = '*', $sqltype = 'REPLACE' ){
        if( $field !== '*'  ){
            $field = preg_replace( '/\,[ ]+/', ',', $field );
            $field = explode( ',', $field );
        }else{
            $field = $this->_field;
            //自动删除$data数组中主键为$this->_field['_pk']的值
            if( $sqltype !== 'REPLACE'  && isset( $this->_field['_pk'] ) && $this->_field['_pk'] !== '' && $this->_field['_autoinc'] === true  ){
                unset($data[$this->_field['_pk']]);
            }
            unset( $field['_pk'], $field['_autoinc'] );
        }
        
        $field = array_flip( $field );
        foreach( $data as $key => $value ){
            //非标量过滤
            if( !array_key_exists( $key, $field ) || !is_scalar($value) ){
                unset($data[$key]);
            }
        }
        
        return $data;
        
    }
    
}


