<?php
/**
 * DX FLASH批量图片上传（SWFUPLOAD）失败排查程序（开发者专用版）
 * @version $Id$
 */
ob_start();
header("Content-type:text/html;charset=utf-8");

error_reporting(E_ALL);

$attach_dir = realpath(dirname(__FILE__). '/data/attachment/forum');
if(empty($attach_dir)){
	exit('attach_dir not exist');
}
$attach_dir_test = $attach_dir. '/2033_test111';
debug_output('指引：请在测试完成后，删除如下目录：', $attach_dir_test);
debug_output('指引：请在测试完成后，删除如下文件：', __FILE__);


if($_SERVER['REQUEST_METHOD'] != 'POST'){
	tpl_upload();
	exit();
}

debug_output('$_FILES ARRAY', $_FILES);
if(empty($_FILES['userfile']['name']) || $_FILES['userfile']['error'] != 0){
	exit('$_FILES ARRAY ERROR');
}

$tmp_target_dir = $attach_dir_test. '/'. mt_rand() . '/'. mt_rand();
debug_output('tmp_target_dir', $tmp_target_dir);


if(!is_dir($tmp_target_dir)){
	$mk_dir_res = mkdir($tmp_target_dir, 0777, true);
	debug_output('mk_dir_res', $mk_dir_res);
	if(!$mk_dir_res){
		exit('mk_dir_res is not true');
	}
}

$img_size = getimagesize($_FILES['userfile']['tmp_name']);
debug_output('getimagesize', $img_size);
if(!in_array($img_size[2], array(1,2,3))){
	exit('ONLY ALLOW GIF/JPG/PNG');
}

$tmp_target_file_dest = $tmp_target_dir. '/'. mt_rand(). '.jpg';
debug_output('tmp_target_file_dest', $tmp_target_file_dest);


$dz_is_upload_file = dz_is_upload_file($_FILES['userfile']['tmp_name']);
debug_output('dz_is_upload_file', $dz_is_upload_file);
if(!$dz_is_upload_file){
	exit('dz_is_upload_file is not true');
}

$succeed = false;
if(copy($_FILES['userfile']['tmp_name'], $tmp_target_file_dest)) {
	debug_output('method copy success');
	$succeed = true;
}elseif(function_exists('move_uploaded_file') && move_uploaded_file($_FILES['userfile']['tmp_name'], $tmp_target_file_dest)) {
	debug_output('method move_uploaded_file success');
	$succeed = true;
}elseif (is_readable($_FILES['userfile']['tmp_name']) && ($fp_s = fopen($_FILES['userfile']['tmp_name'], 'rb')) && ($fp_t = fopen($tmp_target_file_dest, 'wb'))) {
	while (!feof($fp_s)) {
		$s = fread($fp_s, 1024 * 512);
		fwrite($fp_t, $s);
	}
	fclose($fp_s); fclose($fp_t);
	debug_output('method fopen/fread/fwrite success');
	$succeed = true;
}

debug_output('upload result', $succeed);



function tpl_upload(){
	echo <<<EOF
	DX FLASH批量图片上传（SWFUPLOAD）失败排查程序（开发者专用版）<br />
	你正在使用测试文件。测试完成后，请按指引删除相关文件和文件夹！<br />
<!-- The data encoding type, enctype, MUST be specified as below -->
<form enctype="multipart/form-data" method="POST">
    Send this file, only img: <input name="userfile" type="file" />
    <input type="submit" value="Send File" />
</form>	
EOF;
	exit();
}


function debug_output(){
	echo "\r\n\r\n<BR /><BR />===========<BR />\r\n<pre>";
	$p = func_get_args();
	var_export($p);
	echo "</pre><BR />\r\n===========\r\n\r\n<BR /><BR />";
}


function dz_is_upload_file($source) {
	return $source && ($source != 'none') && (is_uploaded_file($source) || is_uploaded_file(str_replace('\\\\', '\\', $source)));
}