<?php

namespace Asycle\Core\Session\Handler;
use Asycle\Core\Session\SessionHandlerInterface;

/**
 * User: Tenhan
 * Date: 2017/9/11
 * Time: 20:55
 */
class RedisHandler implements SessionHandlerInterface {
    /**
     * 会话前缀
     * @var string
     */
    protected $prefix = '';
    /**
     * 用户sid=》uid
     * @var mixed|string
     */
    protected $sidKeyPrefix = '';

    /**
     * 用户会话信息Key
     * @var string
     */
    protected $uidKeyPrefix = '';
    /**
     *
     * @var null|\Redis
     */
    protected $redis = null;

    public function __construct(\Redis $redis,$prefix = '')
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->sidKeyPrefix = 'session_sid_'.$this->prefix;
        $this->uidKeyPrefix = 'session_uid_'.$this->prefix;
    }
    public function getSidKey($sid){
        return $this->sidKeyPrefix .$sid;
    }
    public function getUidKey($uid){
        return $this->uidKeyPrefix .$uid;
    }
    /**
     * 开启会话
     * @param $sid
     * @param $uid
     * @param $data
     * @param $expired
     * @return bool
     */
    public function start(&$sid, &$uid, &$data, &$expired): bool
    {
        if(empty($sid)){
            return false;
        }
        $uid = $this->redis->get($this->getSidKey($sid));
        if(empty($uid)){
            return false;
        }
        $data = $this->redis->hGetAll($this->getUidKey($uid));
        return true;
    }

    /**
     * 保存会话
     * @param $sid
     * @param $uid
     * @param $data
     * @param $ttl
     * @return bool
     */
    public function store($sid, $uid, $data, $ttl): bool
    {
        $this->redis->set($this->getSidKey($sid),$uid,$ttl);
        return $this->redis->hMset($this->getUidKey($uid), $data);
    }

    /**
     * 销毁会话
     * @param $sid
     * @param $uid
     * @return bool
     */
    public function destroy($sid,$uid): bool
    {
        if(! empty($sid)){
            if(empty($uid)){
                $uid = $this->redis->get($this->getSidKey($sid));
            }
            $this->redis->delete($this->getSidKey($sid));
        }
        if( ! empty($uid)){
            $this->redis->delete($this->getUidKey($uid));
        }
        return true;
    }
}