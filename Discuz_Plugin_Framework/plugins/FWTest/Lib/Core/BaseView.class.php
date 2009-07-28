<?php

/**
 * @name Discuz! Plugin Framework Core BaseModel Class File
 * @author Horse Luke
 * @copyright Horse Luke, 2009. Licensed under the Apache License, Version 2.0
 * @version ver 0.0.1 build 20090724 rev 1 For Discuz! 7
 */

!defined('IN_FW') && exit();

class BaseView{
    public $t_var = array();
    
    /**
     * 对模版变量赋值
     *
     * @param mix $name 模版变量名称，若为数组，则无需$value。
     * @param mix $value 模版变量值
     */
    public function assign($name,$value=''){
        if(is_array($name)){
            foreach ($name as $k => $v){
                //$k = strval($k);
                $this->t_var[$k] = $v;
            }
        }else{
            //$name = strval($name);
            $this->t_var[$name] = $value;
        }
    }
    
    /**
     * 进行模版渲染和输出
     *
     * @param string $tplFileName 模版名称，可选
     */
    public function display($tplFileName=''){
        $controller = FWBase::getConfig('DEFAULT_CONTROLLER').'Controller';
        if (empty($tplFileName)){
            $tplFileName = FWBase::getConfig('DEFAULT_ACTION').'Action';
        }
        $tplFilePath = APP_PATH."/Tpl/{$controller}/{$tplFileName}.htm";
        if(!is_file($tplFilePath)){
                FWBase::throw_exception('未找到模版！无法显示结果！','FRAMEWORK_ERROR');
        }

        foreach ($this->t_var as $name => $value){
            $$name = $value;
        }
        //ob_start();
        require ($tplFilePath);
        //ob_end_flush();
        //exit;
    }
}