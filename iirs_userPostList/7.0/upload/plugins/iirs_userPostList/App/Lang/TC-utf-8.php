<?php
$scriptlang['iirs_userPostList'] = array(
	'multiple_select_forum' => '版塊選擇（按Ctrl多選）',
	'deselect_forum_all' => '取消版塊選擇（一旦選擇此將清空設置）',
	'adminIgnoreFidListEdit_intro' => '<li>由於Discuz!的許可權設置及檢查較為分散，出於效率的考慮，目前該插件無法自動禁止以下版塊的帖子顯示在列表中：
                    <ol>1、設置了訪問密碼的版塊</ol><ol>2、設置了許可權運算式的版塊</ol></li>
                    <li>如果論壇有上述所說的版塊，或者想額外忽略一些板塊，請在這裏手動設置。</li>
                    <li>設置成功後，插件將強制忽略這些板塊的帖子（不管用戶是否存在對本版塊的訪問許可權）。</li>',
	'set_ok' => '設置成功！',
	'use_personal_center_to_see_own_threads' => '要查看自己發佈的帖子，請點擊這裏進入“個人中心”的“我的帖子”查看。',
	'view_his_posts' => '查看回復的帖子',
	'view_his_threads' => '查看發佈的主題',
	'plugin_setting_cache_lost' => '本插件原有設置的緩存丟失，將影響插件的運行。請管理員到後臺重新設置該插件參數！',
);

$templatelang['iirs_userPostList'] = array(
	'close' => '關閉',
	'subject' => '主題',
	'his_subjects' => '的主題',
	'reply' => '回復',
	'his_replies' => '的回復',
	'forum' => '版塊',
	'lastpost' => '最後回復',
	'nodata' => '暫無數據',
	'not_enough_read_permission' => '閱讀許可權不足，無權讀取',
	'thread_in_recyclebin' => '主題在回收站，無法讀取',
	'post_auditing' => '正在審核中，無法顯示',
);

$installlang['iirs_userPostList'] = array(
	'PHP_ENV_NOT_SUPPORTED' => '你的伺服器環境不是PHP5.0及以上版本，安裝此插件失敗！',
);

?>