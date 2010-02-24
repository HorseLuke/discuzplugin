Discuz! Debug工具——PHP错误显示和记录
不定时更新地址：
http://www.freediscuz.net/bbs/thread-5742-1-1.html
http://code.google.com/p/discuzplugin-hl/wiki/Inter_Error_For_Discuz
==============================

本debug工具为改进本人的一个错误debug类而来。
由于DZ本身运行时有大量的NOTICE错误（这是dz的编写习惯导致），因此本次发布的debug工具为适应dz版（屏蔽NOTICE错误版本）。
如果需要原版，请使用这个链接的文件：http://horseluke-code.googlecode.com/svn/trunk/InterFramework/Lib/Inter/Error.php


请注意：
1、不是所有错误都是系统漏洞，有些错误本身并不会引起系统错误，只是在检查时候的“依例通知”；然而有些错误则需要引起开发者们的警觉。具体发现，请自行摸索～
2、本debug文件仅适合PHP5.0及以上版本。
3、本文件仅允许开发者在本地调试使用，非必要情况下，严禁使用于生产环境（比如正在运营的网站）！
4、本人不承担任何因使用及误用而导致任何不良后果的责任！
5、请多多查看日志记录文件，并及时做好删除操作。


使用方法：
1、把php文件Error.php放入include文件夹内

2、打开include/common.inc.php，在

error_reporting(0);

的前面插入：

//Inter_Error调试插入(开始)
require("Error.php");
//然后接管PHP的错误处理机制
set_exception_handler(array('Inter_Error', 'exception_handler'));
set_error_handler(array('Inter_Error', 'error_handler'), E_ALL);
//然后可选择地使用如下方式进行设置（假如保持默认值，可以不需配置。默认值请查看Error.php里面关于静态属性$debugMode的说明）：
Inter_Error::$conf['debugMode'] = true;   //是否在浏览器显示
Inter_Error::$conf['logType'] = 'simple';       //日志记录方式：'simple'简单、'detail'全记录、false不记录
Inter_Error::$conf['logDir'] = 'R:/TEMP';  //强调！强调！！请在这里填写日志记录的完整目录路径（最后不要带/）！绝对不要使用常量DISCUZ_ROOT！（因为此时不存在这东西）
//Inter_Error调试插入(结束)


==============================

其它Discuz! Debug工具：
SQL语句调试（非官方debug.func.php文件）：
http://www.freediscuz.net/bbs/thread-5198-1-2.html

==============================
   Copyright Horse Luke (2010)

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.