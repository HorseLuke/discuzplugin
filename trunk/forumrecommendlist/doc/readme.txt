作品名称：版主推荐主题帖的单独显示页面
广告词：让版主们的推荐有个家！
适用论坛版本：Discuz! 6.0
形式：非插件
版本：V1.1 FIX1 Build 20080603
作者：Horse Luke（中文名称：大菜鸟）
首发于：http://tea.monkeye.cn

功能说明：在单独页面显示所有版主推荐的主题帖，支持5种排序。没有走缓存路线。

使用方法：
1、上传所有upload文件夹下的文件到论坛根目录
2、然后下载论坛根目录下的robots.txt，并在最后加上一句：
Disallow: /forumrecommendlist.php
3、最后通过直接访问forumrecommendlist.php即可。

设置方法：
打开forumrecommendlist.php
其中有两行可以设置，请依据实际情况进行修改。
$hack_cut_str = 80;    //设置标题显示的字数
$navigation="版主们推荐了什么好帖子？";      //设置该版块名称

卸载方法：
删除对应文件即可（forumrecommendlist.php，templates\default\forumrecommendlist.htm）

编写说明：
1、业余时间分离并改写一未知出处的论坛首页Home中关于版主推荐部分代码+论坛文件digest.php（精华帖搜寻显示）的部分代码。
2、代码已经过作者的测试，但仍存在风险；如对您造成损失，本人不承担任何责任。请在下载该附件并上传到服务器前慎重考虑。
3、已知问题：因不走缓存路线，执行查询时占用服务器资源较大。

版本更新历史：
V1.1 FIX1 Build 20080603：
完成分页功能，消除仅能获取前25张推荐帖子的弊病。

V1.01FIX2 Build 20080602：
增加让用户自由排序的下拉菜单，默认按发表时间排序。
精简代码并增强逻辑性。通过两次SQL语句，第一次判断是否有版主推荐帖子，若有才读取内容。

V1.0 FIX1 Build 20080602：
增加判断，当无版主推荐帖子时候返回“无版主推荐”信息。

Debug Information：
Study Using "LEFT JOIN ... ON ..."
It will encount a wrong message "Column 'XXX' in field list is ambiguous" While don't use nickname to remark the same Field name which is in different Tables.