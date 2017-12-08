<?php
/**
 * 开发环境配置
 */

define('APP_CONNECTION_DATABASE',[

    'default' => [
        'dbms'         => 'mysql',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'test',
        'table_prefix' => '',
        'pconnect'     => false,
        'charset'      => 'utf8',
        'debug'        =>APP_DEBUG,
    ],
    'test' => [
        'dbms'         => 'mysql',
        'hostname'     => '127.0.0.1',
        'username'     => 'test_user',
        'password'     => '12345678',
        'database'     => 'test',
        'table_prefix' => '',
        'pconnect'     => false,
        'charset'      => 'utf8',
        'debug'        =>APP_DEBUG,
    ],
]);

define('APP_CONNECTION_REDIS',[
    'default' => [
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'password' => '',
        'database' => 0,
        'timeout'  => 3,
    ],
]);

define('APP_CONNECTION_MEMCACHED',[
    'default' => [
        'servers' => ['127.0.0.1:11211'],
        'debug' => APP_DEBUG,
        'compress_threshold' => 10240,  //超过多少字节的数据时进行压缩
        'persistant' => false  //是否使用持久连接
    ],
]);

define('APP_CONNECTION_MONGODB',[
    'default' => [
        /**
         *不要带http前缀
         */
        'host'=> '127.0.0.1',
        /**
         * 端口号
         */
        'port'=> 27017,
        /**
         * //留空则不进行密码验证
         */
        'auth'=> '',
    ],
]);

define('APP_CONNECTION_MAIL',[
    'default'=>[
        'debug'     =>  APP_DEBUG,   // 是否启用smtp的debug进行调试
        'host'      =>  'm',   // SMTP服务器地址
        'port'      =>  465,  //ssh连接远程服务器端口号
        'auth'      =>  true, //启用smtp认证
        'username'  =>  'm',  // 用户名
        'from'      =>  '',  // 邮箱地址
        'from_name' =>  '',  // 发件人姓名
        'password'  =>  '',  //smtp登录的密码
        'ssl'       => true,
        'charset'   =>  'UTF-8',   // 字符集
        'html'      =>  true, // 是否HTML格式邮件
        'reply_to'  =>  '',   //用户回复邮件时的接收邮箱
        'cc'        =>  [],    //抄送者
        'bcc'       =>  [],    //密送着
    ],
]);