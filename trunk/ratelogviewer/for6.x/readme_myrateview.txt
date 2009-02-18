个人评分记录独立查看页面For Discuz6.1.0F


当前版本：Ver 0.1 Beta Build 20080813 For Discuz6.1.0F
插件开发者：Horse Luke（中文名称：竹节虚）
更新发布页面：http://www.freediscuz.net/bbs/thread-4073-1-1.html

注意：
本插件基于个人业余兴趣而编写制作，并且已经经过作者的测试，但仍存在风险（包括但不限于：对论坛的压力增大、存在编码漏洞等等），如对您造成损失，本人不承担任何责任。请在下载和安装前慎重考虑。
安装使用前，请您仔细阅读本插件的说明，以免在安装或使用过程中遇到不必要的麻烦。

开发缘由：
该功能为Discuz 4.1.0 Lite（http://www.freediscuz.net/bbs/viewthread.php?tid=637&page=1#pid6153）时候的独有功能，现在因为论坛管理需要，特地编写使用。

安装方法：
直接上传upload文件夹内的所有文件，无需修改任何文件，访问http://你的BBS站点/myrateview.php即可使用。
若想集合到个人控制面板中，请打开模板templates\default\personal_navbar.htm。在

<div class="credits_info">

上面加入：

     <div>
      <h2>评分查看面板</h2>
      <ul>
                    <li<!--{if $action == 'myrate'}--> class="side_on first"<!--{/if}-->><h3><a href="myrateview.php?action=myrate">我的评分记录</a></h3></li>
                    <li<!--{if $action == 'berated'}--> class="side_on first"<!--{/if}-->><h3><a href="myrateview.php?action=berated">我的被评记录</a></h3></li>
      </ul>
     </div>