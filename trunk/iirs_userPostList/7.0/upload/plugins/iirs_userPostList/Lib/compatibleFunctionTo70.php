<?php
/**
 * 函数库包，来源自各种不同的程序
 * 本函数库包仅适用于7.0
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * 来源自：Discuz 7.1!
 * 关于回复的简短处理显示，适合于“我的回复”、“动态”等
 *
 * @param string $str 要处理的信息
 * @param numeric $length 截取长度
 * @return string 处理结果
 */
function messagecutstr($str, $length) {
	global $language, $_DCACHE;
	include_once language('misc');
	include_once DISCUZ_ROOT.'./forumdata/cache/cache_post.php';
	$bbcodes = 'b|i|u|p|color|size|font|align|list|indent|float';
	$bbcodesclear = 'url|email|code|free|table|tr|td|img|swf|flash|attach|media|payto'.($_DCACHE['bbcodes_display'] ? '|'.implode('|', array_keys($_DCACHE['bbcodes_display'])) : '');
	$str = cutstr(strip_tags(preg_replace(array(
			"/\[hide=?\d*\](.+?)\[\/hide\]/is",
			"/\[quote](.*)\[\/quote]/siU",
			$language['post_edit_regexp'],
			"/\[($bbcodesclear)=?.*\].+?\[\/($bbcodesclear)\]/siU",
			"/\[($bbcodes)=?.*\]/iU",
			"/\[\/($bbcodes)\]/i",
		), array(
			"[b]$language[post_hidden][/b]",
			'',
			'',
			'',
			'',
			''
		), $str)), $length);
	$str = preg_replace($_DCACHE['smilies']['searcharray'], '', $str);
	return trim($str);
}