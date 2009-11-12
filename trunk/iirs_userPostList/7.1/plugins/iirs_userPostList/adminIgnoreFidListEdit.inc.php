<?php
/**
 * 
 * 用户帖子信息列表之——(后台)版块忽略设置
 * 本文件采取面向过程的编程方法
 * @author Horse Luke<horseluke@126.com>
 * @copyright Horse Luke, 2009
 * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
 * @version $Id: adminPermitEdit.inc.php 73 2009-11-06 20:30:00 horseluke $
 * @package iirs_userPostList_Discuz_7.1
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}





if(!submitcheck('ignoreFidListEditsubmit')) {
    
    if(FALSE == @include_once(DISCUZ_ROOT.'./forumdata/cache/plugin_iirs_userPostList_ignoreFidList.php')){
        $_DPLUGIN['iirs_userPostList']['ignoreFidList']  = array();
    }
    
    require_once DISCUZ_ROOT.'./include/forum.func.php';
    $forumselect = '<select name="ignoreFidListnew[]" size="25" multiple="multiple">'
    .'<option value="0">取消版块选择（一旦选择此将清空设置）</option>'
    .discuzPlugin_forumselect(FALSE, 0, $_DPLUGIN['iirs_userPostList']['ignoreFidList'] , TRUE)
    .'</select>';
    
    showtips('<li>由于Discuz!的权限设置及检查较为分散，出于效率的考虑，目前该插件无法自动禁止以下版块的帖子显示在列表中：'
            .'<ol>1、设置了访问密码的版块</ol><ol>2、设置了权限表达式的版块</ol></li>'
            .'<li>如果论坛有上述所说的版块，或者想额外忽略一些板块，请在这里手动设置。</li>'
            .'<li>设置成功后，插件将强制忽略这些板块的帖子（不管用户是否存在对本版块的访问权限）。</li>');
    showformheader("{$action}&operation={$operation}&identifier={$identifier}&mod={$mod}");
    showtableheader();
    showsetting('板块选择', '', '', $forumselect);
    showsubmit('ignoreFidListEditsubmit');
    showtablefooter();
    showformfooter();

}else{
    //忽略板块列表数据检查和构造
    $_DPLUGIN['iirs_userPostList']['ignoreFidList']  = array();
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
    
    //写入缓存
    $cachedata="if(!defined('IN_DISCUZ')) {exit('Access Denied');}\n\n\$_DPLUGIN['iirs_userPostList']['ignoreFidList']=".var_export($_DPLUGIN['iirs_userPostList']['ignoreFidList'],true).";";
    require_once './include/cache.func.php';
    writetocache('iirs_userPostList_ignoreFidList', '', $cachedata, 'plugin_');
    cpmsg('设置成功！', "admincp.php?action={$action}&operation={$operation}&identifier={$identifier}&mod={$mod}" ,'succeed');
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