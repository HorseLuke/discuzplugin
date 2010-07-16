$Id$


目的：
 - 绿色版在某些程度上其实是加重了服务器负荷。
 - 将其当作项目而非通用插件来做，因此PHP核心部分不作为绿色修改版插件。
 - 绿色插件实现部分：仅作View层使用
 - 在Discuz 7.1上运行成功

安装方法：
1、运行如下SQL：
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

2、修改文件viewthread.php（注意备份）

找到：
mf.sightml AS signature, mf.customstatus, mf.spacename $fieldsadd
改为：
mf.sightml AS signature, mf.customstatus, mf.spacename, mf.diggup_count as mf_diggup_count, mf.diggdown_count as mf_diggdown_count $fieldsadd


3、将iirs_digg目录上传到论坛的plugins目录下，然后在后台安装该插件

4、后台设置该插件。

========================================
卸载方法则进行相反操作。