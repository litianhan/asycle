<?php
define('APP_START_TIME', microtime(true));//APP启动时的时间
define('APP_START_MEMORY', memory_get_usage()); //APP启动时的内存
if ( ! version_compare(PHP_VERSION, '7.0', '>=')){
    die('需要PHP7.0及以上版本。');
}
/**
 * APP基本环境常用配置
 * development 开发环境，如果程序出错，将在页面输出错误和异常信息
 * production 生产环境，程序抛出未捕获异常或者错误后，不向用户输出任何内容
 * testing 测试环境，类似于生产环境，使用测试环境的配置
 * maintenance 维护环境，所有接口不能访问，统一重定向到维护页面
 */
define('APP_ENVIRONMENT','development');//程序环境：为以上三值之一(分别为：开发、上线、维护)
define('APP_NAME', 'app');//APP文件夹名称
define('APP_BASE_URL','');//网站URL,例如：http://example.com/,用于跳转默认的url
define('APP_BASE_HOST','');//网站host,例如：example.com
define('APP_SALT','');//APP密码盐，长度16（AES-128-CBC）或32（AES-256-CBC），用于cookie加密等
define('APP_CIPHER','AES-128-CBC');//APP加密方式，例如：'AES-128-CBC,AES-256-CBC
define('APP_TIMEZONE','Asia/Shanghai');//时区，例如:Asia/Shanghai,为空则使用PHP默认的时区
define('APP_LANGUAGE','zh-cn');//语言，请与配置目录中Language下的文件夹相对应
define('APP_AUTOLOAD_FILE_EXT', '.php');//自加载PHP脚本时使用的后缀名
define('APP_DEFAULT_CHARSET','UTF-8');//默认字符编码
define('APP_INDEX_NAME','index.php');//公共入口文件名称，主要用于过滤或者添加入口文件到路由中

/**
 * 调试环境配置
 */
define('APP_DEBUG', APP_ENVIRONMENT === 'development');//APP调试是否开启
define('APP_SHOW_TOOL_BAR', true);//是否允许展示调试工具,只有http请求并且在DEBUG环境下才有效
define('APP_IS_PRODUCTION', APP_ENVIRONMENT === 'production');//APP是否生产环境
define('APP_IS_DEVELOPMENT', APP_ENVIRONMENT === 'development');//APP是否开发环境
define('APP_IS_TESTING', APP_ENVIRONMENT === 'testing');//APP是否测试环境
define('APP_IS_MAINTENANCE', APP_ENVIRONMENT === 'maintenance');//APP是否维护环境

/**
 * 基础目录
 */
chdir(__DIR__);
define('APP_PATH_PARENT', realpath('../') . DIRECTORY_SEPARATOR);//APP父级目录
define('APP_PATH',APP_PATH_PARENT . APP_NAME . DIRECTORY_SEPARATOR);//APP目录
define('APP_PATH_VIEWS', APP_PATH  . 'Views' .DIRECTORY_SEPARATOR);//APP视图目录
define('APP_PATH_CONFIG', APP_PATH  . 'config' .DIRECTORY_SEPARATOR);//APP配置目录
/**
 * 配置目录
 */
define('APP_PATH_CONFIG_ROUTE', APP_PATH_CONFIG  . 'route' .DIRECTORY_SEPARATOR);//APP路由目录
define('APP_PATH_CONFIG_VALUES',APP_PATH_CONFIG . 'values' . DIRECTORY_SEPARATOR);//APP键值目录
define('APP_PATH_CONFIG_LANGUAGE', APP_PATH_CONFIG . 'language' . DIRECTORY_SEPARATOR);//APP语言目录
define('APP_PATH_CONFIG_FILES', APP_PATH_CONFIG . 'files' . DIRECTORY_SEPARATOR);//APP内容文件目录
/**
 * 可写目录
 */
define('APP_PATH_WRITABLE', APP_PATH_PARENT . 'writable' . DIRECTORY_SEPARATOR);//可写目录，可写，存放日志、缓存等信息
define('APP_PATH_WRITABLE_DATA', APP_PATH_WRITABLE . 'data' . DIRECTORY_SEPARATOR);//数据目录
define('APP_PATH_WRITABLE_DEBUG',APP_PATH_WRITABLE . 'debug' . DIRECTORY_SEPARATOR);//debug内容文件
define('APP_PATH_WRITABLE_LOG',APP_PATH_WRITABLE .'logs' .DIRECTORY_SEPARATOR);//日志目录
define('APP_PATH_WRITABLE_CACHE',APP_PATH_WRITABLE .'cache' .DIRECTORY_SEPARATOR);//缓存目录
define('APP_PATH_WRITABLE_UPLOAD',APP_PATH_WRITABLE .'uploads' .DIRECTORY_SEPARATOR);//上传目录
define('APP_PATH_WRITABLE_SESSION',APP_PATH_WRITABLE  . 'session'.DIRECTORY_SEPARATOR);//会话文件目录

/**
 * 加载文件路径配置
 */
define('APP_FILE_COMPOSER', APP_PATH_PARENT .'vendor/autoload.php');//composer自加载文件
define('APP_FILE_ROUTE_WEB',APP_PATH_CONFIG_ROUTE  . 'web.php');//web路由配置文件
define('APP_FILE_ROUTE_COMMANDS',APP_PATH_CONFIG_ROUTE  . 'commands.php');//命令行路由配置文件

/**
 * cookie配置
 */
define('APP_COOKIE_PREFIX','');
define('APP_COOKIE_DOMAIN',APP_BASE_HOST);
define('APP_COOKIE_PATH','/');
define('APP_COOKIE_SECURE',false);
define('APP_COOKIE_HTTP_ONLY',false);
/**
 * session配置
 */
define('APP_SESSION_COOKIE_NAME','sid');//session的cookie名称
define('APP_SESSION_EXPIRATION',3600);//session的过期时间
define('APP_SESSION_FILE_PATH',APP_PATH_WRITABLE_SESSION);//session文件路径


/**
 * 日志配置
 * 1 emergency
 * 2 alert
 * 3 critical
 * 4 error
 * 5 warning
 * 6 debug
 * 7 notice
 * 8 info
 */
define('APP_LOG_PRODUCTION_THRESHOLD',4);//生产环境允许打印的日志级别阈值
define('APP_LOG_DEVELOPMENT_THRESHOLD',8);//开发环境允许打印的日志级别阈值
define('APP_LOG_TESTING_THRESHOLD',4);//测试环境允许打印的日志级别阈值
define('APP_LOG_MAINTENANCE_THRESHOLD',4);//维护环境允许打印的日志级别阈值
define('APP_LOG_FILE_EXTENSION','.txt');//日志文件后缀
define('APP_LOG_FILE_MODE',2);//日志文件形式，1单文件，2按天，3按月，4按年，默认按天
define('APP_LOG_FILE_PERMISSION',0644);//文件权限(i.e. 0700, 0644, etc.)
define('APP_LOG_DATE_FORMAT','Y-m-d H:i:s');//日期格式

/**
 * 加载环境配置文件
 */
require_once (__DIR__ .DIRECTORY_SEPARATOR.APP_ENVIRONMENT . APP_AUTOLOAD_FILE_EXT);

/**
 * 加载composer自加载文件
 */
$loader = require (APP_FILE_COMPOSER);
/**
 * 添加APP命名空间
 */
$loader->addPsr4('App\\', APP_PATH);



