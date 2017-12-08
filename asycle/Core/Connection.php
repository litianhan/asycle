<?php

namespace Asycle\Core;

use Asycle\Core\Database\DB;
use Asycle\Core\Database\MongoHelper;
use Asycle\Core\mail\Mail;
use Asycle\Core\Queue\AsyncQueue;
use Asycle\Core\Queue\DelayQueue;

/**
 * Date: 2017/9/23
 * Time: 下午7:11
 */
class Connection{
    protected  static $db = [];
    protected  static $mail = [];
    protected  static $redis = [];
    protected  static $mongodb = [];
    protected  static $memcached = [];
    protected  static $delayQueue = [];
    protected  static $asyncQueue = [];
    /**
     * 释放资源
     */
    public  static function release(){
        foreach (self::$db as $key=>$value){
            if($value instanceof DB){
                $value->close();
                self::$db[$key] = null;
            }
        }
        foreach (self::$redis as $key => $value){
            if($value instanceof \Redis){
                $value->close();
                self::$redis[$key] = null;
            }
        }
        foreach (self::$mongodb as $key=>$value){
            if($value instanceof MongoHelper){
                $value->close();
                self::$mongodb[$key] = null;
            }
        }
        foreach (self::$memcached as $key=>$value){
            if($value instanceof \Memcached){
                $value->quit();
                self::$memcached[$key] = null;
            }
        }
    }
    /**
     * 异步延时队列
     * @param $name
     * @return DelayQueue
     */
    public static function delayQueue($name): DelayQueue
    {
        if (!(self::$delayQueue[$name] instanceof DelayQueue)) {
            self::$delayQueue[$name] = new DelayQueue();
        }
        return self::$delayQueue[$name];
    }
    /**
     * 异步队列
     * @param $name
     * @return AsyncQueue
     */
    public static function asyncQueue($name): AsyncQueue
    {
        if (!(self::$asyncQueue[$name] instanceof AsyncQueue)) {
            self::$asyncQueue[$name] = new AsyncQueue();
        }
        return self::$asyncQueue[$name];
    }

    public static function getQueryRecords(){
        $records = [];
        foreach (self::$db as $key=>$value){
            if($value instanceof DB){
                $records[$key] = $value->getQueryRecords();
            }
        }
        return $records;
    }
    /**
     * 数据库连接
     * @param string $connection
     * @return DB
     */
    public static function db(string $connection = ''): DB
    {
        if (empty($connection)) {
            $connection = 'default';
        }
        if (!(self::$db[ $connection ] instanceof DB)) {
            self::$db[ $connection ] = new DB($connection);
        }
        return self::$db[ $connection ];
    }
    /**
     * 获取redis连接
     * @param string $name
     * @return \Redis
     */
    public static function redis(string $name = ''): \Redis
    {
        if (empty($name)) {
            $name = 'default';
        }
        if (!(self::$redis[ $name ] instanceof \Redis)) {
            $config = APP_CONNECTION_REDIS;
            $redisConfig = $config[$name];
            foreach (['host', 'port', 'timeout'] as $value) {
                if (!array_key_exists($value, $redisConfig)) {
                    throw new \InvalidArgumentException('redis配置信息缺漏:' . $value);
                }
            }
            $redis = new \Redis();
            $redis->connect($redisConfig['host'], $redisConfig['port'], $redisConfig['timeout']);
            if (isset($redisConfig['password']) and ! empty($redisConfig['password'])) {
                $redis->auth($redisConfig['password']);
            }
            $database = $redisConfig['database'] ?? 0;
            if( ! empty($database)){
                $redis->select($database);
            }
            self::$redis[ $name ] = $redis;
        }
        return self::$redis[ $name ];
    }

    /**
     * MongoHelper
     * @param string $name
     * @return MongoHelper
     */
    public static function mongodb(string $name = ''): MongoHelper
    {
        if (empty($name)) {
            $name = 'default';
        }
        if (!(self::$mongodb[ $name ] instanceof \MongoClient)) {

            $config =APP_CONNECTION_MONGODB ;
            $mongodbConfig = $config[$name];
            $auth = '';
            if( ! empty($mongodbConfig['auth'])){
                $auth=$mongodbConfig['auth'].'@';
            }
            $server = 'mongodb://'.$auth.$mongodbConfig['host'].':' . $mongodbConfig['port'];
            self::$mongodb[ $name ] = new MongoHelper($server);
        }
        return self::$mongodb[ $name ];
    }

    /**
     * Memcache
     * @param string $name
     * @return \Memcached
     */
    public static function memcached(string $name): \memcached
    {
        if (empty($name)) {
            $name = 'default';
        }
        if (!(self::$memcached[ $name ] instanceof \Memcached)) {

            $config = APP_CONNECTION_MEMCACHED ;

            self::$memcached[ $name ] = new \Memcached($config[$name]);
        }
        return self::$memcached[ $name ];

    }

    /**
     * 邮件客户端
     * @param string $name
     * @return Mail
     */
    public static function mail(string $name = ''):Mail{
        if(empty($name)){
            $name = 'default';
        }
        if ( ! (self::$mail[$name] instanceof Mail)) {

            $config = APP_CONNECTION_MAIL;
            if( ! isset($config[$name])){
                throw new \InvalidArgumentException('邮件配置不存在:'.$name);
            }
            self::$mail[$name] = new Mail($config[$name]);
        }
        return self::$mail[$name];
    }

}