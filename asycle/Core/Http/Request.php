<?php

namespace Asycle\Core\Http;

/**
 *
 * 请求功能类
 * Class Request
 * @package Asycle\Core
 * Date: 2017/4/28
 * Time: 16:40
 */

class Request
{
    protected static $baseUrl = APP_BASE_URL;
    public function __construct()
    {
    }

    /**
     * 返回键对应的值
     * 使用样例：
     * @param array $keys
     * @param null $default
     * @param bool $xssClean
     * @param bool $returnObject
     * @return array|\stdClass
     */
    public static function fetchInput(array $keys,$default = null ,$xssClean = false,$returnObject = false){
        if($returnObject){
            $input = new \stdClass();
            foreach ($keys as $key){
                $input->$key = self::input($key,$default,$xssClean);
            }
        }else{
            $input = [];
            foreach ($keys as $key){
                $input[$key] = self::input($key,$default,$xssClean);
            }
        }
        return $input;
    }
    public static function fetchHeader(array $params,$default = null ,$xssClean = false,$returnObject = false){
        if($returnObject){
            $input = new \stdClass();
            foreach ($params as $param){
                $input->$param = self::header($param,$default,$xssClean);
            }
        }else{
            $input = [];
            foreach ($params as $param){
                $input[$param] = self::header($param,$default,$xssClean);
            }
        }
        return $input;
    }
    public static function fetchCookie(array $params,$default = null ,$xssClean = false,$returnObject = false){
        if($returnObject){
            $input = new \stdClass();
            foreach ($params as $param){
                $input->$param = self::cookie($param,$default,$xssClean);
            }
        }else{
            $input = [];
            foreach ($params as $param){
                $input[$param] = self::cookie($param,$default,$xssClean);
            }
        }
        return $input;
    }
    public static function inputInteger(string $key,$min = null,$max = null){
        $value = self::input($key);
        if( ! is_numeric($value)){
            return null;
        }
        $value = intval($value);
        if( ! is_null($min) and $value < $min){
            return null;
        }
        if( ! is_null($max) and $value > $max){
            return null;
        }
        return $value;
    }
    public static function inputString(string $key,$minLength = null,$maxLength = null,$xssClean = false){
        $value = self::input($key,null,$xssClean);
        if( is_null($value)){
            return null;
        }
        $value = strval($value);
        $length = strlen($value);
        if( ! is_null($minLength) and $length < $minLength){
            return null;
        }
        if( ! is_null($maxLength) and $length > $maxLength){
            return null;
        }
        return $value;
    }
    public static function inputFloat(string $key,$min = null,$max = null){
        $value = self::input($key);
        if( ! is_numeric($value)){
            return null;
        }
        $value = floatval($value);
        if( ! is_null($min) and $value < $min){
            return null;
        }
        if( ! is_null($max) and $value > $max){
            return null;
        }
        return $value;
    }
    /**
     * 获取输入
     * @param string $key
     * @param null $default
     * @param bool $xssClean
     * @return null|string
     */
    public static function input(string $key,$default = null,$xssClean = false){
        $value = $_POST[ $key ] ?? $_GET[ $key ] ?? $default;
        if(is_null($value)){
            return null;
        }
        if($xssClean){
            return self::$xssClean($value);
        }
        return $value;
    }

    /**
     * 获取所有输入
     * @param bool $xssClean
     * @return array
     */
    public static function inputAll($xssClean = false){
        $result = [];
        foreach ($_GET as $key=>$value){
            $result[$key] = $value;
        }
        foreach ($_POST as $key=>$value){
            $result[$key] = $value;
        }
        if($xssClean){
            foreach ($result as $key=>$value){
                $result[$key]=self::xssClean($value);
            }
        }
        return $result;
    }

    /**
     * 获取请求头
     * @param string $key
     * @param null $default
     * @param bool $xllClean
     * @return null|string
     */
    public static function header(string $key,$default = null,$xllClean = false){
        $key = 'HTTP_' . strtoupper($key);
        if( ! isset($_SERVER[$key])){
            return $default;
        }
        if($xllClean){
            return self::xssClean($_SERVER[$key]);
        }
        return $_SERVER[$key];
    }

    /**
     * 获取所有请求头
     * @param bool $xllClean
     * @return array
     */
    public static function headerAll($xllClean = false){
        $headers = [];
        foreach ($_SERVER as $key => $val)
        {
            if (sscanf($key, 'HTTP_%s', $header) === 1)
            {
                $headers[$key] = $_SERVER[$key];
            }
        }
        if($xllClean){
            foreach ($headers as $key=>$value){
                $headers[$key] = self::xssClean($value);
            }
        }
        return $headers;
    }

    /**
     * 获取客户端发送的json数据
     * @return mixed
     */
    public static function inputJson()
    {
        return file_get_contents("php://input");
    }
    /**
     * 请求引用页
     * @param null $default
     * @param bool $clean
     * @return null|string
     */
    public static function referer($default = null,$clean = false){
        return self::server('HTTP_REFERER',$default,$clean);
    }
    /**
     * 获取键对应的COOKIE数据
     * @param string $key
     * @param null $default
     * @param bool $clean
     * @return null|string
     */
    public static function cookie(string $key, $default = null,$clean = false)
    {
        if ( ! isset($_COOKIE[ $key ])) {
            return $default;
        }
        if ($clean) {
            return self::xssClean($_COOKIE[ $key ]);
        }
        return $_COOKIE[ $key ];
    }

    /**
     * 获取所有COOKIE数据
     * @param bool $clean
     * @return array
     */
    public static function cookieAll($clean = false)
    {
        $cookies = [];
        foreach ($_COOKIE as $key=>$value){
            $cookies[$key] = $value;
        }
        if ($clean) {
            foreach ($_COOKIE as $key=>$value){
                $cookies[$key] = self::xssClean($value);
            }
        }
        return $cookies;
    }
    /**
     * 是否是ajax请求
     * @return bool
     */
    public static function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * 是否cli请求
     * @return bool
     */
    public static function isCli(): bool
    {
        return (PHP_SAPI === 'cli');
    }

    /**
     * 获取上传的文件信息
     * @param string $fileIndex
     * @return array
     */
    public static function files(string $fileIndex = ''): array
    {
        $uploadFiles = [];
        if (empty($fileIndex)) {
            $fileIndex = 'file';
        }
        if (!isset($_FILES[ $fileIndex ]['tmp_name'])) {
            return $uploadFiles;

        }
        if ( ! is_uploaded_file($_FILES[ $fileIndex ]['tmp_name'])) {
            return $uploadFiles;
        }
        return $_FILES[ $fileIndex ];
    }

    /**
     * 获取$_SERVER中的键信息
     * @param string $key
     * @param null $default
     * @param bool $clean 是否进行xss过滤
     * @return null|string
     */
    public static function server(string $key, $default = null,$clean = false)
    {
        $key = strtoupper($key);
        if ( ! isset($_SERVER[ $key ])) {
            return $default;
        }
        if ($clean) {
            return self::xssClean($_SERVER[ $key ]);
        }
        return $_SERVER[ $key ];
    }

    public static function protocol(): string
    {
        if (self::isHttps()) {
            return 'https://';
        } else {
            return 'http://';
        }
    }

    /**
     * 获取host，如果$_SERVER['HTTP_HOST']不存在，将使用服务端ip
     * @return string
     * @throws \HttpRequestException
     */
    public static function host(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if (empty($host)) {
            //使用server ip代替
            $host = self::serverAddress();
        }
        return $host;
    }
    /**
     * 服务端地址
     * @return string
     */
    public static function serverAddress(): string
    {
        if (isset($_SERVER) and isset($_SERVER['SERVER_ADDR'])) {
            $serverAddress = filter_var($_SERVER['SERVER_ADDR'], FILTER_VALIDATE_IP);
            if (is_string($serverAddress)) {
                return $serverAddress;
            }
        }
        return '';
    }
    public static function port(): int
    {
        $port = $_SERVER['REMOTE_PORT'] ?? 0;
        return intval($port);
    }

    public static function baseURL(string $uri = ''): string
    {
        if( empty(APP_BASE_URL)){
            $url = self::protocol() . self::host() . '/' . trim($uri, '/');
        }else{
            $url = rtrim(APP_BASE_URL,'/') . '/'.ltrim($uri,'/');
        }

        return $url;
    }

    public static function baseIndexNameURL(string $uri = '',$indexName = 'index.php')
    {
        if (!empty($indexName)) {
            $indexName = trim($indexName, '/') . '/';
        }
        $url = self::protocol() . self::host() . '/' . $indexName . trim($uri, '/');
        return $url;
    }

    public static function method($upper = false): string
    {
        $method = strtolower($_SERVER['REQUEST_METHOD'] ?? '');
        if ( ! in_array($method, ['get', 'post', 'head', 'put', 'delete', 'connect', 'options', 'trace'])) {
            $method = 'get';
        }
        if ($upper) {
            $method = strtoupper($method);
        }
        return $method;
    }

    /**
     * 是否https协议
     * @return bool
     */
    public static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return true;
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }

    /**
     * 请求是否来自微信浏览器
     * @return bool
     */
    public static function isFromMicroMessenger(): bool
    {
        $ua = $_SERVER["HTTP_USER_AGENT"] ?? '';
        if (stripos($ua, 'MicroMessenger') === FALSE) {
            // 非微信
            return false;
        }
        return true;
    }

    /**
     * 请求是否来自移动端
     * @return bool
     */
    public static function isFromMobile(): bool
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备
        if (isset ($_SERVER['HTTP_VIA']))
        {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }

        if(isset($_SERVER['HTTP_USER_AGENT'])){
            $clients = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clients) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 用户代理
     * @param bool $clean
     * @return string
     */
    public static function userAgent($clean = false): string
    {
        return self::server('HTTP_USER_AGENT', $clean) ?? '';
    }

    /**
     * 客户端地址
     * @return string
     */
    public static function remoteAddress(): string
    {
        if (isset($_SERVER) and isset($_SERVER['REMOTE_ADDR'])) {
            $clientAddress = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);;
            if (is_string($clientAddress)) {
                return $clientAddress;
            }
        }
        return '';
    }
    protected static function xssClean(string $val): string
    {
        if (empty($val)) {
            return $val;
        }
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
        $val = htmlspecialchars($val);
        return $val;
    }
}