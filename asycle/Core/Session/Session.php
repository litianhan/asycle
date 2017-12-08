<?php

namespace Asycle\Core\Session;
use Asycle\Core\Http\Request;
use Asycle\Core\Http\Response;
use Asycle\Core\Session\Handler\FileHandler;

/**
 * 会话扩展类
 * User: Tenhan
 * Date: 2017/5/19
 * Time: 12:54
 */
class Session {
    /**
     * session id
     * @var string
     */
    protected static $sid = null;
    /**
     * 用户id
     * @var string
     */
    protected static $uid = null;
    /**
     * 会话数据
     * @var array
     */
    protected static $data = [];
    /**
     * 会话过期时间
     * @var int
     */
    protected static $expired = null;
    protected static $handler = null;
    /**
     * cookie前缀
     * @var string
     */
    protected static $cookieKey = APP_SESSION_COOKIE_NAME;
    protected static $path = APP_COOKIE_PATH;
    protected static $domain = APP_COOKIE_DOMAIN;
    protected static $secure = APP_COOKIE_SECURE;
    public static function setHandler(SessionHandlerInterface $handler){
        self::$handler = $handler;
    }
    protected static function getCookieKey(){
        return self::$cookieKey;
    }
    /**
     * 开启会话
     * @return bool
     */
    public static function start(): bool
    {
        $sid = Request::cookie(self::getCookieKey(),'');
        if (self::validateSessionId($sid)) {
            // 如果有sid,则读取用户id,再根据用户id读取用户登录信息
            self::$sid = $sid;
            if( ! (self::$handler instanceof SessionHandlerInterface)){
                self::$handler = new FileHandler();
            }
            self::$handler->start(self::$sid,self::$uid,self::$data,self::$expired);

            if(empty(self::$uid) or intval(self::$expired) < time()){
                //登录信息已过期
                self::$sid = self::createSessionId(64);
                return true;
            }
            return true;
        } else { //没有的登录信息，则重新生成一个新的sid
            self::$sid = self::createSessionId(64);
            return true;
        }
    }
    /**
     * 保存会话
     *  @param string $uid 用户id
     * @param int $ttl 生存时间，单位:秒
     * @return bool
     */
    public static function store($uid,int $ttl = APP_SESSION_EXPIRATION): bool
    {
        if (self::validateSessionId(self::$sid)) {
            self::$uid = $uid;
            self::$expired = time() + $ttl;
            //使用httponly属性防止cookies被xss攻击读取
            Response::cookie(
                self::getCookieKey(),
                self::$sid,
                self::$expired,
                self::$path,
                self::$domain,
                self::$secure,
                true);
            $handler  = &self::$handler;
            if($handler instanceof SessionHandlerInterface){
                return $handler->store(self::$sid,self::$uid,self::$data,$ttl);
            }
            return true;
        }
        return false;
    }
    /**
     * 当前会话用户id
     * @return mixed
     */
    public static function uid()
    {
        return self::$uid;
    }

    /**
     * 当前会话sid
     * @return mixed
     */
    public static function sid()
    {
        return self::$sid;
    }
    /**
     * 当前会话过期时间
     * @return mixed
     */
    public static function expired(){
        return self::$expired;
    }

    /**
     * 销毁会话
     * @return bool
     */
    public static function destroy(): bool
    {
        if (empty(self::$sid)){
            return false;
        }
        $handler  = &self::$handler;
        if( $handler instanceof SessionHandlerInterface){
            return $handler->destroy(self::$sid,self::$uid);
        }
        return false;
    }

    /**
     * 获取键值
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return self::$data[ $key ] ?? $default;
    }

    /**
     *  获取包含所有键值的数组
     * @return mixed
     */
    public static function getAll()
    {
        return self::$data;
    }

    /**
     * 设置键值
     * @param string $key
     * @param $value
     */
    public static function set(string $key, $value)
    {
        self::$data[ $key ] = $value;
    }

    /**
     *  删除键
     * @param string $key
     */
    public static function remove(string $key)
    {
        unset(self::$data[ $key ]);
    }

    /**
     * 验证sid格式是否符合
     * @param $string
     * @param int $minLength
     * @param int $maxLength
     * @return bool
     */
    public static function validateSessionId($string, $minLength = 64, $maxLength = 256): bool
    {
        if(empty($string)){
            return false;
        }
        //检查$sid是否合法
        if (preg_match('/^[a-zA-Z0-9]{' . $minLength . ',' . $maxLength . '}$/', $string)) {
            return true;
        }
        return false;
    }

    /**
     * 创建session ID
     * @param int $length
     * @return string
     */
    public static function createSessionId($length = 64): string
    {
        //嵌入26字符的session_create_id()
        $sessionId = session_create_id();
        $segLength = $length - strlen($sessionId);
        $sid = bin2hex(random_bytes($segLength / 2 + 1));
        return substr($sessionId.$sid, 0, $length);
    }
}