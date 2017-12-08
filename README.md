使用Asycle
=============
介绍
------------
Asycle是一个为高并发而生的PHP7全栈框架，支持composer、restful API,简、快、安全。

安装
------------

下载文件后，拷贝到服务器，把服务器根目录设为文件目录中的asycle/public 即index.php所在的目录。
然后访问localhost/index.php即可。


创建Controller
------------
在/app/Controllers/Sample/目录下新建Welcome.php
/app/Controllers/Sample/Welcome.php内容为：
```php
<?php
namespace App\Controllers\Sample;
use App\Controllers\BaseController;
use Asycle\Core\Http\Response;

/**
 * Date: 2017/11/16
 * Time: 16:32
 */
class Welcome extends BaseController {
    
    public function index()
    {
        return Response::view('welcome');
    }
}
```


路由
--------------------
路由配置文件：/app/config/route/web.php,添加一下内容
```php

<?php
use \Asycle\Core\Http\Router;
/**
 * 路由配置
 */
Router::defaultControllerNamespace("App\Controllers");
Router::defaultMiddlewareNamespace('App\Middleware');

Router::api('/','Sample\Welcome::index');
```



可以通过重写规则去掉index.php，对于Apache服务器,开启重写模块，在web根目录下创建
.htaccess文件，写入以下内容:
```ini
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```
访问的地址localhost则为localhos/index.php
即可看到：欢迎使用Asycle :)

其中Response::view('welcome');会发送app/Views/welcome.php文件的内容到客户端。

Restful API
-------------------
在路由配置文件：/app/config/route/web.php中添加：
Router::restful('restful','Sample\Sample');
文件内容：
```php
<?php
use \Asycle\Core\Http\Router;
/**
 * 路由配置
 */
Router::defaultControllerNamespace("App\Controllers");
Router::defaultMiddlewareNamespace('App\Middleware');

Router::api('/','Sample\Welcome::index');
Router::restful('restful','Sample\Sample');
```
即可通过localhost/restful访问到App\Controllers\Sample\Sample中对应的方法函数体
URL:localhost/restful的get请求对应：App\Controllers\Sample\Sample::get
URL:localhost/restful的post请求对应：App\Controllers\Sample\Sample::post


中间件
-------------------

```php

<?php
use \Asycle\Core\Http\Router;
/**
 * 路由配置
 */
Router::defaultControllerNamespace("App\Controllers");
Router::defaultMiddlewareNamespace('App\Middleware');
Router::group(['Sample\Authenticate'],[],function (){
    //在这里的API会调用Sample\Authenticate中间件，通过后才会到达控制器方法Sample\Sample::index
    Router::api('sample','Sample\Sample::index');
});
```

Request
-------------------
Asycle为了速度，对于参数输入并不建议你使用封装的Request类，
建设使用$_GET,$_POST,$_FILE等PHP原生的参数，配合Filter作参数校验。
```php


```

Response
-------------------
### 页面模板渲染

Asycle使用PHP默认的渲染模板，以求达到更快的速度：
app/views/sample.php页面如下:
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>

<div id="container">

    <div id="body">
        <h2>if语句:</h2>
        <?php if ($option1===true): ?>
            <p>条件option1成立</p>
        <?php elseif ($option1===true): ?>
            <p>条件option2成立</p>
        <?php else: ?>
            <p>条件不成立</p>
        <?php endif; ?>

        <h2>switch语句:</h2>
        <?php switch ($switch):?>
<?php case 1:?>
                <p>switch1成立</p>
                <?php break;?>
                <p>switch2成立</p>
            <?php case 2:?>
                <p>switch3成立</p>
                <?php break;?>
            <?php default:?>
                <p>switch default成立</p>
            <?php endswitch;?>

        <h2>while语句:</h2>
        <?php while ($while):?>
            <span><?=$while?></span>
            <?php $while--;?>
        <?php endwhile;?>

        <h2>for语句:</h2>
        <?php for ($i=0;$i<5;$i++):?>
            <span><?=$i?>,</span>
        <?php endfor;?>

        <h2>foreach语句:</h2>
        <?php foreach ($strings as $value): ?>
            <span><?=$value?>,</span>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
```

```php
<?php
namespace App\Controllers\Sample;
use App\Controllers\BaseController;
use Asycle\Core\Http\Response;

/**
 * Date: 2017/11/16
 * Time: 16:33
 */
class Sample extends BaseController
{
    /**
     * 示例
     * @return bool
     */
    public function index()
    {
        $data = [
            'title'=> '使用样例',
            'option1'=>true,
            'switch'=>1,
            'while'=>5,
            'strings'=>[
                'string1','string2'
            ]
        ];
        return Response::view('sample',$data);
    }
    /**
     * restful get
     */
    public function get(){
        echo __METHOD__;
    }
}
```
输出为：
```html
<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <title>PHP原生模板语言样例</title>
</head>
<body>

<div id="container">

    <div id="body">
        <h2>if语句:</h2>
                    <p>条件option1成立</p>
        
        <h2>switch语句:</h2>
                        <p>switch1成立</p>
                
        <h2>while语句:</h2>
                    <span>5</span>
                                <span>4</span>
                                <span>3</span>
                                <span>2</span>
                                <span>1</span>
                    
        <h2>for语句:</h2>
                    <span>0,</span>
                    <span>1,</span>
                    
                    <span>2,</span><span>3,</span>
                    <span>4,</span>
        
        <h2>foreach语句:</h2>
                    <span>value1,</span>
                    <span>value2,</span>
            </div>
</div>


</body></html>
```

> 提示: 


参数校验
-------------------

```php
        $_GET = [
            'id'=>100,
            'username'=>'asycle'
        ];
        $filter = new Filter($_GET);
        $filter->requiredKey('id')->isInteger(1);
        $filter->requiredKey('username')->isUsername(6,32);
        if($filter->hasError()){
            die('参数错误');
        }

        $params = $filter->fetchAll();
        var_dump($params);

```
输出：
array(2) {
  ["id"]=>
  int(100)
  ["username"]=>
  string(6) "asycle"
}

数据库
-------------------
样例：
在env/development.php中的APP_CONNECTION_DATABASE数组里添加test数据库连接配置：
```php

define('APP_CLIENT_DATABASE',[

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
```

```php
use Asycle\Core\Connection;
//获取数据库连接
$db = Connection::db('test');
$db->table('users')->get();

```

Redis
-------------------
在env/development.php中的APP_CONNECTION_DATABASE数组里添加test数据库连接配置：
```php

$redis= Connection::redis('default');
$redis->set('key','asycle');
$value = $redis->get('key');
var_dump($value);
```

MongoDB
-------------------


邮件
-------------------

缓存
-------------------


会话
-------------------

配置
-------------------


多语言
-------------------

验证码
-------------------


错误日志
-------------------