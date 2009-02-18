该数据库修复程序主要显示通过disucz 7的刷分漏洞而导致所有发表在fid为0的无法显示的主题帖。
漏洞详情及修复方法请关注这里:http://www.alan888.com/Discuz/thread-162824-1-1.html,或者这里：http://www.discuz.net/thread-1211501-1-1.html
本修复程序版本：0.0.1 BUILD 20090213 FIX 1。作者：Horse Luke（竹节虚）。


请注意：该程序为非官方程序，请先按照上面帖子的修复方法修复Discuz!，然后后台备份好数据库，然后再运行！作者不承担一切责任！

使用方法：上传creditbugfix.php，然后以管理员身份运行，按照步骤进行修复。
如果觉得修复速度太慢，请打开creditbugfix.php，并找到
$limit_once_process=1000;
这里是一次处理多少条的参数，请把1000修改为大于1的你认为适合的整数。


  Copyright 2008 Horse Luke（竹节虚）.

  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.