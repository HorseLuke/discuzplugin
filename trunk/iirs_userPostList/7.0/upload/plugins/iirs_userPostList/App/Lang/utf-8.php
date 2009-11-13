<?php
$scriptlang['iirs_userPostList'] = array(
	'multiple_select_forum' => '版块选择（按Ctrl多选）',
	'deselect_forum_all' => '取消版块选择（一旦选择此将清空设置）',
	'adminIgnoreFidListEdit_intro' => '<li>由于Discuz!的权限设置及检查较为分散，出于效率的考虑，目前该插件无法自动禁止以下版块的帖子显示在列表中：
                    <ol>1、设置了访问密码的版块</ol><ol>2、设置了权限表达式的版块</ol></li>
                    <li>如果论坛有上述所说的版块，或者想额外忽略一些板块，请在这里手动设置。</li>
                    <li>设置成功后，插件将强制忽略这些板块的帖子（不管用户是否存在对本版块的访问权限）。</li>',
	'set_ok' => '设置成功！',
	'use_personal_center_to_see_own_threads' => '要查看自己发布的帖子，请点击这里进入“个人中心”的“我的帖子”查看。',
	'view_his_posts' => '查看回复的帖子',
	'view_his_threads' => '查看发布的主题',
	'plugin_setting_cache_lost' => '本插件原有设置的缓存丢失，将影响插件的运行。请管理员到后台重新设置该插件参数！',
);

$templatelang['iirs_userPostList'] = array(
	'close' => '关闭',
	'subject' => '主题',
	'his_subjects' => '的主题',
	'reply' => '回复',
	'his_replies' => '的回复',
	'forum' => '版块',
	'lastpost' => '最后回复',
	'nodata' => '暂无数据',
	'not_enough_read_permission' => '阅读权限不足，无权读取',
	'thread_in_recyclebin' => '主题在回收站，无法读取',
	'post_auditing' => '正在审核中，无法显示',
);

$installlang['iirs_userPostList'] = array(
	'PHP_ENV_NOT_SUPPORTED' => '你的服务器环境不是PHP5.0及以上版本，安装此插件失败！',
);

?>