<?php

!defined('IN_FW') && exit('ACCESS IS NOT ALLOWED!');

class RecommendThreadController extends BaseController{
    public function ShowAction(){
        $recomendThread = $this->createModel('RecommendThread');
        if(!( $threadcount= $recomendThread->count())){
            $this->showMessage("没有任何被推荐的帖子，请返回！",'javascript:history.go(-1)');
        }else{
            $order=FWBase::getRequest('order');
            if(empty($order) || !in_array($order, array('t.dateline', 't.lastpost', 'fr.subject', 'fr.fid', 'fr.displayorder'))) {
                $order = 't.dateline';
            }
            $ordercheck = array($order => 'selected="selected"');
            $page = max(1, intval(FWBase::getRequest('page')));
            $page = $page > 100 ? 1 : $page;
            $startlimit = ($page - 1) * 60;
            $result = $recomendThread->order($order)->limit($startlimit.',60')->findall();
            $this->view->assign('multipage',multi($threadcount, 60, $page, "forumrecommend.php?order={$order}", 100));
            $this->view->assign('ordercheck',$ordercheck);
            $this->view->assign('recommendlist',$result);
            $this->view->assign('fwversion',implode(' / ',FWBase::getVersion()));
            $this->view->display();
        }
    }
}