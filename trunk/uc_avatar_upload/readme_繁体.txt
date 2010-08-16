=================
說明書概述
=================
版本：$Id: readme.txt 135 2010-07-05 17:18:51Z horseluke@126.com $
程式名稱：剝離UCenter的flash上傳頭像程式為單獨程式
更新位址：http://code.google.com/p/discuzplugin-hl/

=================
！！！ 警告 ！！！
=================
該檔有代碼包含了康盛創想（北京）科技有限公司Discuz!/UCenter的代碼。根據相關協定的規定：
    “禁止在 Discuz! / UCenter 的整體或任何部分基礎上以發展任何派生版本、修改版本或第三方版本用於重新分發。”
故在此聲明如下：
    本程式僅為作者學習和研究軟體內含的設計思想和原理而作，不以盈利為目的，同時也無意侵犯第三方軟體作者/公司的權益。
    如若侵犯權益，請發郵件告知。在本人接獲通知的48小時之內將會把自己所發佈的代碼進行撤回操作。
    同時提醒第三方下載者和使用者使用這些代碼時考慮本程式的法律風險，第三方下載者和使用者的一切行為與本人無關。

Discuz!/UCenter頭文件注釋：
(C)2001-2009 Comsenz Inc.
This is NOT a freeware, use is subject to license terms


=================
概述
=================
歡迎查看Horse Luke（中文名稱：微碌）所編寫/修改的程式。

本程式受朋友邀請所寫，主要是剝離UCenter的Flash頭像上傳程式為獨立的程式，經過初步測試有效。
其中的swf檔來源自Discuz!NT程式。原因是Discuz!NT程式的同功能Flash檔，可指定任意的頭像上傳入口（即uc_api + 當前執行腳本檔案名）。

本程式允許在PHP5.0及以上版本運行，編碼為UTF-8。在Windows Server 2003 + IIS平臺下測試通過。
本程式需要開啟SPL庫（因為config類繼承了SPL的ArrayObject類；PHP5.0默認開啟了SPL）

由於版權原因，本程式不建議使用到生產環境，僅用於瞭解其flash頭像上傳的原理。

類似的剝離請查看別人的成果：http://www.phpchina.com/bbs/thread-187941-1-1.html

=================
使用方法
=================
1、本程式部分遵循MVC架構開發，upload.php是該程式的指定唯一入口。
你可以將upload.php改為任意名字，而不影響該程式的運行。

2、打開upload.php，對config陣列進行修改。
請使用Editplus等軟體打開，不要使用Windows自帶的記事本打開；
否則當保存時，windows記事本將自動往該檔加utf8 bom，從而可能導致本腳本無法運行（因為本腳本的編碼是utf8）！
config陣列含義：
（1）'tmpdir'：臨時存放第一次上傳檔的檔夾（相對於upload.php的位置而言），開頭和結尾請不要加反斜杆。
請務必將該文件夾和upload.php存放於同一分區，同時不要超過upload.php所運行的網址的頂端目錄，並且設置為可讀可寫，否則將出錯！
（2）'avatardir'：存儲頭像的檔夾（相對於upload.php的位置而言），開頭和結尾請不要加反斜杆。
請務必將該文件夾和upload.php存放於同一分區，同時不要超過upload.php所運行的網址的頂端目錄，並且設置為可讀可寫，否則將出錯！
（3）'authkey'：通訊密鑰，推薦進行修改。
此專案必須填寫，否則腳本將無法運行。
（4）'debug'：是否開啟debug記錄？
開啟後，錯誤日誌將存儲在upload.php所在目錄的Log檔夾下。
你可以通過修改upload.php檔的下面代碼來更改日誌位置：
Inter_Error::$conf['logDir'] = dirname(__FILE__). '/Log';
Inter_Error類的其他說明請參考對應附錄。
（5）'uploadsize'：上傳圖片檔的最大值，單位是KB。
請勿超過php.ini所允許的最大上傳值，否則flash將在上傳過程中出現邏輯混亂而無法運行。
（6）'uc_api'：運行該腳本的網址，末尾請不要加反斜杠（比如http://www.aaa.com/avatar/upload）。
如果為空，系統將自動生成一個。但自動生成的話可能會有錯誤，導致無法上傳頭像。如果遇到此情況，請修改這裏的值。


其他沒提到的設置，屬於系統設置。請不要隨便進行修改，否則將引起程式安全隱患！

3、在流覽器輸入：
http://127.0.0.1/uc_avatar_upload/upload.php?uid=9
其中uid必須存在，同時必須要指定為一個正整數。一切順利的話將看到flash頭像上傳介面。
如果這麼輸入：
http://127.0.0.1/uc_avatar_upload/upload.php?uid=9&returnhtml=0
則將返回一段json資料，裏面包含了創建flash所需要的變數。
（此時需要開啟伺服器對json的支持，否則php因為無法使用json_encode函數將返回fatal error錯誤）

4、測試頭像上傳。

=================
後續開發和思考建議
=================
1、本程式只有一個控制器：Controller_AvatarFlashUpload類（Lib/Controller/AvatarFlashUpload.php）。
該類有3個action：
（1）showuploadAction：獲取顯示上傳flash的代碼
（2）uploadavatarAction：頭像上傳第一步，上傳原文件到暫存檔案夾
（3）rectavatarAction：頭像上傳第二步，上傳到頭像存儲位置
建議開發者在執行這些action之前（甚至是實例化該例之前），對uid進行許可權判斷。

2、你可以利用Controller_AvatarFlashUpload類中的clear_avatar_file方法一次性清除指定uid的頭像。

3、如果你想即時瞭解上傳的結果，可以在Lib/Controller檔夾下面將如下代碼保存為“showuploadAction.html”
----->>>>>代碼開始<<<<<-----
<?php
!defined('IN_INTER') && exit('Fobbiden!');
$avatarsize = array( 1 => 'big', 2 => 'middle', 3 => 'small');
$avatartype = array( 'real', 'virtual' );

foreach ( $avatarsize as $size ){
    foreach ( $avatartype as $type ){
        $avatarurlpath = $this->config->uc_api. '/'. $this->config->avatardir. '/'. $this->get_avatar_filepath($uid, $size, $type);
        $result .= '<div>Avatar Type:'. $type. ' & Avatar Size:'. $size .'</div>'.
                   '<div id="'. $size. '_' .$type. '">'.
                   '<img src="'. $avatarurlpath. '" onerror="this.onerror=null;this.src=\''. $this->config->uc_api. '/images/noavatar_'. $size. '.gif\'" />'.
                   '</div><br />';
    }
}

$result .= '<script type="text/javascript">
function updateavatar() {
	window.location.reload();
}
</script>';
----->>>>>代碼結束<<<<<-----

然後在Lib/Controller/AvatarFlashUpload.php中的：
----->>>>>代碼開始<<<<<-----
            return $result;
        } else {
            return array(
            'width', '450',
            'height', '253',
            'scale', 'exactfit',
            'src', $uc_avatarflash,
            'id', 'mycamera',
            'name', 'mycamera',
            'quality','high',
----->>>>>代碼結束<<<<<-----
前面加上：
----->>>>>代碼開始<<<<<-----
            require('showuploadAction.html');
----->>>>>代碼結束<<<<<-----
即可即時查看結果了（上傳成功會自動刷新）。

4、其他事項，請閱讀文件最開始的“警告”。



=================
附錄：Inter_Error類說明書
=================
版本：0Intro_Error.txt 113 2010-03-04 16:14:23Z horseluke@126.com
Inter_Error類下載和更新位址：http://code.google.com/p/horseluke-code/

<?php
//本類檔可單獨使用。PHP版本要求：>=5.0.0。使用方法（代碼示例）：
date_default_timezone_set('PRC');    //設置時區，PRC內容請替換為合適的時區，詳細的請自查php手冊。PHP版本 >= 5.1.0的時候一定要做這步驟，否則很可能會導致記錄時間不准（除非在引用前已經設置時區）；版本 < 5.1.0則不需要。
require_once("Error.php");     //在適當的地方以require / require_once正確引用該檔
//然後接管PHP的錯誤處理機制
set_exception_handler(array('Inter_Error', 'exception_handler'));
set_error_handler(array('Inter_Error', 'error_handler'), E_ALL);
//然後可選擇地使用如下方式進行設置（假如保持預設值，可以不需配置。預設值請查看Error.php裏面關於靜態屬性$debugMode的說明）：
Inter_Error::$conf['debugMode'] = true;
Inter_Error::$conf['friendlyExceptionPage']='1234.htm';
Inter_Error::$conf['logType'] = 'simple';
Inter_Error::$conf['logDir'] = dirname(__FILE__).'/Log';

//可錯誤的代碼，這時候就會調出Inter_Error來處理了
$variable1 = '1111';

function a(){
    b();
}

function b(){
    echo 1/0;
}

function c(){
	throw new exception('Exception Occur!');
}

a();


//假如代碼沒有錯誤，但是你又想看看一些變數值。那麼就可以設置好Inter_Error::$conf['variables']，然後靜態調用show_variables方法，以顯示變數。
//注意：一旦有php代碼出錯，此方法會自動調用
/*
//以陣列加入要檢測的變數名即可。
Inter_Error::$conf['variables'] = array("_GET", "_POST", "_SESSION", "_COOKIE", "variable1", "variable2");
echo '<hr />';
Inter_Error::show_variables();
echo '<hr />';
*/

c();
