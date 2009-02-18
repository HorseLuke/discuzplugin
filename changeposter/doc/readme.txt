发布地址：
for 6.x：http://www.discuz.net/thread-1078272-1-1.html
for 7：http://www.discuz.net/thread-1199954-1-1.html


插件安装
	1、请使用对应论坛版本的文件夹，把该文件夹下的upload文件夹内所有文件上传到论坛根目录。
	2、这时候插件就可以使用了。但为了方便使用，你也可继续作如下修改。修改前请注意备份文件。
	打开templates\default\viewthread.htm，找到
						<strong onclick="scroll(0,0)" title="{lang top}">TOP</strong>	
	在上面加入
						<!--{if $adminid==1}-->
							<a href="changeposter.php?pid=$post[pid]" id="changeposter_$post[pid]">更改发帖人</a>
						<!--{/if}-->	
	即可在管理员登录状态下的每张帖子右下角看到该链接，从而快速更改该帖的发贴人。