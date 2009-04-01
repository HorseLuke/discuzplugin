该插件用于在首页显示图片附件并ajax翻页（带缓存）

方法：
1，解压缩后，打开picdisplay.php
然后设置$fid_ignore_list='';
这里是指不参与显示图片的板块fid，请以数字和半角符号分割。比如（不包括双引号）“12”，“12,55”等
比如说
你不想让 http://www/forumdisplay.php?fid=27
这个板块参与进去，就填27，变成$fid_ignore_list='27';
不想 http://www/forumdisplay.php?fid=27
 http://www/forumdisplay.php?fid=23
就填23,27，变成$fid_ignore_list='23,27';
不要填错

2，上传后还要修改一个文件：templates\default\discuz.htm
记住做好备份
在
<div id="nav"><a href="$indexname">$bbname</a> &raquo; {lang home}</div>
<!--{if $admode && empty($insenz['hardadstatus']) && !empty($advlist['text'])}--><div class="ad_text" id="ad_text"><table summary="Text Ad" cellpadding="0" cellspacing="1">$advlist[text]</table></div><!--{else}--><div id="ad_text"></div><!--{/if}-->
下面添加：
<script language="javascript">
var flag=false; 
function DrawImage(ImgD,w,h){ 
var image=new Image(); 
image.src=ImgD.src; 
if(image.width>0 && image.height>0){ 
flag=true; 
if(image.width/image.height>= w/h){ 
    if(image.width>w){ 
      ImgD.width=w; 
      ImgD.height=(image.height*w)/image.width; 
    }else{ 
      ImgD.width=image.width; 
      ImgD.height=image.height; 
    } 
}else{ 
    if(image.height>h){ 
      ImgD.height=h; 
      ImgD.width=(image.width*h)/image.height; 
    }else{ 
      ImgD.width=image.width; 
      ImgD.height=image.height; 
    } 
} 
} 
} 
</script>
<div class="ad_text" id="picdisplayindex">
    {eval include_once("./picdisplay.php"); }
</div>
就ok了
