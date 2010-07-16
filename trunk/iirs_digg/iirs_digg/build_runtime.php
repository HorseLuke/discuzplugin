<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>（Horse Luke于2010-7-17抽取代码，令其能单独运行）
// +----------------------------------------------------------------------
// $Id$


exit('去掉此句话后再运行！');

$filepath[] = dirname(__FILE__).'/Lib/mini/Controller.php';
$filepath[] = dirname(__FILE__).'/Lib/mini/Model.php';
$filepath[] = dirname(__FILE__).'/Lib/common.php';
$dest = dirname(__FILE__).'/~runtime.php';

build_runtime( $filepath, $dest );

echo 'BUILD RUNTIME OK!';



function build_runtime($filepath = array(), $dest = ''){
    $content = '';
    foreach ( $filepath as $file ){
        $content .= compile($file);
    }
    file_put_contents($dest ,strip_whitespace('<?php'.$content));
}

//[RUNTIME]
// 编译文件
function compile($filename,$runtime=false) {
    $content = file_get_contents($filename);
    if(true === $runtime)
        // 替换预编译指令
        $content = preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s','',$content);
    $content = substr(trim($content),5);
    if('?>' == substr($content,-2))
        $content = substr($content,0,-2);
    return $content;
}

// 去除代码中的空白和注释(ok)
function strip_whitespace($content) {
    $stripStr = '';
    //分析php源码
    $tokens =   token_get_all ($content);
    $last_space = false;
    for ($i = 0, $j = count ($tokens); $i < $j; $i++)
    {
        if (is_string ($tokens[$i]))
        {
            $last_space = false;
            $stripStr .= $tokens[$i];
        }
        else
        {
            switch ($tokens[$i][0])
            {
                //过滤各种PHP注释
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;
                //过滤空格
                case T_WHITESPACE:
                    if (!$last_space)
                    {
                        $stripStr .= ' ';
                        $last_space = true;
                    }
                    break;
                default:
                    $last_space = false;
                    $stripStr .= $tokens[$i][1];
            }
        }
    }
    return $stripStr;
}