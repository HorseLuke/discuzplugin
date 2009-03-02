<?php

/*
	[Discuz!] (C)2001-2007 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: menu.inc.php 12715 2008-03-08 05:06:03Z monkey $
*/

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
        exit('Access Denied');
}

showmenu('global', array(
	array('menu_settings_basic', 'settings&operation=basic'),
	array('menu_settings_access', 'settings&operation=access'),
	array('menu_settings_styles', 'settings&operation=styles'),
	array('menu_settings_optimize', 'settings&operation=seo'),
	array('menu_settings_functions', 'settings&operation=functions'),
	array('menu_settings_user', 'settings&operation=permissions'),
	array('menu_settings_credits', 'settings&operation=credits'),
	$isfounder ? array('menu_settings_mail', 'settings&operation=mail') :array(),
	array('menu_settings_sec', 'settings&operation=sec'),
	array('menu_settings_datetime', 'settings&operation=datetime'),
	array('menu_settings_attachments', 'settings&operation=attachments'),
	array('menu_settings_wap', 'settings&operation=wap'),
));
showmenu('forum', array(
	array('menu_forums', 'forums'),
	array('menu_forums_merge', 'forums&operation=merge'),
	array('menu_forums_threadtypes', 'threadtypes'),
	array('menu_styles', 'styles'),
	$isfounder ? array('menu_styles_templates', 'templates') : array(),
	array('menu_forums_infotypes', 'threadtypes&special=1'),
	array('menu_forums_infomodel', 'threadtypes&operation=typemodel'),
	array('menu_forums_infooption', 'threadtypes&operation=typeoption')
));
showmenu('user', array(
	array('menu_members_add', 'members&operation=add'),
	array('menu_members_edit', 'members'),
	array('menu_members_edit_ban_user', 'members&operation=ban'),
	array('menu_members_ipban', 'members&operation=ipban'),
	array('menu_members_credits', 'members&operation=reward'),
	array('menu_moderate_modmembers', 'moderate&operation=members'),
	array('menu_members_profile_fields', 'members&operation=profilefields'),
	array('menu_admingroups', 'groups&operation=admin'),
	array('menu_usergroups', 'groups&operation=user'),
	array('menu_ranks', 'groups&operation=ranks')
));
showmenu('topic', array(
	array('menu_moderate_posts', 'moderate&operation=threads'),
	array('menu_maint_threads', 'threads'),
	array('menu_maint_prune', 'prune'),
	array('menu_maint_attaches', 'attachments'),
	array('menu_posting_discuzcodes', 'misc&operation=discuzcodes'),
	array('menu_posting_tags', 'misc&operation=tags'),
	array('menu_posting_censors', 'misc&operation=censor'),
	array('menu_posting_smilies', 'smilies'),
	array('menu_thread_icon', 'misc&operation=icons'),
	array('menu_posting_attachtypes', 'misc&operation=attachtypes'),
	array('menu_moderate_recyclebin', 'recyclebin')
));
showmenu('extended', array(
	array('menu_plugins', 'plugins'),
	array('menu_google', 'google&operation=config'),
	array('menu_qihoo', 'qihoo&operation=config'),
	array('menu_video', 'video&operation=config'),
	array('menu_ecommerce', 'settings&operation=ecommerce')
));
showmenu('misc', array(
	array('menu_magics', 'magics&operation=config'),
	array('menu_medals', 'medals'),
	array('menu_misc_announces', 'announcements'),
	array('menu_misc_links', 'misc&operation=forumlinks'),
	array('menu_misc_crons', 'misc&operation=crons'),
	array('menu_misc_help', 'faq&operation=list'),
	array('menu_misc_onlinelist', 'misc&operation=onlinelist'),
	array('menu_custommenu_manage', 'misc&operation=custommenu')
));
showmenu('tools', array(
	array('menu_members_newsletter', 'members&operation=newsletter'),
	array('menu_tools_updatecaches', 'tools&operation=updatecache'),
	array('menu_tools_updatecounters', 'counter'),
	array('menu_tools_javascript', 'jswizard'),
	array('menu_tools_creditwizard', 'creditwizard'),
	array('menu_tools_fileperms', 'tools&operation=fileperms'),
	array('menu_tools_filecheck', 'checktools&operation=filecheck'),
	array('menu_forum_scheme', 'project'),
	$isfounder ? array('menu_database', 'database&operation=export') : array(),
	array('menu_logs', 'logs&operation=illegal')
));


$insenz = ($insenz = $db->result_first("SELECT value FROM {$tablepre}settings WHERE variable='insenz'")) ? unserialize($insenz) : array();
showmenu('adv', array(
	array('menu_adv_custom', 'advertisements'),
	array('menu_insenz_settings', 'insenz&operation=settings&do=basic'),
	array('menu_insenz_softad', 'insenz&operation=campaignlist&c_status=2'),
	$insenz['topicstatus'] ? array('menu_insenz_virtualforum', 'insenz&operation=virtualforum&c_status=2') : array(),
	array('menu_insenz_tools_myinsenz', 'http://www.insenz.com/publishers/', '_blank'),
	array('menu_insenz_tools_faq', 'http://www.insenz.com/publishers/faq/', '_blank')
));
$historymenus = array(array('menu_home', 'home'));
$query = $db->query("SELECT title, url FROM {$tablepre}admincustom WHERE uid='$discuz_uid' AND sort='0' ORDER BY dateline DESC LIMIT 0, 10");
while($custom = $db->fetch_array($query)) {
	$historymenus[] = array($custom['title'], substr($custom['url'], 19));
}
if(count($historymenus) > 1) {
	$historymenus[] = array('menu_home_clearhistorymenus', 'misc&operation=custommenu&do=clean', 'main', 'class="menulink"');
}


//D6功能移植to6.1f by horseluke
$lang['menu_intro']='模块简介';
$lang['menu_membersmerge']='合并用户';
$lang['menu_pmprune']='清理短消息';
showmenu('d6func', array(
	array('menu_intro', 'd6func&operation=intro'),
	array('menu_membersmerge', 'd6func&operation=membersmerge'),
	array('menu_pmprune', 'd6func&operation=pmprune'),
));
//D6功能移植to6.1f by horseluke


showmenu('index', $historymenus);

?>