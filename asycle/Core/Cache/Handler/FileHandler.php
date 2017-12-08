<?php

namespace Asycle\Core\Cache\Handler;
use Asycle\Core\Cache\CacheHandlerInterface;

/**
 * Date: 2017/9/11
 * Time: 19:37
 */
class FileHandler implements CacheHandlerInterface{

    protected $fileDirectory = APP_PATH_WRITABLE_CACHE;
    public function __construct()
    {
    }
    public function getFileDirectory(){
        return $this->fileDirectory;
    }
    /**
     * 保存键值,会覆盖旧值
     * @param string $key 键
     * @param string $value 值
     * @param int $ttl 存活时间,为0则永久保存
     * @return bool 是否保存成功
     */
    public function set(string $key, string $value, int $ttl = 0): bool
    {
        @$fp = fopen($this->getFileDirectory() . md5($key),'w');
        if( ! $fp){
            return false;
        }
        $data = [
            'expired'=>$ttl == 0 ? 0 : time() + $ttl,
            'data'=>$value
        ];
        $res  = fwrite($fp,json_encode($data));
        fclose($fp);
        return $res;
    }

    /**
     * 保存多个键值，过期时间都一样
     * @param array $keysAndValues
     * @param int $ttl
     * @return bool
     */
    public function setMany(array $keysAndValues, int $ttl = 0): bool
    {
        foreach ($keysAndValues as $key => $value){
            if( ! $this->set($key,$value,$ttl)){
                return false;
            }
        }
        return true;
    }

    /**
     * 获取键值
     * @param string $key
     * @param null $default
     * @return bool|mixed
     */
    public function get(string $key,$default = null )
    {
        $data = file_get_contents($this->getFileDirectory() . md5($key));
        if(empty($data)){
            return $default;
        }
        $data = json_decode($data,true);
        if( ! is_array($data) or ! isset($data['expired'],$data['data'])){
            return $default;
        }
        $expired = intval($data['expired']);
        if($expired > 0 and $expired < time()){
            $this->delete($key);
            return $default;
        }
        return $data['data'];
    }

    /**
     * 返回多个键值，不存在则对应键值为false
     * @param array $keys
     * @param null $default
     * @return array|mixed
     */
    public function getMany(array $keys,$default = null )
    {
        $value = [];
        foreach ($keys as $key){
            $value[$key] = $this->get($key,$default);
        }
        return $value;
    }

    /**
     * @param string $key
     * @param int $value
     * @param int $ttl
     * @return bool|int
     */
    public function increment(string $key, $value = 1,$ttl = 0)
    {
        @$fp = fopen($this->getFileDirectory() . md5($key), 'r+');
        if ( ! $fp) {
            return false;
        }

        flock($fp, LOCK_EX);
        $data = fgets($fp);
        if(empty($data)){
            $data = 0;
        }
        if( ! is_numeric($data)){
            return false;
        }
        $data = intval($data) + $value;
        $res = fwrite($fp, $data);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $res;
    }

    /**
     * @param string $key
     * @param int $value
     * @param int $ttl
     * @return mixed
     */
    public function decrement(string $key, $value = 1,$ttl = 0)
    {
        return $this->increment($key,0 - $value);
    }

    /**
     * 清空所有缓存
     * @return bool
     */
    public function flush(): bool
    {
        $dir = $this->getFileDirectory();
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file != "." && $file != "..") {
                $fullPath=$dir .DIRECTORY_SEPARATOR . $file;
                if(!is_dir($fullPath)) {
                    unlink($fullPath);
                }
            }
        }
        closedir($dh);
        return true;
    }

    /**
     * 获取键值,不存在则更新并获取
     * @param string $key
     * @param int $ttl
     * @param \Closure $closure
     * @return mixed
     */
    public function getOrRefresh(string $key, int $ttl = 0, \Closure $closure)
    {
        $value = $this->get($key);
        if($value){
            return $value;
        }
        $value = $closure();
        $this->set($key,$value,$ttl);
        return $value;
    }

    /**
     * 删除键值
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return unlink($this->getFileDirectory() . md5($key));
    }
}