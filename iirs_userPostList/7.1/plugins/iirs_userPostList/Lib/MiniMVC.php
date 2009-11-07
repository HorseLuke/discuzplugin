<?php
/**
 * 
 * 基于Discuz!7.1架构下的微型MVC架构——主文件
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: miniMVC.php 74 2009-11-06 20:30:00 horseluke $
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
    public $db;
    public function __construct(){
        $this->db=$GLOBALS['db'];
    }
}


/**
 * 控制器（C）基类，所有控制器需要进行extends baseController。
 * 由于Discuz! 7的View层太强大但不完全支持面向对象的写法，
 * 因此为适应Discuz!的标准，砍掉V层，改由在运行时，在controller输出的模版文件全部采取block return包含方式，
 * 其return的数据存储在baseController中的$viewData数组中,并且指定$viewFilename（总模板名称）
 * $viewData的格式为$viewData['模版变量名']= 输出的数据;
 * 在执行完控制器后，frontLoader（引导文件）判断该数值是否为空，然后根据$viewFilename值include指定的总模板。
 * URL中的inajax=1表示使用了Ajax调用。
 *
 */
class BaseController{
    protected $_param = array();
    public $viewData = array();
    public $viewFilename = '';
    
    public function __construct(){
        
    }
    
}