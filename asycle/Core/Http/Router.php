<?php

namespace Asycle\Core\http;
/**
 * Date: 2017/8/9
 * Time: 22:44
 */
class Router
{
    protected static $requestUri = null;
    protected static $route = [];
    protected static $currentGroupIndex = 0;

    protected static $groupPreControllerMiddleware = [];
    protected static $groupPostControllerMiddleware = [];
    protected static $globalPreControllerMiddleware = [];
    protected static $globalPostControllerMiddleware = [];
    protected static $controllerNamespace = '';
    protected static $middlewareNamespace = '';

    public static function group(array $preControllerMiddleware,array $postControllerMiddleware,\Closure $closure){
        self::$currentGroupIndex ++ ;
        self::$groupPreControllerMiddleware [self::$currentGroupIndex] = $preControllerMiddleware;
        self::$groupPostControllerMiddleware[self::$currentGroupIndex] = $postControllerMiddleware;
        call_user_func($closure);
    }
    public static function globalControllerMiddleware(array $preControllerMiddleware,array $postControllerMiddleware){
        self::$globalPreControllerMiddleware = $preControllerMiddleware;
        self::$globalPostControllerMiddleware = $postControllerMiddleware;
    }
    protected static function requestMethod(){
        $method = 'GET';
        if(isset($_SERVER)){
            $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        }
        if( ! in_array($method,['GET','POST','PUT','DELETE','PATCH'])){
            $method = 'GET';
        }
        return $method;
    }
    protected static  function addRouteUri($routeUri,$action,$preControllerMiddleware = [],$postControllerMiddleware = []){
        $routeUri = '/'.trim($routeUri,'/');
        $routeSegments = explode('/',$routeUri);
        $routeSegments []='/';

        $currentRoute = &self::$route;
        foreach ($routeSegments as $segment){
            if(empty($segment)){
                continue;
            }
            if( ! isset($currentRoute[$segment])){
                $currentRoute[$segment] = [];
            }
            $currentRoute = &$currentRoute[$segment];
        }
        $controllers = explode('::',$action);
        if( ! empty($currentRoute)){
            throw new \InvalidArgumentException('路由已经存在:'.$routeUri);
        }
        $currentRoute = [
            self::$currentGroupIndex,
            $controllers[0] ?? '',
            $controllers[1] ?? '',
            $preControllerMiddleware,
            $postControllerMiddleware
        ];
    }
    public static function parseRequestUri():string{
        if ( ! isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
            return '';
        }
        $uri = parse_url('http://dummy'.$_SERVER['REQUEST_URI']);
        $query = isset($uri['query']) ? $uri['query'] : '';
        $uri = isset($uri['path']) ? $uri['path'] : '';

        if (isset($_SERVER['SCRIPT_NAME'][0]))
        {
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
            {
                $uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            }
            elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
            {
                $uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
        }
        if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0)
        {
            $query = explode('?', $query, 2);
            $uri = $query[0];
            $_SERVER['QUERY_STRING'] = $query[1] ?? '';
        }else
        {
            $_SERVER['QUERY_STRING'] = $query;
        }

        if ($uri === '/' OR $uri === '')
        {
            return '/';
        }
        return $uri;
    }
    public static function defaultControllerNamespace($namespace){
        if(empty($namespace)){
            self::$controllerNamespace = '';
        }else{
            self::$controllerNamespace = '\\'.trim($namespace,'\\') . '\\';
        }

    }
    public static function defaultMiddlewareNamespace($namespace){
        if(empty($namespace)){
            self::$middlewareNamespace = '';
        }else{
            self::$middlewareNamespace = '\\'.trim($namespace,'\\') . '\\';
        }
    }
    public static function restful($uri,$controller,$preControllerMiddleware = [],$postControllerMiddleware = []){
        if(strpos($controller,'::')){
            throw new \InvalidArgumentException('非法controller');
        }
        self::addRouteUri($uri,$controller,$preControllerMiddleware,$postControllerMiddleware);
    }
    public static function api($uri,$action,$preControllerMiddleware = [],$postControllerMiddleware = []){
        self::addRouteUri($uri,$action,$preControllerMiddleware,$postControllerMiddleware);
    }
    public static function tryRouteUri($uri){
        $segments = explode('/', $uri);
        $currentRoute = self::$route;
        $params = [];
        foreach ($segments as $segment)
        {
            if (empty($segment)) {
                continue;
            }
            if(empty($params) and isset($currentRoute[$segment])){
                $currentRoute =&$currentRoute[$segment];
            }else{
                $params [] = $segment;
            }
        }
        if(isset($currentRoute['/'])){
            $temp = &$currentRoute['/'];
            if(strpos($temp[1],'\\') === 0){
                $controllerName = $temp[1];
            }else{
                $controllerName = self::$controllerNamespace . $temp[1];
            }

            $preControllerMiddleware = [];
            $postControllerMiddleware = [];
            foreach (self::$globalPreControllerMiddleware as $value){
                if(strpos($value,'\\') === 0){
                    $preControllerMiddleware[]= $value;
                }else{
                    $preControllerMiddleware[]= self::$middlewareNamespace . $value;
                }

            }
            foreach (self::$globalPostControllerMiddleware as $value){
                if(strpos($value,'\\') === 0){
                    $postControllerMiddleware[]= $value;
                }else{
                    $postControllerMiddleware[]= self::$middlewareNamespace . $value;
                }
            }
            foreach (self::$groupPreControllerMiddleware[self::$currentGroupIndex] as $value){
                if(strpos($value,'\\') === 0){
                    $preControllerMiddleware[]= $value;
                }else{
                    $preControllerMiddleware[]= self::$middlewareNamespace . $value;
                }
            }
            foreach ($temp[3] as $value){
                if(strpos($value,'\\') === 0){
                    $preControllerMiddleware[]= $value;
                }else{
                    $preControllerMiddleware[]= self::$middlewareNamespace . $value;
                }
            }
            foreach (self::$groupPostControllerMiddleware[self::$currentGroupIndex] as $value){
                if(strpos($value,'\\') === 0){
                    $postControllerMiddleware[]= $value;
                }else{
                    $postControllerMiddleware[]= self::$middlewareNamespace . $value;
                }
            }
            foreach ($temp[4] as $value){
                if(strpos($value,'\\') === 0){
                    $postControllerMiddleware[]= $value;
                }else{
                    $postControllerMiddleware[]= self::$middlewareNamespace . $value;
                }
            }
            return [$uri,$controllerName,$temp[2],$params,$preControllerMiddleware,$postControllerMiddleware];
        }
        return false;
    }
}