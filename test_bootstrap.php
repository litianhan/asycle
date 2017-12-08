<?php
/**
 * 加载资源用于测试
 *
 *
 * 执行 php phpunit.phar --bootstrap test_bootstrap.php tests
 *
 * User: tenhanlee
 * Date: 2017/9/16
 * Time: 下午3:40
 */
require('env/boot.php');
$app = new \Asycle\Core\Application();
return $app;