<?php

namespace Asycle\Core;

/**
 * Date: 2017/11/25
 * Time: 10:19
 */
abstract class Middleware{
    /**
     * 中间件处理，返回false则终止程序，true则继续执行
     * @return bool
     */
    abstract public function handle():bool ;
}