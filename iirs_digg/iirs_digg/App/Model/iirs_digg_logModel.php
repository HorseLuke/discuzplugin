<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * $Id$
 *
 */
class iirs_digg_logModel extends mini_Model {
    
    protected $_field = array(
                              'pid',
                              'authorid',
                              'loguid',
                              'logtype',
                              'dateline',
                              '_pk' => '',
                              '_autoinc' => false,
                              );
    
     public $piddata = array();
     public $tiddata = array();
     public $fiddata = array();
     
     public $count_disable = 0;
     
    /**
     * 检查要进行digg的pid可访问性
     *
     * @param int $pid
     * @param boolen $deep 是否进行包括帖子访问权限、板块访问权限等的深度检查？默认为true
     */
    public function check_pid_accessable( $pid, $deep = true ){
        $this->piddata = $this->select(  array(
                                                   'field' => 'pid, fid, tid, author, authorid, invisible, anonymous, status',
                                                   'table' => '__TABLEPRE__posts',
                                                   'where' => 'pid =  '. (int)$pid ,
                                                   'limit_offset' => 1,
                                                   )
                                             );
        //检查pid是否存在？
        if( empty($this->piddata) ){
            return -1;
        }

        //检查pid是否被隐藏或者在审核中
        if( $this->piddata['invisible'] != 0 || $this->piddata['status'] != 0 ){
            return -2;
        
        //检查pid所在的作者是否是自己？（游客除外）
        }elseif( $this->piddata['authorid'] == $GLOBALS['discuz_uid']  && $GLOBALS['discuz_uid'] != 0 ){
            return -3;
        }
        
        //检查pid是否是游客或者匿名发的？匿名时，检查是否不计入某人的鲜花鸡蛋总数？
        if( $this->piddata['anonymous'] != 0 && (int)common::config('get', 'count_disable_when_anonymous') == 1 ){
            $this->count_disable = 1;
        }elseif( $this->piddata['authorid'] == 0 ){
            $this->count_disable = 1;
        }
        
        //检查当前登录帐户是否已经向该pid发送了鲜花鸡蛋？（如果有游客发送了的话也算进去，也就是说有且仅有一个游客有权限发送一次鲜花鸡蛋）
        $this->data = $this->select(  array(
                                                   'field' => 'loguid',
                                                   'where' => 'pid =  '. (int)$this->piddata['pid']. ' AND loguid ='. (int)$GLOBALS['discuz_uid'] ,
                                                   'limit_offset' => 1,
                                                   )
                                             );
        if (!empty($this->data)) {
        	return -6;
        }

        //执行深度检查
        if( true === $deep ){
            $this->tiddata = $this->select(  array(
                                                       'field' => 'tid, closed, displayorder',
                                                       'table' => '__TABLEPRE__threads',
                                                       'where' => 'tid =  '. (int)$this->piddata['tid'] ,
                                                       'limit_offset' => 1,
                                                       )
                                                 );

            //检查pid所在的tid是否存在？
            if( empty($this->tiddata) ){
                return -1;
            }
        
            //检查pid所在的tid是否是被删除入回收站？
            if( $this->tiddata['displayorder'] != 0 ){
                return -4;
            
            //检查pid所在的tid是否是被关闭？    
            }elseif( $this->tiddata['closed'] != 0 ){
                return -5;
            }
        
            $this->fiddata = $this->select(  array(
                                                       'field' => 'f.fid, f.viewperm, f.formulaperm, a.allowview',
                                                       'table' => '__TABLEPRE__forumfields f',
                                                       'before_where' => 'LEFT JOIN __TABLEPRE__access a ON a.fid = f.fid',
                                                       'where' => 'f.fid =  '. (int)$this->piddata['fid'] ,
                                                       'limit_offset' => 1,
                                                       )
                                                 );
        
            //检查pid所在的fid是否存在？
            if( empty($this->fiddata) ){
                return -1;
            }
        
            //检查pid所在的fid是否允许该用户访问？（改动自dz代码）[此处违反了model原则，不过改起来有点麻烦，所以还算了]
            if($this->fiddata['viewperm'] && !forumperm($this->fiddata['viewperm']) && !$this->fiddata['allowview']) {
                showmessagenoperm('viewperm', $this->fiddata['fid']);
            } elseif ($this->fiddata['formulaperm']) {
                formulaperm($this->fiddata['formulaperm']);
            }
        
        }
        
        return 0;
        
    }
    
    /**
     * 检查用户的可操作性
     *
     * @param string $logtype 操作,可选值为diggup/diggdown
     * @param int $credit_type 积分类型代号,可选值为1到8
     * @param int $credit_num 积分数量
     * @return unknown
     */
    public function check_user_accessable( $credit_type, $credit_num ){
        //检查当前登录用户是否具有扔鲜花鸡蛋的权限？
        $allow_usergroup = unserialize(common::config('get', 'allow_usergroup'));
        
        if( !in_array( $GLOBALS['groupid'], $allow_usergroup ) ){
            return -1;
        }
        
        //检查是否具有足够备用钱？
        $extcredits = 'extcredits'.$credit_type;
        if( $GLOBALS[$extcredits] - $credit_num < 0 ){
            return -2;
        }
        
        return 0;
    }
    
    /**
     * 进行digg写入操作
     *
     * @param string $logtype
     * @return boolen
     */
    public function diggupdate($logtype){
        if( empty($this->piddata) ){
            return false;
        }
        
        $data = $this->piddata;
        
        $data['loguid'] = $GLOBALS['discuz_uid'];
        $data['logtype'] = $logtype;
        $data['dateline'] = $GLOBALS['timestamp'];
        
        $this->data = common::addslashes($data, 1, true);
        
        $this->insert( $this->data, array(), 'INSERT' );
        
        $logtype_count = common::addslashes($logtype.'_count', 1);
        $this->data[$logtype_count] = $logtype_count . ' + 1 ';

        $this->update( $this->data, array(
                                             'field' => "pid, {$logtype_count}",
                                             'table' => '__TABLEPRE__posts',
                                             'where' => "pid = '{$this->data['pid']}'"
                                         )
                     );
                     

        if( $this->count_disable == 0){
            $this->update( $this->data, array(
                                             'field' => "uid, {$logtype_count}",
                                             'table' => '__TABLEPRE__memberfields',
                                             'where' => "uid = '{$this->data['authorid']}'"
                                         )
                         );
        }
        
        
        
        $credit_type = (int)common::config('get', 'credit_type');
        $credit_num = (int)common::config('get', 'credit_'. $logtype. '_num') * (-1);
        updatecredits( $this->data['loguid'], array( $credit_type => $credit_num ) );
        
        if( $this->data['authorid'] != 0 ){
            if( $this->data['logtype'] == 'diggup' ){
                $logtype_message = $GLOBALS['discuz_user']. '送你一束鲜花';
            }else{
                $logtype_message = $GLOBALS['discuz_user']. '向你扔一个鸡蛋';
            }
            $message = $logtype_message. '，并对你说：'. (string)common::input('extramessage', 'POST', '无', true);
            $message = dhtmlspecialchars($message). "<br /><a href=\"redirect.php?goto=findpost&pid={$this->data['pid']}\">[被操作帖子请点击这里]</a>";
            $message= '<div>'. dhtmlspecialchars($logtype_message). ' {time}<br />'. $message. '</div>';
            sendnotice($this->data['authorid'], $message, 'systempm');
        }
        
        return true;
        
    }
    
    
}