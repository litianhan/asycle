<?php

namespace Asycle\Core;

/**
 * Date: 2017/11/25
 * Time: 16:13
 */
class Console{
    protected static $defaultNamespace = '';
    protected static $commands = [];
    public static function defaultCommandNamespace($namespace){
        if(empty($namespace)){
            self::$defaultNamespace = '';
        }else{
            self::$defaultNamespace ='\\'. trim($namespace,'\\') . '\\';
        }
    }
    public static function command($commandName,$commandClass,$argumentKeys = []){
        if(empty($commandName)){
            throw  new \InvalidArgumentException('命令行名称不能为空。');
        }
        self::$commands[$commandName] = [$commandClass,$argumentKeys];
        return true;
    }
    public static function parseCommand(&$commandName,&$commandClass,&$arguments,&$options){

        $args = $_SERVER['argv'] ?? [];
        //去掉文件名
        array_shift($args);
        $commandName = array_shift($args);
        if(empty($commandName)){
            return false;
        }

        if( ! isset( self::$commands[$commandName])){

            return false;
        }
        if(strpos($commandClass,'\\') === 0){
            $commandClass = self::$commands[$commandName][0];

        }else{
            $commandClass =  self::$defaultNamespace . self::$commands[$commandName][0];
        }


        $argumentKeys =  self::$commands[$commandName][1];
        //解析命令行参数
        $options = [];
        $isOption = false;
        $currentOptionName = '';
        foreach ($args as $value){
            if(strpos($value,'-') === 0){
                $isOption = true;
                $currentOptionName = ltrim($value,'-');
                continue;
            }
            if($isOption){
                $options [$currentOptionName] = $value;
                $currentOptionName = '';
                $isOption = false;
                continue;
            }else{
                $value = array_shift($argumentKeys);
                if( ! empty($value)){
                    $arguments [$value]= $value;
                }

            }

        }
        return true;
    }
}