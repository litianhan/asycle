<?php

namespace Asycle\Core\Http;

/**
 * Date: 2017/4/23
 * Time: 15:43
 */
class Response
{

    protected static $viewPath = APP_PATH_VIEWS;
    protected static $fileExt = APP_AUTOLOAD_FILE_EXT;
    protected static $statusHeader = '';
    protected static $headers = [];
    protected static $cookies = [];
    protected static $body = '';
    protected static $encryption = null;
    public static function clear(){
        self::$statusHeader = '';
        self::$headers = [];
        self::$cookies = [];
        self::$body = '';
        return true;
    }
    public static function flush(){
        if( ! empty(self::$statusHeader)){
            header(self::$statusHeader,true);
        }
        foreach (self::$headers as $header){
            header($header[0], $header[1]);
        }
        foreach (self::$cookies as $cookie){
            setcookie(...$cookie);
        }
        if( ! empty(self::$body)){
            echo self::$body;
        }
        return self::clear();
    }

    /**
     * 重定向
     * @param $url
     * @param null $code
     * @param string $method
     * @return bool
     */
    public static function redirect($url, $code = null,$method = 'auto')
    {
        if ( ! preg_match('#^(\w+:)?//#i', $url)) {
            //throw new \InvalidArgumentException('URI地址错误:' . $url);
            $url = Request::baseURL($url);
        }
        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
            $method = 'refresh';
        } elseif ($method !== 'refresh' && (empty($code) or !is_numeric($code))) {
            if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
                $code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
                    ? 303    // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
                    : 307;
            } else {
                $code = 302;
            }
        }
        switch ($method) {
            case 'refresh':
                header('Refresh:0;url=' . $url);
                break;
            default:
                header('Location: ' . $url, TRUE, $code);
                break;
        }
        return true;
    }

    /**
     * 添加头部
     * @param string $header
     * @param bool $replace
     * @return bool
     */
    public static function header(string $header, $replace = false)
    {
        self::$headers []=[$header,$replace];
        return true;
    }
    public static function appendBody($content,$xssClean = false){
        if($xssClean){
            self::$body .= self::$xssClean($content);
        }else{
            self::$body .= $content;
        }
        return true;
    }

    /**
     * 设置cookie,如果删除，设为过期值为空即可
     * @param $key
     * @param $value
     * @param $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @return bool
     */
    public static function cookie($key,$value,$expire,$path = APP_COOKIE_PATH,$domain = APP_COOKIE_DOMAIN,$secure = APP_COOKIE_SECURE,$httpOnly = APP_COOKIE_HTTP_ONLY){
        if(empty($key) or ! preg_match('/^[a-zA-Z0-9_-]*$/',$key)){
            throw new \InvalidArgumentException('invalid key:'.$key);
        }
        self::$cookies []= [$key,$value,$expire,$path,$domain,$secure,$httpOnly];
        return true;
    }
    protected function xssClean(string $val): string
    {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
        $val = htmlspecialchars($val);
        return $val;
    }
    public static function commonJson($code = 0,$msg = '',$data = []){
        return self::json([
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data
        ]);
    }

    /**
     * 以json格式输出参数
     * @param $params
     * @return bool
     */
    public static function json($params)
    {
        self::header('Content-Type:application/json;charset=UTF-8', true);
        if(is_string($params)){
            return self::appendBody($params);
        }else{
            return self::appendBody(json_encode($params));
        }
    }

    /**
     * 以xml格式输出参数
     * @param $params
     * @param string $rootName
     * @param string $version
     * @param string $encoding
     * @return bool
     */
    public static function xml($params,$rootName = 'root',$version = '1.0',$encoding = 'UTF-8'){
        self::header('Content-type: text/xml', true);
        $string = "<?xml version=\"{$version}\" encoding=\"{$encoding}\"?>\n<{$rootName}>\n";
        $string .= self::wrapWithXmlTag($params);
        $string.="</{$rootName}>";
        return self::appendBody($string);
    }
    protected static function wrapWithXmlTag($value){
        if(is_string($value)){
            return htmlspecialchars($value);
        }
        if(is_object($value)){
            $value = get_object_vars($value);
        }
        if(is_array($value)){
            $result = '';
            foreach ($value as $key=>$val){
                $result .= "<".$key.">".self::wrapWithXmlTag($val)."</".$key.">\n";
            }
            return $result;
        }else{
            return '';
        }

    }

    /**
     * @param $img
     * @param string $contentType
     * @return bool
     */
    public static function image($img, $contentType = 'image/png')
    {
        header("Content-type: " . $contentType);
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        switch ($contentType) {
            case 'image/png':
                imagepng($img);
                break;
            case 'image/jpeg':
                imagejpeg($img);
                break;
            case 'image/gif':
                imagegif($img);
                break;
            default:
                imagejpeg($img);
        }
        return true;
    }

    /**
     * 输出页面
     * @param string $filename
     * @param array $data
     * @return bool
     */
    public static function view(string $filename, $data = [])
    {
        return self::appendBody(self::renderView($filename, $data));
    }

    /**
     * 渲染页面，不会输出，而是返回渲染的结果
     * @param string $filename
     * @param array $data
     * @return string
     * @throws \InvalidArgumentException
     */
    protected static function renderView(string $filename, $data = []): string
    {
        $filePath = self::$viewPath . trim($filename, '/') . self::$fileExt;
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException($filePath . '文件不存在：' . $filePath);
        }
        foreach ($data as $key => $val) {
            $$key =& $data[ $key ];
        }
        ob_start();
        //使用include而不是include_once将允许我们多次加载同名文件
        include($filePath);
        $buffer = ob_get_contents();
        @ob_end_clean();
        return $buffer;
    }

    /**
     * 下载文件
     * @param string $filename
     * @param string $rename
     * @return bool
     */
    public static function file(string $filename, string $rename = '')
    {
        if (!@is_file($filename) || ($size = @filesize($filename)) === false) {
            return false;
        }
        $fp = @fopen($filename, 'rb');
        if ($fp === false) {
            return false;
        }
        if (empty($rename)) {
            $filenames = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
            $rename = end($filenames);
        }
        //设置响应头
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $rename . '"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $size);
        header('Cache-Control: private, no-transform, no-store, must-revalidate');
        // 1MB每次
        while (!feof($fp) && ($data = fread($fp, 1048576)) !== false) {
            echo $data;
        }
        fclose($fp);
        return true;
    }

    public static function download(string $data, string $filename)
    {
        $size = strlen($data);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $size);
        header('Cache-Control: private, no-transform, no-store, must-revalidate');
        self::appendBody($data);
    }

    public static function error(int $code = 500, string $err = '')
    {
        return self::statusCode($code, $err);
    }

    /**
     * 设置响应码
     * @param int $code
     * @param string $text
     * @return bool
     */
    public static function statusCode(int $code, string $text = '')
    {
        if (empty($text)) {

            $text = StatusCode::text($code);
        }
        if (strpos(PHP_SAPI, 'cgi') === 0) {
            $status='Status: ' . $code . ' ' . $text;
        } else {
            $server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            $status = $server_protocol . ' ' . $code . ' ' . $text;
        }
        self::$statusHeader = $status;
        return true;
    }
}