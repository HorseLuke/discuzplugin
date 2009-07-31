<?php

!defined('IN_FW') && exit('ACCESS IS NOT ALLOWED!');

class FileCache{
    private static $_instance;             //保存实例
    private $cachedir ;
    public $time;
    
    
    /**
     * 创建一个新对象，在此期间设置该文件缓存类的目录和现在时间。
     *
     */
    private function __construct()  {
        $this->cachedir = FWBase::getConfig('CACHE_DIR');
        $this->time = time();
    }
    
    private function __clone()  {}
    
    
    /**
     * 文件缓存单例化命令。要进行文件缓存操作，必须静态调用该方法。
     *
     * @return object
     */
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    
    /**
     * 设置缓存目录
     *
     * @param string $dirpath
     */
    public function setCachedir($dirpath){
        if(is_dir($dirpath)){
            $this->cachedir = $cachedir;
        }else{
            FWBase::throw_exception('缓存目录不存在，设置失败！',9001);
        }
    }
    
    
    /**
     * 载入缓存，若发现缓存失效则删除之并返回空值；或者不存在该缓存时，也返回空值
     *
     * @param string $flag 缓存标记
     * @return mixed
     */
    public function load($flag){
        $flag=md5($flag);
        $filePath = $this->cachedir.'/FW_CACHE_'.$flag.'.php';
        if(!is_file($filePath)){
            return null;
        }
        $expiretime=0;
        @include $filePath;
        if($expiretime > 0 && $expiretime < $this->time ){
            @unlink($filePath);
            return null;
        }
        if(empty($cachedata)){
            return null;
        }
        return $cachedata;
    }
    
    
    /**
     * 保存缓存内容
     *
     * @param mixed $cachedata 要缓存的数据
     * @param string $flag 缓存标记，只允许字符类型，并将在随后进行md5 hash
     * @param integer $seconds 该缓存可存在缓存创立后的多长时间，单位为秒
     * @return boolen
     */
    public function save($cachedata,$flag,$seconds=0){
        $flag=md5($flag);
        $filePath = $this->cachedir.'/FW_CACHE_'.$flag.'.php';
        if(0 == $seconds){
            $expiretime=0;
        }else{
            $expiretime = $this->time + $seconds;
        }
        $cachedata="<?php\r\n\$expiretime={$expiretime};"."\$cachedata=".var_export($cachedata,true).";";
        $cachedata = @file_put_contents( $filePath , $cachedata );
        if(empty($cachedata)){
            FWBase::throw_exception('缓存文件无法写入，请检查缓存目录具有可写状态！',9001);
        }
        return true;
    }
    
    
    /**
     * 根据标记直接删除文件
     *
     * @param string $flag 缓存标记
     * @return boolen
     */
    public function deleteByFlag($flag){
        $flag=md5($flag);
        $filePath = $this->cachedir.'/FW_CACHE_'.$flag.'.php';
        @unlink($filePath);
        return true;
    }
    
    
    /**
     * 删除缓存目录下所有过时的缓存（未完工代码）
     * @todo 读取缓存目录并对缓存文件逐个检查，若过时则删除
     */
    public function deleteOld(){
        
    }
    
}