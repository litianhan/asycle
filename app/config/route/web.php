<?php
use \Asycle\Core\Http\Router;
/**
 * 路由配置
 */
Router::defaultControllerNamespace("App\Controllers");
Router::defaultMiddlewareNamespace('App\Middleware');

Router::api('/','Sample\Welcome::index');

/**
 * 样例
 */
Router::restful('restful','Sample\Sample');
Router::group(['Sample\Authenticate'],[],function (){

    Router::api('template','Sample\Sample::template');
});

