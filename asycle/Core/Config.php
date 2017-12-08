<?php

namespace Asycle\Core;

/**
 * Date: 2017/11/23
 * Time: 10:52
 */
class Config{
    protected static $values = [];
    protected static $valuesDirectory = APP_PATH_CONFIG_VALUES;
    protected static $filesDirectory = APP_PATH_CONFIG_FILES;
    protected static $lang = [];
    protected static $langDirectory = APP_PATH_CONFIG_LANGUAGE;
    protected static $fileExt = APP_AUTOLOAD_FILE_EXT;
    protected static $currentLang = APP_LANGUAGE;

    /**
     * 获取配置文件中的键值
     * @param $file
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public static function value($file,$key,$default = null){

        $file = trim($file,DIRECTORY_SEPARATOR);
        if(empty($file)){
            throw new \InvalidArgumentException();
        }
        if( ! isset(self::$values[$file])){
            $fileName =
                self::$valuesDirectory .
                DIRECTORY_SEPARATOR.
                $file . self::$fileExt;
            if( ! file_exists($fileName)){
                return $default;
            }
            self::$values[$file] = require ($fileName);
        }
        $currentConfig = &self::$values[$file];
        if(empty($key) ){
            return $currentConfig[$key] ?? $default;
        }
        $segments = explode('.',$key);

        foreach ($segments as $segment){
            if(isset($currentConfig[$segment])){
                $currentConfig = & $currentConfig[$segment];
            }else{
                return $default;
            }
        }
        return $currentConfig;
    }

    /**
     * 语言类
     * @param string $file
     * @param string $name
     * @param null $default
     * @return mixed
     * @throws \Exception
     */
    public static function lang(string $file,string $name,$default = null)
    {

        if (empty($file) or empty($name)) {
            throw new \Exception('参数不能为空。');
        }
        if (! isset(self::$lang[ $file ])) {
            $filename = self::$langDirectory .DIRECTORY_SEPARATOR
                . trim(self::$currentLang, '/') . DIRECTORY_SEPARATOR . trim($file, '/') . self::$fileExt;
            if( ! file_exists($filename)){
                return $default;
            }
            self::$lang[ $file ] = require ($filename);
        }
        $segments = explode('.',$name);
        $currentConfig = &self::$lang[ $file ];
        foreach ($segments as $segment){
            if(isset($currentConfig[$segment])){
                $currentConfig = & $currentConfig[$segment];
            }else{
                return $default;
            }
        }
        return $currentConfig;
    }

    /**
     * 读取文件内容
     * @param $file
     * @param null $default
     * @return bool|null|string
     */
    public static function file($file,$default = null){
        $file = trim($file,DIRECTORY_SEPARATOR);
        $fileName =
            self::$filesDirectory .
            DIRECTORY_SEPARATOR.
            $file;
        if( ! file_exists($fileName)){
            return $default;
        }
        return file_get_contents($fileName);
    }

}