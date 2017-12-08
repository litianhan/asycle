<?php

namespace Asycle\Core\Session\Handler;
use Asycle\Core\Session\SessionHandlerInterface;

/**
 * User: Tenhan
 * Date: 2017/9/11
 * Time: 20:54
 */
class RawHandler implements SessionHandlerInterface {

    public function __construct()
    {
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
        $res = session_start();
        //使用自带的session
        $sid = session_id();
        $data = &$_SESSION;
        return $res;
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
        ini_set('session.gc_maxlifetime', strval($ttl));
        ini_set("session.cookie_lifetime", strval($ttl));
        return session_write_close();
    }

    /**
     * 销毁会话
     * @param $sid
     * @param $uid
     * @return bool
     */
    public function destroy($sid, $uid): bool
    {
        return session_destroy();
    }
}