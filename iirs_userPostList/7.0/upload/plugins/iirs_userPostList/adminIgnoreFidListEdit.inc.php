<?php
/**
 * 
 * 用户帖子信息列表之——(后台)版块忽略设置
 * 本文件采取面向过程的编程方法
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: adminPermitEdit.inc.php 87 2009-11-13 14:10:00 horseluke $
 * @package iirs_userPostList_Discuz_7.0
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$identifier = 'iirs_userPostList';

//引入语言包
include_once(dirname(__FILE__)."/App/Lang/{$charset}.php");


if(!submitcheck('ignoreFidListEditsubmit')) {
    
    if(FALSE == @include_once(DISCUZ_ROOT.'./forumdata/cache/plugin_iirs_userPostList_ignoreFidList.php')){
        $_DPLUGIN['iirs_userPostList']['ignoreFidList']  = array();
    }
    
    //以下代码照抄dz7.1 admin/plugins.inc.php部分代码
    require_once DISCUZ_ROOT.'./include/forum.func.php';
    $forumselectshow = '<select name="ignoreFidListnew[]" size="18" multiple="multiple"><option value="0">'.discuzPlugin_scriptlang('deselect_forum_all').'</option>'.forumselect(FALSE, 0, 0).'</select>';
    if(!empty($_DPLUGIN['iirs_userPostList']['ignoreFidList'])){
        foreach($_DPLUGIN['iirs_userPostList']['ignoreFidList'] as $k => $v) {
            $forumselectshow = str_replace('<option value="'.$v.'">', '<option value="'.$v.'" selected>', $forumselectshow);
        }
    }
    
    showtips(discuzPlugin_scriptlang('adminIgnoreFidListEdit_intro'));
    showformheader("{$action}&operation={$operation}&identifier={$identifier}&mod={$mod}");
    showtableheader();
    showsetting(discuzPlugin_scriptlang('multiple_select_forum'), '', '', $forumselectshow);
    showsubmit('ignoreFidListEditsubmit');
    showtablefooter();
    showformfooter();

}else{
    
    //忽略板块列表数据检查和构造
    $_DPLUGIN['iirs_userPostList']['ignoreFidList']  = array();
    if(is_array($ignoreFidListnew) && !empty($ignoreFidListnew)){
        foreach ($ignoreFidListnew as $key => $fid){
            $fid = abs(intval($fid));
            //若等于0时，则表示清空忽略版块设置
            if($fid == 0){
                $_DPLUGIN['iirs_userPostList']['ignoreFidList'] = array();
                break;
            }else{
                $_DPLUGIN['iirs_userPostList']['ignoreFidList'][] = $fid;
            }
        }
    }
    
    //写入缓存
    $cachedata="if(!defined('IN_DISCUZ')) {exit('Access Denied');}\n\n\$_DPLUGIN['iirs_userPostList']['ignoreFidList']=".var_export($_DPLUGIN['iirs_userPostList']['ignoreFidList'],true).";";
    require_once './include/cache.func.php';
    writetocache('iirs_userPostList_ignoreFidList', '', $cachedata, 'plugin_');
    cpmsg(discuzPlugin_scriptlang('set_ok'), "admincp.php?action={$action}&operation={$operation}&identifier={$identifier}&mod={$mod}" ,'succeed');
}



/**
 * Discuz! 7.1插件接口动作语言翻译
 *
 * @param string $name 要翻译的字段名称
 * @return string 翻译结果
 */
function discuzPlugin_scriptlang($name){
    global $scriptlang,$identifier;
    if(isset($scriptlang[$identifier][$name])){
        return $scriptlang[$identifier][$name];
    }else{
        return $name;
    }
}


/**
 * 版块设置菜单，仅简单改造自函数原型forumselect，使其支持$selectedfid的传入
 *
 */
function discuzPlugin_forumselect($groupselectable = FALSE, $tableformat = 0, $selectedfid = array(), $showhide = FALSE){
	global $_DCACHE, $discuz_uid, $groupid, $fid, $gid, $indexname, $db, $tablepre;

	if(!isset($_DCACHE['forums'])) {
		require_once DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';
	}
	$forumcache = &$_DCACHE['forums'];

	$forumlist = $tableformat ? '<dl><dd><ul>' : '<optgroup label="&nbsp;">';
	foreach($forumcache as $forum) {
		if(!$forum['status'] && !$showhide) {
			continue;
		}
		if($forum['type'] == 'group') {
			if($tableformat) {
				$forumlist .= '</ul></dd></dl><dl><dt><a href="'.$indexname.'?gid='.$forum['fid'].'">'.$forum['name'].'</a></dt><dd><ul>';
			} else {
				$forumlist .= $groupselectable ? '<option value="'.$forum['fid'].'">'.$forum['name'].'</option>' : '</optgroup><optgroup label="--'.$forum['name'].'">';
			}
			$visible[$forum['fid']] = true;
		} elseif($forum['type'] == 'forum' && isset($visible[$forum['fup']]) && (!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'])) || strstr($forum['users'], "\t$discuz_uid\t"))) {
			if($tableformat) {
				$forumlist .= '<li'.($fid == $forum['fid'] ? ' class="current"' : '').'><a href="forumdisplay.php?fid='.$forum['fid'].'">'.$forum['name'].'</a></li>';
			} else {
				$forumlist .= '<option value="'.$forum['fid'].'"'.( !empty($selectedfid) && in_array($forum['fid'],$selectedfid) ? ' selected="selected" ' : '').'>'.$forum['name'].'</option>';
			}
			$visible[$forum['fid']] = true;
		} elseif($forum['type'] == 'sub' && isset($visible[$forum['fup']]) && (!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'])) || strstr($forum['users'], "\t$discuz_uid\t"))) {
			if($tableformat) {
				$forumlist .=  '<li class="sub'.($fid == $forum['fid'] ? ' current' : '').'"><a href="forumdisplay.php?fid='.$forum['fid'].'">'.$forum['name'].'</a></li>';
			} else {
				$forumlist .= '<option value="'.$forum['fid'].'"'.( !empty($selectedfid) && in_array($forum['fid'],$selectedfid) ? ' selected="selected" ' : '').'>&nbsp; &nbsp; &nbsp; '.$forum['name'].'</option>';
			}
		}
	}
	$forumlist .= $tableformat ? '</ul></dd></dl>' : '</optgroup>';
	$forumlist = str_replace($tableformat ? '<dl><dd><ul></ul></dd></dl>' : '<optgroup label="&nbsp;"></optgroup>', '', $forumlist);

	return $forumlist;
}