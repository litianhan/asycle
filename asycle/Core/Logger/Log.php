<?php

namespace Asycle\Core\Logger;
/**
 * Date: 2017/5/2
 * Time: 20:06
 */
/**
 * 日志功能类
 * Class Log
 * @package Asycle\Core
 */
class Log
{
    protected static $handler = [];
    protected static $logFileDirectory = APP_PATH_WRITABLE_LOG;
    protected static $threshold =
    APP_IS_PRODUCTION  ? APP_LOG_PRODUCTION_THRESHOLD:
        (APP_IS_DEVELOPMENT ? APP_LOG_DEVELOPMENT_THRESHOLD : (
            APP_IS_TESTING ? APP_LOG_TESTING_THRESHOLD : APP_LOG_MAINTENANCE_THRESHOLD
        )) ;
    protected static $level = [
        'emergency' => 1,
        'alert'		 => 2,
        'critical'	 => 3,
        'error'		 => 4,
        'warning'		 => 5,
        'debug'	 => 6,
        'notice'	 => 7,
        'info'		 => 8,
    ];
    protected static function enableLog($level){
        if (self::$level[$level] > 0 and self::$level[$level] <= self::$threshold) {
            return true;
        }
        return false;
    }
    public static function addHandler(LoggerInterface $handler){
        self::$handler []= $handler;
    }
    public static function clearHandler(){
        self::$handler [] = [];
    }
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function emergency($message, array $context = [])
    {
        return self::log('emergency',$message,$context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function alert($message, array $context = [])
    {
        return self::log('alert',$message,$context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function critical($message, array $context = [])
    {
        return self::log('critical',$message,$context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function error($message, array $context = [])
    {
        return self::log('error',$message,$context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function warning($message, array $context = [])
    {
        return self::log('warning',$message,$context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function notice($message, array $context = [])
    {
        return self::log('notice',$message,$context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function info($message, array $context = [])
    {
        return self::log('info',$message,$context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function debug($message, array $context = [])
    {
        return self::log('debug',$message,$context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function log($level, $message, array $context = [])
    {
        if(empty(self::$handler)){
            return false;
        }
        if( ! self::enableLog($level)){
            return false;
        }
        if(empty(self::$handler)){
            return false;
        }

        $date = date(APP_LOG_DATE_FORMAT, time());
        $remoteAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown remote address';
        /**
         * 换行符要用双引号""
         */
        $prefix = '[' . $level . '] ['.$date . '] ['.$remoteAddress.'] ';
        $result = false;
        foreach (self::$handler as $handler){
            if($handler instanceof LoggerInterface){
                $contextString = empty($context)?'':print_r($context,true)."\n";
                $result = $handler->log($level,$prefix.$message ."\n".$contextString  );
            }
        }
        return $result;
    }
}