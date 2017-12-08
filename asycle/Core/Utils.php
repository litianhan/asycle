<?php

namespace Asycle\Core;

/**
 * 计量单位相关功能类
 * Date: 2017/11/22
 * Time: 19:31
 */
class Utils{
    public static function varPrint(...$var){
        echo '<pre>';
        foreach ($var as $value){
            var_dump($value);
        }
        echo '<pre/>';
    }
    /**
     * 产生安全的随机字符串
     * @param int $length
     * @return string
     */
    public static function randomSecureString(int $length): string
    {
        if ($length <= 0) {
            return '';
        }
        return substr(bin2hex(random_bytes($length / 2 + 1)), 0, $length);
    }

    /**
     * 从给定的字符集中生成随机字符串
     * @param int $length
     * @param string $chars
     * @return string
     */
    public static function randomStringFrom(int $length, string $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        if ($length <= 0) {
            return '';
        }
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    public static function uuid($dashes = true): string
    {
        if ($dashes)
        {
            $format = '%s-%s-%04x-%04x-%s';
        }
        else
        {
            $format = '%s%s%04x%04x%s';
        }
        return sprintf($format,
            // 8 hex characters
            bin2hex(openssl_random_pseudo_bytes(4)),
            // 4 hex characters
            bin2hex(openssl_random_pseudo_bytes(2)),
            // "4" for the UUID version + 3 hex characters
            mt_rand(0, 0x0fff) | 0x4000,
            // (8, 9, a, or b) for the UUID variant + 3 hex characters
            mt_rand(0, 0x3fff) | 0x8000,
            // 12 hex characters
            bin2hex(openssl_random_pseudo_bytes(6))
        );
    }


    /**
     * 当前UNIX时间毫秒数
     * @return float
     */
    public static function currentMillisecond(){
        return floor(microtime(true) * 1000);
    }

    /**
     * 字节转为易读字符串
     * @param int $bytes
     * @return string
     */
    public static function bytesToReadable(int $bytes){
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes >= 1024 && $i < 4; $i++) $bytes /= 1024;
        return round($bytes, 2).$units[$i];
    }

    /**
     * 秒数转为易读字符串
     * @param int $seconds
     * @return string
     */
    public static function secondsToReadable(int $seconds){
       if($seconds < 60){
           return $seconds.'秒';
       }elseif($seconds < 3600){
           return floor(($seconds/60)).'分'.
               floor(($seconds%60)).'秒';
       }elseif($seconds < 86400){
           return floor($seconds/3600).'时'.
               floor(($seconds%3600)/60).'分'.
               floor(($seconds%3600)%60).'秒';
       }elseif($seconds < 31536000){
           return floor($seconds/86400).'天'.
               floor(($seconds%86400)/3600).'时'.
               floor((($seconds%86400)%3600)/60).'分'.
               floor((($seconds%86400)%3600)/60).'秒';
       }else{
           return floor(($seconds/31536000)).'年';
       }
    }

    /**
     * 计算任务剩余秒数
     * @param int $consumedSeconds
     * @param float $finishedRatio
     * @return float|int
     */
    public static function secondsRemain(int $consumedSeconds,float $finishedRatio){
        if($consumedSeconds <= 0 or $finishedRatio > 1){
            return 0;
        }
        if($finishedRatio <= 0 ){
            return $consumedSeconds;
        }
        return round(($consumedSeconds/$finishedRatio - $consumedSeconds),0);
    }
}