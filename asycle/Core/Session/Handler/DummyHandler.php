<?php

namespace Asycle\Core\Session\Handler;

/**
 * User: tenhan
 * Date: 2017/12/1
 * Time: 17:02
 */
class DummyHandler implements \Asycle\Core\Session\SessionHandlerInterface{
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
        return true;
    }

    /**
     * 销毁会话
     * @param $sid
     * @param $uid
     * @return bool
     */
    public function destroy($sid, $uid): bool
    {
        return true;
    }
}