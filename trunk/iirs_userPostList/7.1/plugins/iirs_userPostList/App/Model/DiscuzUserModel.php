<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class DiscuzUserModel extends BaseModel{
    
    public $uid;
    public $username;
    public $groupid;
    public $adminid;
    
    /**
     * Construct an instance of user
     *
     * @param numeric $uid 用户的uid值
     */
    public function __construct($uid){
        parent::__construct();
        if($uid == $GLOBALS['discuz_uid']){
            $this->uid = $GLOBALS['discuz_uid'];
            $this->username = $GLOBALS['discuz_user'];
            $this->groupid = $GLOBALS['groupid'];
            $this->adminid = $GLOBALS['adminid'];
        }else{
            $member = $this->db->fetch_first("SELECT uid,username,groupid,adminid FROM {$GLOBALS['tablepre']}members WHERE uid = '{$uid}'");
            if(!is_array($member)){
                showmessage('member_nonexistence',null,'HALTED');
            }else{
                $this->uid = $member['uid'];
                $this->username = $member['username'];
                $this->groupid = $member['groupid'];
                $this->adminid = $member['adminid'];
            }
        }
    }
    
    /**
     * 获取不允许访问的fidlist
     * 更改自search.php部分代码
     *
     */
    public function getDisallowVisitFidList(){
        $disallowVisitFidList=array();

        if(empty($GLOBALS['readaccess'])){
            $disallowVisitFidList[] = 0;         //只要不允许范文的fidlist中有0，则表示全部版块均禁止访问
        }else{
            foreach($GLOBALS['_DCACHE']['forums'] as $fid => $forum) {
                //不使用此条件：'group' == $forum['type']
                if( $forum['viewperm'] && !strstr($forum['viewperm'], "\t{$this->groupid}\t") ) {
                    $disallowVisitFidList[] = $fid;
                }
            }
        }
        return $disallowVisitFidList;
    }

    /**
     * 获取该用户的回帖列表。返回的结果数组格式如下（括号为标注）：
     * array('totalCount'（总共有多少条记录） => 99 , 
     *       'datalist' => array( 9（主题帖子tid） => array(
     *                                                     'subject'=>'帖子主题', 'fid' => 9, 'readperm' => 10, 'displayorder'（是否被删除到回收站）=>0, 'postlist'（此主题下此人的所有回帖） => array( 999（pid值）=> array (
     *                                                                                                                                                                                                                'sequence'（顺序）=>1, 'dateline'=>'09-2-11 11:50', 'invisible'（后台是否审核完毕） =>0, 'message'=>'回帖信息')))));
     * 
     * @param numeric $startnum 寻找的初始游标数值
     * @param numeric $limitnum 预计寻找多少条记录
     * @param array $ignoreFidList 不进行搜索的版块列表
     * @return array 一个结果数组
     */
    public function getPostlist($startnum,$limitnum,$ignoreFidList=array()){
        $result = array('totalCount'=>0 , 'datalist'=>array() );
        
        //只要发现$ignoreFidList含有0，则禁用此功能，返回初始化数值
        if(!in_array(0,$ignoreFidList)){
            if(!empty($ignoreFidList)){
                $ignoreFidListSQL=' AND p.fid NOT IN ('.implode(",",$ignoreFidList).') '; 
            }else{
                $ignoreFidListSQL='';
            }
            
            //查询有多少条记录
            $result['totalCount'] = $this->db->result_first("SELECT COUNT(*) FROM {$GLOBALS['tablepre']}posts p
                                                             WHERE p.authorid='{$this->uid}' {$ignoreFidListSQL} ");
            
            //假如记录数大于0，则继续查询
            if(!empty($result['totalCount'])){
                
                require_once DISCUZ_ROOT.'./include/post.func.php';
                
                $startnum = $startnum < $result['totalCount'] ? $startnum : 0 ;
                
                $query = $this->db->query("SELECT t.subject, t.displayorder, t.readperm, p.fid, p.tid, p.first, p.pid, p.dateline, p.invisible, p.anonymous, p.message FROM {$GLOBALS['tablepre']}posts p
                                           LEFT JOIN {$GLOBALS['tablepre']}threads t ON t.tid=p.tid
                                           WHERE p.authorid='{$this->uid}' {$ignoreFidListSQL} 
                                           ORDER BY p.dateline DESC LIMIT {$startnum},{$limitnum}");
                $sequence = 1;
                while($post = $this->db->fetch_array($query)) {

                    //仅处理回复帖
                    if($post['first'] != '1'){
                        $result['datalist'][$post['tid']]['subject'] = $post['subject'];
                        $result['datalist'][$post['tid']]['fid'] = $post['fid'];
                        $result['datalist'][$post['tid']]['readperm'] = $post['readperm'];
                        $result['datalist'][$post['tid']]['displayorder'] = $post['displayorder'];    //此主题是否被删除到回收站的
                        //仅处理非匿名帖
                        if('0' == $post['anonymous']){
                            $result['datalist'][$post['tid']]['postlist'][$post['pid']]['sequence'] = $sequence++;
                            $result['datalist'][$post['tid']]['postlist'][$post['pid']]['dateline'] = gmdate('y-n-j H:i', $post['dateline'] + $GLOBALS['timeoffset'] * 3600);
                            $result['datalist'][$post['tid']]['postlist'][$post['pid']]['invisible'] = $post['invisible'];
                            $result['datalist'][$post['tid']]['postlist'][$post['pid']]['message'] = messagecutstr($post['message'], 100);
                        }

                    }
                }
            }
        }
        return $result;
    }
    
    
    
    /**
     * 获取该用户的主题列表。部分代码来自forumdisplay.php
     * 返回的结果数组格式如下（括号为标注）：
     * array('totalCount'（总共有多少条记录）=>99 ,
     *       'datalist'=> array( 9（主题帖子tid） => array(......主题的各类信息，其中新增urlencode后的用户名字段lastposterenc和表示主题图标的字段icon)))) );
     *
     * @param numeric $startnum 寻找的初始游标数值
     * @param numeric $limitnum 预计寻找多少条记录
     * @param array $ignoreFidList 不进行搜索的版块列表
     * @return array 一个结果数组
     */
    public function getThreadlist($startnum,$limitnum,$ignoreFidList=array()){
        $result = array('totalCount'=>0 , 'datalist'=>array() );
        
        //只要发现$ignoreFidList含有0，则禁用此功能，返回初始化数值
        if(!in_array(0,$ignoreFidList)){
            if(!empty($ignoreFidList)){
                $ignoreFidListSQL=' AND t.fid NOT IN ('.implode(",",$ignoreFidList).') '; 
            }else{
                $ignoreFidListSQL='';
            }
            
            //查询有多少条记录
            $result['totalCount'] = $this->db->result_first("SELECT COUNT(*) FROM {$GLOBALS['tablepre']}threads t
                                                             WHERE t.authorid='{$this->uid}' {$ignoreFidListSQL} AND t.displayorder=0 ");
            
            //假如记录数大于0，则继续查询.t.displayorder=0表示帖子处在可见状态。
            if(!empty($result['totalCount'])){
                
                $startnum = $startnum < $result['totalCount'] ? $startnum : 0 ;
                
                $query = $this->db->query("SELECT t.* FROM {$GLOBALS['tablepre']}threads t
                                           WHERE t.authorid='{$this->uid}' {$ignoreFidListSQL} AND t.displayorder=0 
                                           ORDER BY t.dateline DESC LIMIT {$startnum},{$limitnum}");
                while($thread = $this->db->fetch_array($query)) {
                    $thread['icon'] = '<img src="images/icons/icon'.$thread['iconid'].'.gif" onerror="this.onerror=null;this.src=\'plugins/iirs_userPostList/Public/Images/icon1.gif\'" class="icon" />';
                    $thread['lastpost'] = gmdate('y-n-j H:i', $thread['lastpost'] + $GLOBALS['timeoffset'] * 3600);
                    $thread['lastposterenc'] = rawurlencode($thread['lastposter']);
                    $result['datalist'][$thread['tid']] = $thread;
                }
            }
        }
        return $result;
    }

}