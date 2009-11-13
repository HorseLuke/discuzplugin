<?php
/**
 * 
 * 基于Discuz!7.1架构下的微型MVC架构——主文件
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: miniMVC.php 85 2009-11-13 00:45:00 horseluke $
 * @package miniMVC_Discuz_7.1
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * 模型（M）基类，所有新模型需要进行extends baseModel
 *
 */
class BaseModel{
    protected $db;
    public function __construct(){
        $this->db=$GLOBALS['db'];
    }
}


/**
 * 控制器（C）基类，所有控制器需要进行extends baseController。
 * 
 * 由于Discuz! 7的View层太强大但不完全支持面向对象的写法，因此为适应Discuz!的标准，砍掉自写V层。
 * 如果需要进行模版输出，可进行如下操作：
 * 用户可使用$GLOBALS['模版变量名称']='模版变量值'进行赋值；
 * 也可使用$this->assign('模版变量名称','模版变量值')（或者直接传入一个数组），
 * 最后必须$this->display('模板名')，此时将会自动生成一个APP_TPL_FILENAME常量。
 * 在执行完控制器后，frontLoader（引导文件）将会自动判断是否存在该常量，
 * 如果存在，则根据APP_TPL_FILENAME值include template指定的总模板。
 * 请注意，URL中的inajax=1表示使用了Ajax调用。controller中有$this->_param['inajax']查看。
 */
class BaseController{
    protected $_param = array();
    
    public function __construct(){
        $this->_param['inajax'] = ( empty($GLOBALS['inajax']) || ($GLOBALS['inajax'] != 1) ) ? 0 : 1;
    }
    
    /**
     * 对模版变量赋值
     *
     * @param mixed $name 模版变量名称，若为数组，则无需$value。请注意key要符合变量名规则
     * @param mixed $value 模版变量值
     */
    public function assign($name,$value=''){
        if(is_array($name)){
            foreach ($name as $k => $v){
                //只要存在键值为name的request数据，就直接覆盖，防止外部干扰view数据输出（dz历史原因，作此处理）。
                if(isset($_REQUEST[$k]) || !isset($GLOBALS[$k])){
                    $GLOBALS[$k] = $v;
                }
            }
        }else{
            //只要存在键值为name的request数据，就直接覆盖，防止外部干扰view数据输出（dz历史原因，作此处理）。
            if(isset($_REQUEST[$name]) || !isset($GLOBALS[$name])){
                $GLOBALS[$name] = $value;
            }
        }
    }
    
    /**
     * 进行模版渲染（实际是变量外部化）和Discuz! View层调用前期常量定义，dz专用方法
     *
     * @param string $tplFileName 模版名称
     */
    public function display($tplFileName){
        define ('APP_TPL_FILENAME',$tplFileName);
    }
    
}
