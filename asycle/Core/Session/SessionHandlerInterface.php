<?php

namespace Asycle\Core\Session;
/**
 * Date: 2017/9/11
 * Time: 20:49
 */
interface SessionHandlerInterface{
    /**
     * 开启会话
     * @param $sid
     * @param $uid
     * @param $data
     * @param $expired
     * @return bool
     */
    public function start(&$sid,&$uid,&$data,&$expired):bool ;

    /**
     * 保存会话
     * @param $sid
     * @param $uid
     * @param $data
     * @param $ttl
     * @return bool
     */
    public function store($sid,$uid,$data,$ttl):bool;

    /**
     * 销毁会话
     * @param $sid
     * @param $uid
     * @return bool
     */
    public function destroy($sid,$uid):bool;

}