<?php
!defined('IN_DISCUZ') && exit('Access Denied');

/**
 * 嵌入点设计
 * 在本插件中，只当作视图层（View）设计使用
 * $Id$
 */

class plugin_iirs_digg {

    var $viewthread_postheader_return = array();
    var $viewthread_sidetop_return = array();


    function plugin_iirs_digg(){

    }


    /**
	 * 帖子鲜花鸡蛋总数
	 *
	 * @return array
	 */
    function viewthread_postheader_output() {

        if( empty($this->viewthread_postheader_return) ){
            $this->_parse_postlist_in_viewthread();
        }

        return $this->viewthread_postheader_return;

    }


    /**
	 * 用户鲜花鸡蛋总数
	 *
	 * @return array
	 */
    function viewthread_sidetop_output() {

        if( empty($this->viewthread_sidetop_return) ){
            $this->_parse_postlist_in_viewthread();
        }

        return $this->viewthread_sidetop_return;
    }


    
    function _parse_postlist_in_viewthread(){
        if(empty($GLOBALS['postlist']) || empty($GLOBALS['tid']) || !is_array($GLOBALS['postlist'])){
            return ;
        }
        
        $GLOBALS['page'] = isset($GLOBALS['page']) ? abs((int)$GLOBALS['page']) : 1;
        
        foreach ( $GLOBALS['postlist'] as $pid => $post ){
            $this->viewthread_postheader_return[] = $this->_html_send_digg($post);
            $this->viewthread_sidetop_return[] = $this->_html_memberfields_digg_count($post);
        }

    }

    function profile_baseinfo_bottom_output(){
        if( !isset($GLOBALS['member']) || empty($GLOBALS['member']) ){
            return '';
        }

        $str = <<<EOF
<table cellpadding="0" cellpadding="0" class="formtable">
<tr>
<th><img border="0" src="./plugins/iirs_digg/images/diggup.gif"  />&nbsp;鲜花数：</th>
<td>
{$GLOBALS['member']['diggup_count']}
</td>
</tr><tr>
<th><img border="0" src="./plugins/iirs_digg/images/diggdown.gif"  />&nbsp;鸡蛋数：</th>
<td>
{$GLOBALS['member']['diggdown_count']}
</td>
</tr></table>
EOF;

        return $str;

    }
    
    
    function _html_send_digg( $post ){
        $img_diggup = '<img border="0" align="absmiddle" src="./plugins/iirs_digg/images/send_diggup.gif" title="本楼已有鲜花：'. $post['diggup_count']. '"  />';
        $img_diggdown = '<img border="0" align="absmiddle" src="./plugins/iirs_digg/images/send_diggdown.gif" title="本楼已有鸡蛋：'. $post['diggdown_count']. '"  />';
        if( $post['authorid'] != $GLOBALS['discuz_uid'] ){
            $img_diggup = "<a href='plugin.php?id=iirs_digg:frontLoader&c=log&a=diggshow&logtype=diggup&pid={$post['pid']}&page={$GLOBALS['page']}&tid={$GLOBALS['tid']}' onclick=\"showWindow('iirs_digg_{$post[pid]}', this.href);doane(event);\">". $img_diggup. '</a>'; 
            $img_diggdown = "<a href='plugin.php?id=iirs_digg:frontLoader&c=log&a=diggshow&logtype=diggdown&pid={$post['pid']}&page={$GLOBALS['page']}&tid={$GLOBALS['tid']}' onclick=\"showWindow('iirs_digg_{$post[pid]}', this.href);doane(event);\">". $img_diggdown. '</a>'; 
        }
        return $img_diggup.$img_diggdown;
        
    }
    
    
    function _html_memberfields_digg_count( $post ){
        $html = '';
        
        //不是游客发的，同时也不是在审核过程中的，或者也不是在屏蔽状态下的，也不是匿名发的
        if( $post['authorid'] > 0 && $post['invisible'] == 0 && $post['status'] == 0 && $post['anonymous'] == 0 ){
            $html = <<<EOF
<table cellspacing="0" cellpadding="0" > <tr>
<td width="10%"></td><td width="45%"><img border="0" src="./plugins/iirs_digg/images/diggup.gif"  />&nbsp;{$post['mf_diggup_count']}</td><td width="45%"><img border="0" src="./plugins/iirs_digg/images/diggdown.gif" />&nbsp;{$post['mf_diggdown_count']}</td>
</tr></table>
EOF;

        }
        
        return $html;
    }

}

?>