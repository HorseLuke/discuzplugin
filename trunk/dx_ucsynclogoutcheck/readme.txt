=================
说明书概述
=================
版本：$Id$
程序名称：Discuz!X Ucenter Sync Logout Check
更新地址：http://code.google.com/p/discuzplugin-hl/
说明文档：http://code.google.com/p/discuzplugin-hl/wiki/dz_dx_ucsynclogoutcheck

=================
概述
=================
欢迎查看Horse Luke（中文名称：微碌）所编写/修改的程序。
本程序用于检测uc同步登录是否正常（调用的是uc_user_synlogout接口）
目前在DX 1.5测试通过，理论上可用于DX 1.0/1.5/1.5.1/2.0

=================
使用方法
=================
上传复制ucsynclogoutcheck.php到论坛根目录，然后运行。
如果在URL后面增加参数showcache=1，则读取存储在DZ/DX的同步登录缓存文件:[DZ/DX目录]/uc_client/data/cache/apps.php

=================
其它技巧
=================
DZ/DX无法通过UCenter进行同步登录的可能原因和对策：
http://code.google.com/p/discuzplugin-hl/wiki/uc_sync_problem_check_tips