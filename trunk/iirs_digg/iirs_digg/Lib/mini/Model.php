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
 */
class mini_Model{
    public $db;
    
    //模型对应的数据表名称
    protected $_tableName = '';
    
    //模型的真实数据表名称。当设置此项时，模型对应的数据表名称将无效
    protected $_realtableName = '';
    
    //表列，进行insert/update时将用此进行检查（除非在$params中对field进行了指定）。
    protected $_fields = array(
                                  'id',
                                  '_pk' => 'id',
                                  '_autoinc' => true,
                              );
    
    //数据SQL操作默认参数
    protected $_defaultparams = array(
                                  'field' => '*',              //要查询的表列
                                  'table' => '',               //真实数据表名称，不填将自动套用$this->_realtableName。若遇到表前缀可使用__TABLEPRE__代替
                                  'extra' => '',                //SQL的附加语句，比如JOIN/WHERE/GROUP BY/HAVING等
                                  'order' => '',               //进行排序的列
                                  'limit_start' => 0,          //限制条数的开始位置
                                  'limit_offset' => 0,          //限制取出的条数。若为0则表示不限制
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
     * 根据$this->_fields['_pk']的设定进行对应单条数据的查询
     *
     * @param string $value
     * @param array $params
     * @return unknown
     */
    public function find( $value, $params = array() ){
        if ( isset( $this->_fields['_pk'] )  &&  !empty( $this->_fields['_pk'] ) ) {
        	$params['extra'] = ' WHERE '. $this->_fields['_pk']. "='". $value. "' " ;
        	$params['limit_offset'] = 1;
        	return $this->select($params);
        }else{
            return false;
        }
        
    }
    
    /**
     * 进行数据库查询
     *
     * @param array $params 参数
     * @param string $indexkey 输出结果是否按照指定的indexkey进行数据排序？true则表示使用预定义的$this->params['_pk']
     */
    public function select( $params = array(), $indexkey = null ){
        $this->_parseParam( $params );
        $sql = 'SELECT '. $params['field']. ' FROM '. $params['table']. ' ' .$params['extra'];
        $datalist = $this->query($sql, $indexkey);
        if( $params['limit_offset'] == 1 && !empty($datalist) ){
            $this->data = end($datalist);
            return $this->data;
        }
        return $datalist;
    }
    
    /**
     * 进行数据库插入或者替换
     *
     * @param array $data 需要插入的数据
     * @param array $params 参数
     * @param array $type 类型。默认为'INSERT'，可选值为'REPLACE'
     */
    public function insert( $data = array(), $params = array(), $type = 'INSERT' ){
        if( empty($data) ){
            if( !empty($this->data) ){
                $data = $this->data;
            }else{
                return false;
            }
        }
        
        $this->_parseParam( $params );
        $type = ($type === 'REPLACE') ? $type : 'INSERT';
        $this->_datafilterBYfield( $data , $params['field'], $type );
        $sql = $sqlfields = $sqldata = '';
        foreach ($data as $key => $value){
            $sqlfields .= '`'. $key. '`,' ;
            $sqldata .= '\''. $value. '\',' ;
        }
        
        if( $sqlfields !== '' && $sqldata !== '' ){
            $sqlfields = '('. substr($sqlfields, 0, -1). ')';
            $sqldata = '('. substr($sqldata, 0, -1). ')';
            $sql = $type. ' INTO '. $params['table']. ' '. $sqlfields. ' VALUES '. $sqldata;
            return $this->execute( $sql );
        }else{
            return 0;
        }
    }
    
    /**
     * 进行数据库更新
     *
     * @param array $data 需要插入的数据
     * @param array $params 参数
     */
    public function update( $data = array() , $params = array() ){
        if( empty($data) ){
            if( !empty($this->data) ){
                $data = $this->data;
            }else{
                return false;
            }
        }
        
        //如果没有传入参数，则表示更新当前$this->data的主键所在数据行
        if( !isset($params['extra']) && isset( $this->_fields['_pk'] ) && !empty( $this->_fields['_pk'] ) && isset( $data[$this->_fields['_pk']] ) ){
            $params['extra'] = ' WHERE '. (string)$this->_fields['_pk']. "='". (string)$data[$this->_fields['_pk']]. "' " ;
        }

        
        $this->_parseParam( $params );
        $this->_datafilterBYfield( $data , $params['field'], 'UPDATE' );
        
        foreach ($data as $key => $value){
            if(  preg_match('/[\+\-\*\/]+/', $value) > 0  ){
                $data[$key] = $key. ' = '. $value;
            }else{
                $data[$key] = $key. " = '". $value. "'";
            }
        }
        if( !empty($data) ){
            $sql = 'UPDATE '. $params['table']. ' SET '. implode(',', $data). ' '. $params['extra'];
            return $this->execute($sql);
        }else{
            return 0;
        }
    }
    
    /**
     * 进行数据库删除。
     *
     * @param array $params 参数
     */
    public function delete( $params = array() ){
        //如果没有传入参数，则表示删除当前$this->data的主键所在数据行
        if( empty($params) && isset( $this->_fields['_pk'] )  &&  !empty( $this->_fields['_pk'] ) && isset($data[$this->_fields['_pk']]) && !empty($data[$this->_fields['_pk']])  ){
            $params['extra'] = ' WHERE '. $this->_fields['_pk']. "='". common::addslashes( $data[$this->_fields['_pk']], 1, true ). "'" ;
        }else{
            return false;
        }
        
        $this->_parseParam( $params );
        $sql = 'DELETE FROM '. $params['table']. ' ' .$params['extra'];
        return $this->execute( $sql );
    }
    
    /**
     * 直接进行数据库SQL查询
     *
     * @param string $sql SQL语句。遇到表前缀可使用__TABLEPRE__代替
     * @param string $indexkey 输出结果是否按照指定的indexkey进行数据排序？true则表示使用预定义的$this->params['_pk']
     */
    public function query( $sql, $indexkey = null ){
        $sql = str_replace( '__TABLEPRE__', $GLOBALS['tablepre'], $sql );
        $this->lastsql = $sql;
        $query = $this->db->query( $sql );
        $result = array();
        if( $indexkey === true && isset($this->params['_pk']) &&!empty($this->params['_pk']) ){
            $indexkey = $this->params['_pk'];
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
     * @param array $params 参数（引用）
     */
    protected function _parseParam( &$params ){
        $params = array_merge( $this->_defaultparams, $params );    //合并默认的参数
        
        if( $params['table'] === '' ){
            $params['table'] = $this->_realtableName;
        }
        
        if( $params['order'] !== '' ){
            $params['extra'] = $params['extra']. ' ORDER BY '. $params['order'];
        }
        
        if( $params['limit_offset'] > 0 ){
            $params['extra'] = $params['extra']. ' LIMIT '. $params['limit_start']. ','. $params['limit_offset'];
        }

    }
    
    /**
     * 使用预先设定的表列对数据进行简单过滤，保证插入的数据对应的列在目标数据表存在
     *
     * @param array $data 要过滤的数据（引用）
     * @param string $field 用于过滤的表列条件。若为'*'，则使用$this->_fields完成过滤。
     * @param string $sqltype SQL类型。可选值为REPLACE/INSERT/UPDATE。后两者将在使用$this->_fields时自动删除$data数组中主键为$this->_fields['_pk']的值 
     */
    protected function _datafilterBYfield( &$data , $fields = '*', $sqltype = 'REPLACE' ){
        if( $fields !== '*'  ){
            $fields = preg_replace( '/\,[ ]+/', ',', $fields );
            $fields = explode( ',', $fields );
        }else{
            $fields = $this->_fields;
            //自动删除$data数组中主键为$this->_fields['_pk']的值
            if( $sqltype !== 'REPLACE'  && isset( $this->_fields['_pk'] ) && $this->_fields['_pk'] !== '' && $this->_fields['_autoinc'] === true  ){
                unset($data[$this->_fields['_pk']]);
            }
            unset( $fields['_pk'], $fields['_autoinc'] );
        }
        
        $fields = array_flip( $fields );
        foreach( $data as $key => $value ){
            //非标量过滤
            if( !array_key_exists( $key, $fields ) || !is_scalar($value) ){
                unset($data[$key]);
            }
        }
    }
    
}


