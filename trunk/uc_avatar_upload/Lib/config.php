<?php

!defined('IN_INTER') && exit('Fobbiden!');
/**
 * 参数类
 *
 */
class config extends ArrayObject{

    /**
     * 构建函数
     *
     */
    public function __construct(){
    }

    /**
     * 从dz6.1f同步参数值
     */
    /*
    public function syncFromDZ(){
        
    }
    */
    
    /**
     * 对参数进行设置(ok)
     *
     * @param array $newConfig 新的参数数组
     */
    public function set( $newConfig = array() ){
        foreach ($newConfig as $key => $value){
            $this->$key = $value;
        }
    }
    
    public function __get($name){
        $this->$name = null;
        return null;
    }
}

