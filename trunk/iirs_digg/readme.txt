$Id$

鲜花鸡蛋For Discuz! 7.1 UTF-8 / Discuz! 7.2 UTF-8（非完全绿色插件版）


=======================================================================
特别鸣谢
=======================================================================
    - 江湖大虾仁 的 帖子强化包{ @link http://www.discuz.net/thread-1374692-1-1.html }。已征得同意使用其图片。
    - Discuz!{ @link http://www.discuz.net/ }
    - PHP框架ThinkPHP 2.0{ @link http://www.thinkphp.cn/ }
    - ECMALL { @link http://ecmall.shopex.cn/ }




=======================================================================
一、开发原因
=======================================================================
1.1 关于Discuz!新插件内核机制的思考
（A）嵌入点的设计，是否只是为了方便了站长的安装/卸载操作，而让站长忽视了自己的实际动手能力和故障排除能力？
（B）嵌入点的存在，使得插件作者在编写代码的时候，是否容易出现为实现功能而实现功能，代码中是否容易出现冗余的循环查询和操作，
     结果导致论坛在高负载下效率变差，甚至引起MySQL宕机？
（C）嵌入点的模板，在循环的include template(‘xxx:yyy’)后会否引起多次的I/O操作？
（D）若论坛是作为一个项目来管理，是采取传统的修改文件方法，还是采取新插件内核机制？（特别是PHP核心部分）
1.2 关于Discuz!插件框架
（A）有没有必要设计属于自己的Discuz!插件框架？
（B）Discuz!的自成框架体系下，如何以最小侵入方式设计和运行？
（C）Discuz!插件框架在高负载下运行效率如何？
1.3 编程感觉
    个人问题，要找回感觉 -_-||



=======================================================================
二、关于本插件
=======================================================================
（A）本插件实现了对某一帖子（包括主题贴和回复帖）进行送鲜花或者扔鸡蛋的操作。
（B）本插件作为某论坛的项目开发，为非完全绿色版，具体如下：
    - 数据库部分对两个Discuz!主表分别新增了两列。
    - 需要修改Discuz!论坛文件。
    - 嵌入点的设计仅是为了展示鲜花/鸡蛋数量（即仅作View层使用）；
      并且没有使用include template(‘xxx:yyy’)模板技术（不推荐插件作者在制作通用型插件时不使用官方推荐的模板技术）。
    - 没有使用语言包技术（时间关系没时间弄；不推荐插件作者在制作通用型插件时不使用官方推荐的语言包）
    - 没有使用新内核安装/卸载技术（由于需要修改论坛核心文件，故没有使用该技术；不推荐插件作者在制作通用型插件时不使用官方推荐的新内核安装/卸载技术）
（C）本插件在Discuz! 7.1 UTF-8和Discuz! 7.2 UTF-8下运行通过。
（D）本插件仅推荐如下用户群使用（由高到低排序）：
    - 插件作者之间的交流。
    - 熟悉Discuz!程序的PHP开发者。
    - 将论坛当作长期正式项目来做、并且已经修改了程序内核的站长和公司。
    - 对论坛负载非常敏感并已经存在在使用类似绿色插件后有负荷问题的站长，该站长需同时具备手动修改论坛文件和数据库的能力。
（E）本插件极度不推荐如下用户群使用（排序不分先后）：
    - 处在刚开始运营和发展阶段的论坛（比如：每日PV少于4000的论坛）。
    - 对负载不敏感的论坛、或者可通过投入硬件资源解决问题的论坛。
    - 不熟悉PHP代码、或者不熟悉数据库操作的站长。
    - 不喜欢修改论坛核心、或者认为绿色核心插件才是最好的站长。



=======================================================================
三、安装方法
=======================================================================
3.1 请确认你的论坛编码是UTF-8。如果不是，请自行将所有文件转码。繁体用户请自行将语言转换。

3.2 运行如下SQL（注意备份数据库）：
--------------------------------------------
ALTER TABLE cdb_memberfields ADD COLUMN `diggup_count` int(10) unsigned NOT NULL DEFAULT '0' AFTER `sellercredit`;
ALTER TABLE cdb_memberfields ADD COLUMN `diggdown_count` int(10) unsigned NOT NULL DEFAULT '0' AFTER `diggup_count`;

ALTER TABLE cdb_posts ADD COLUMN `diggup_count` int(10) unsigned NOT NULL DEFAULT '0' AFTER `status`;
ALTER TABLE cdb_posts ADD COLUMN `diggdown_count` int(10) unsigned NOT NULL DEFAULT '0' AFTER `diggup_count`;

DROP TABLE IF EXISTS cdb_iirs_digg_log;
CREATE TABLE cdb_iirs_digg_log (
  pid int(10) unsigned NOT NULL default '0',
  authorid mediumint(8) unsigned NOT NULL DEFAULT '0',
  loguid mediumint(8) unsigned NOT NULL DEFAULT '0',
    `logtype` enum('diggup','diggdown') NOT NULL DEFAULT 'diggup',
      dateline int(10) unsigned NOT NULL default 0,
  KEY pid (pid,loguid)
) TYPE=MyISAM;
--------------------------------------------

3.3 修改文件viewthread.php（注意备份文件）

找到：
mf.sightml AS signature, mf.customstatus, mf.spacename $fieldsadd
改为：
mf.sightml AS signature, mf.customstatus, mf.spacename, mf.diggup_count as mf_diggup_count, mf.diggdown_count as mf_diggdown_count $fieldsadd


3.4 除非本地环境调试，否则请将iirs_digg目录下面的build_runtime.php文件删除！

3.5 将iirs_digg目录上传到论坛的plugins目录下.

3.6 在后台安装该插件。

3.7 后台设置该插件。




=======================================================================
四、卸载方法
=======================================================================
将安装方法反向操作即可。
其中卸载SQL语句为：
--------------------------------------------
DROP TABLE IF EXISTS cdb_iirs_digg_log;

ALTER TABLE cdb_memberfields DROP COLUMN `diggup_count`;
ALTER TABLE cdb_memberfields DROP COLUMN `diggdown_count`;

ALTER TABLE cdb_posts DROP COLUMN `diggup_count`;
ALTER TABLE cdb_posts DROP COLUMN `diggdown_count`;
--------------------------------------------

=======================================================================
五、开发者修改本插件技巧
=======================================================================
frontLoader.inc.php中，当有如下语句的话，将加载预先编译的框架核心文件~runtime.php，以减少I/O操作：
define('USE_RUNTIME', 1);
如果没有，则逐一加载框架核心文件。

~runtime.php可通过运行build_runtime.php得到。
build_runtime函数可以应用在其他方面。有需要的话请自行琢磨。
该函数抽取自ThinkPHP 2.0




=======================================================================
COPYRIGHT NOTICE
----------------
Copyright 2010 Horse Luke（微碌）.
Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at
http://www.apache.org/licenses/LICENSE-2.0
Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
=======================================================================