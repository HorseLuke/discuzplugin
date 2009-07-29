<?php

!defined('IN_FW') && exit('ACCESS IS NOT ALLOWED!');

class IndexController extends BaseController{
    public function IndexAction(){
        $version = implode(' / ',FWBase::getVersion());
        $author = implode(' / ',FWBase::getAuthor());
        $this->showMessage("框架启动成功！<br />版本号：{$version}<br />作者：{$author}");
    }
}