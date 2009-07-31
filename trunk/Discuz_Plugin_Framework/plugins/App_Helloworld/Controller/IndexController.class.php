<?php

!defined('IN_FW') && exit('ACCESS IS NOT ALLOWED!');

class IndexController extends BaseController{
    
    
    /**
     * 这是一个最简单的hello实例
     * 运行方法：http://你的域名/helloworld.php
     * 或者http://你的域名/helloworld.php?c=index&a=index
     *
     */
    public function IndexAction(){
        $version = implode(' / ',FWBase::getVersion());
        $author = implode(' / ',FWBase::getAuthor());
        $this->showMessage("框架启动成功！<br />版本号：{$version}<br />作者：{$author}");
    }
    
    
    /**
     * 这里展示了如何使用缓存技术
     * 运行方法：http://你的域名/helloworld.php&a=cache
     * 或者http://你的域名/helloworld.php?c=index&a=cache
     *
     */
    public function CacheAction(){
        $cache = FileCache::getInstance();
        if(!($result = $cache->load('CACHE_EXAMPLE'))){
            $seconds = 7200;
            $result = '这是缓存了的信息。缓存时间为'.date("Y-M-d H:i:s",$cache->time).'。下一缓存更新时间为'.date("Y-M-d H:i:s",$cache->time+$seconds);
            $cache->save($result,'CACHE_EXAMPLE',$seconds);
            $this->showMessage("指定的缓存标记'CACHE_EXAMPLE'内没有读取到任何信息。<br />正在写入缓存缓存标记为'CACHE_EXAMPLE'的缓存中......请刷新页面查看。");
        }else{
            $this->showMessage("使用了缓存信息。缓存内容为：<br />{$result}");
        }
    }
    
    
}