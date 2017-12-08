<?php
namespace Asycle\Core;
/**
 * Date: 2017/9/4
 * Time: 10:33
 */
class Benchmark{
    protected static $startRecord = [];
    protected static $endRecord = [];
    public function __construct()
    {
    }
    public static function start(string $key){
        $time = microtime(true);
        $memory  = function_exists('memory_get_usage') ? memory_get_usage() : 0;
        self::$startRecord[$key]= [$time,$memory];
    }
    public static function end(string $key){
        $time = microtime(true);
        $memory  = function_exists('memory_get_usage')? memory_get_usage() : 0;
        self::$endRecord[$key]= [$time,$memory];
    }
    public static function getRecords(){
        $res = [];
        foreach (self::$startRecord as $key=>$value){
            if(isset(self::$endRecord[$key])){
                $res[$key] = [self::$endRecord[$key][0] - self::$startRecord[$key][0],
                    self::$endRecord[$key][1] - self::$startRecord[$key][1]];
            }
        }
        return $res;
    }
    public static function getResultString(){
        $result = 'key | time(s)| memory(bytes)'.PHP_EOL;
        foreach (self::$startRecord as $key=>$record){
            $time = 'null';
            $memory = 'null';

            if(isset(self::$endRecord[$key])){
                $temp = self::$endRecord[$key];
                $time = sprintf('%2f',$temp[0] - $record[0]);
                $memory = sprintf('%2f',$temp[1] - $record[1]);
            }
            $result .=$key.'|'.$time.'|'.$memory.PHP_EOL;
        }
        return $result;
    }
}