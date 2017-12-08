<?php

namespace App\Middleware\Sample;
use Asycle\Core\Middleware;

/**
 * Date: 2017/11/25
 * Time: 10:22
 */
class Authenticate extends Middleware{

    /**
     * 中间件处理，返回false则终止程序，true则继续执行
     * @return bool
     */
    public function handle(): bool
    {
        return true;
    }
}