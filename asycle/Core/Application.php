<?php

namespace Asycle\Core;
use Asycle\Core\Debug\ToolBar;
use Asycle\Core\Http\Request;
use Asycle\Core\Http\Response;
use Asycle\Core\Logger\Handler\FileHandler;
use Asycle\Core\Http\Router;
use Asycle\Core\Logger\Log;

/**
 * Date: 2017/8/9
 * Time: 22:43
 */

class Application
{
    const VERSION_CODE = '1.0.0';
    const VERSION_INT = 1;
    public function __construct()
    {
        /**
         * 注册错误与异常处理函数
         */
        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);
        register_shutdown_function([$this, 'shutdownHandler']);

       Log::addHandler(new FileHandler());

        //设置时区
        if ( ! empty(APP_TIMEZONE)) {
            ini_set('date.timezone', APP_TIMEZONE);
        }
        /**
         * 设置环境信息
         */
        switch (APP_ENVIRONMENT) {
            case 'development':
                error_reporting(-1);
                ini_set('display_errors', 1);
                break;
            case 'testing':
            case 'production':
                ini_set('display_errors', 0);
                error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
                break;
            case 'maintenance':
                ini_set('display_errors', 0);
                error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
                Response::error(503);
                Response::view('errors/html/error_maintenance');
                exit(0);
                break;
            default:
                header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
                echo 'Environment config error.';
                exit(1);
        }
    }


    /**
     * 执行控制台命令
     * @return bool
     */
    public function runCommand(){
        $commandName = '';
        $commandClass= '';
        $arguments = [];
        $options = [];
        require_once (APP_FILE_ROUTE_COMMANDS);
        if( ! Console::parseCommand($commandName,$commandClass,$arguments,$options)){
            return $this->actionNotFound( $commandName,true);
        }
        if( ! class_exists($commandClass,true)){
            throw new \InvalidArgumentException('Command class not found:'.$commandClass);
        }
        $object = new $commandClass($arguments,$options);
        if($object instanceof Command){
            return $object->handle();
        }else{
            return $this->actionNotFound( $commandName,true);
        }
    }
    /**
     * 执行http请求
     * @return bool
     */
    public function runHttpRequest()
    {

        /**
         * 加载路由文件
         */
        require_once (APP_FILE_ROUTE_WEB);
        $uri = Router::parseRequestUri();
        $action = Router::tryRouteUri($uri);
        if($action === false){
            return $this->actionNotFound($uri,0);
        }
        $res = $this->run(...$action);
        //输出ToolBar信息
        Response::appendBody($this->getDebugBarBody());
        return $res;
    }

    protected function run($uri,$controller,$controllerMethod,$params = [],$preMiddleware = [],$postMiddleware = []){
        if (empty($controller)) {
            return $this->actionNotFound($uri,0);
        }
        $reflectionMethod = null;
        if( ! class_exists($controller,true)){
            throw new \InvalidArgumentException('controller not found:'.$controller);
        }
        $isRestful = false;
        //如果方法为空则为restful
        if(empty($controllerMethod)){
            $isRestful = true;
            $controllerMethod = Request::method();
        }

        try{
            $reflectionMethod = new \ReflectionMethod($controller, $controllerMethod);
        }catch (\ReflectionException $e){
            if($isRestful){
                return $this->actionNotFound($uri,0);
            }else{
                throw new \InvalidArgumentException('controller method not found:'.$controller.'::'.$controllerMethod);
            }

        }

        //检查参数是否与controller入口方法参数的数量一致
        $minParams = $reflectionMethod->getNumberOfRequiredParameters();
        $maxParams = $reflectionMethod->getNumberOfParameters();
        $numberOfParams = count($params);
        if ($numberOfParams >= $minParams and
            $numberOfParams <= $maxParams and
            $reflectionMethod->isPublic()) {

            //执行中间件
            foreach ($preMiddleware as $middleware){
                if( ! class_exists($middleware,true)){
                    throw new \InvalidArgumentException('Middleware not found:'.$middleware);
                }
                $object = new $middleware();
                if($object instanceof Middleware and $object->handle() === true){
                    continue;
                }else{
                    return false;
                }
            }


            $controllerObject = new $controller();
            $controllerObject->{$controllerMethod}(...$params);

            //执行中间件
            foreach ($postMiddleware as $middleware){
                if( ! class_exists($middleware,true)){
                    throw new \InvalidArgumentException('Middleware not found:'.$middleware);
                }
                $object = new $middleware();
                if($object instanceof Middleware and $object->handle() === true){
                    continue;
                }else{
                    return false;
                }
            }
        } else {
            return $this->actionNotFound($uri,0);
        }


        return true;
    }
    /**
     * 显示404 Not Found页面
     * @param $uri
     * @param bool $isCommand
     * @return bool
     */
    public function actionNotFound($uri,$isCommand = false)
    {
        if($isCommand){
            $heading = 'Command Not Found : ';
            Log::error($heading.$uri);
            Response::error(404);
            Response::view('errors/cli/error_404',
                [
                    'heading' => $heading,
                    'message' => $uri
                ]);
        }else{
            $heading = '404 Page Not Found : ';
            Log::error($heading . $uri);
            Response::error(404);
            Response::view(
                'errors/html/error_404',
                [
                    'heading' => $heading,
                    'message' => $uri
                ]);
        }
        return false;
    }
    public function getDebugBarBody(){
        if( ! APP_DEBUG ){
            return '';
        }
        if(! APP_SHOW_TOOL_BAR){
            return '';
        }
        return ToolBar::bodyContent();
    }

    /**
     * 处理异常
     * @param \Throwable $exception
     */
    public function exceptionHandler(\Throwable $exception)
    {
        Log::error('Uncaught '.$exception);
        Response::clear();
        Response::error();
        $this->showException($exception);
    }

    /**
     * 处理错误
     * @param $severity
     * @param $message
     * @param $filePath
     * @param $line
     */
    public function errorHandler($severity, $message, $filePath, $line)
    {
        $isError = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);
        if ($isError) {
            Log::error($message);
            $this->showError($severity, $message, $filePath, $line);
        }
    }

    /**
     * 脚本执行结束后做一些处理
     */
    public function shutdownHandler()
    {
        $last_error = error_get_last();
        //捕获Fatal错误
        if (isset($last_error) &&
            ($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING))
        ) {
            $this->errorHandler(
                $last_error['type'],
                $last_error['message'],
                $last_error['file'],
                $last_error['line']
            );
        }
        Connection::release();
        Response::flush();
    }

    public function showError($severity, $message, $filepath, $line)
    {
        Response::clear();
        if (!str_ireplace(['off', 'none', 'no', 'false', 'null'], '', ini_get('display_errors'))) {
            Response::error();
            return;
        }
        if ( ! Request::isCLI()) {
            Response::error();
            $view = 'errors/html/error_php';
        } else {
            $view = 'errors/cli/error_php';
        }
        Response::view(
            $view,
            [
                'severity' => $severity,
                'message' => $message,
                'filepath' => $filepath,
                'line' => $line
            ]);
    }
    public function showException(\Throwable $exception)
    {
        Response::clear();
        if ( ! str_ireplace(['off', 'none', 'no', 'false', 'null'], '', ini_get('display_errors'))) {
            Response::error();
            return;
        }
        $message = $exception->getMessage();
        if (empty($message)) {
            $message = '(null)';
        }
        if ( Request::isCLI() ) {
            $view = 'errors/cli/error_exception';
        } else {
            Response::error();
            $view = 'errors/html/error_exception';
        }
        Response::view(
            $view,
            [
                'message' => $message,
                'exception' => $exception
            ]);
    }
}