<?php

namespace Asycle\Core\Debug;
use Asycle\Core\Benchmark;
use Asycle\Core\Connection;
use Asycle\Core\Http\Request;
use Asycle\Core\Utils;

/**
 * Date: 2017/11/30
 * Time: 16:07
 */
class ToolBar{
    protected static $body = '';
    public function __construct()
    {
    }
    public static function bodyContent(){

        self::consoleLog('Asycle ToolBar :');
        self::consoleLog('Request:{');
        self::consoleLog(sprintf('time:%.3fs',microtime(true) - APP_START_TIME));
        self::consoleLog(sprintf('memory：%s',Utils::bytesToReadable(memory_get_usage()-APP_START_MEMORY)));
        $inputString = '';
        foreach (Request::inputAll() as $key=>$value){
            $inputString.=$key.':'.strval($value).',';
        }
        self::consoleLog('get:'.json_encode($_GET));
        self::consoleLog('post:'.json_encode($_POST));
        self::consoleLog('json:'.Request::inputJson());
        self::consoleLog('header:'.json_encode(Request::headerAll()));
        self::consoleLog('cookie:'.json_encode($_COOKIE));
        self::consoleLog('}');
        self::consoleLog('BenchMark:{');
        $bench = Benchmark::getRecords();
        foreach ($bench as $key=>$value){
            self::consoleLog(sprintf('%s:%.3fs,%s',$key,$value[0],
                Utils::bytesToReadable($value[1])));
        }
        self::consoleLog('}');
        self::consoleLog('SQL:{');
        $res = Connection::getQueryRecords();
        foreach ($res as $db=>$records){
            self::consoleLog('connection:'.$db);
            foreach ($records as $record){
                self::consoleLog(sprintf('[%.3fs] [%s] SQL:',$record[2]-$record[1],Utils::bytesToReadable($record[4]-$record[3])).$record[0]);
            }
            self::consoleLog(' ');
        }
        self::consoleLog('}');
        return '<script type="text/javascript">' . self::$body . '</script>';
    }
    protected static function consoleLog($msg){
        //使用单引号，可以打印json
        self::$body.='console.log(\''.$msg.'\');
        ';
    }
}