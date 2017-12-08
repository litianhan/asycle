<?php
namespace Asycle\Core\Logger\Handler;
use Asycle\Core\Logger\LoggerInterface;

/**
 * Date: 2017/9/9
 * Time: 17:25
 */
class FileHandler implements LoggerInterface{

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @return null
     */
    public function log($level, $message)
    {
        if(APP_LOG_FILE_MODE === 1){
            $date = 'log';
        }elseif(APP_LOG_FILE_MODE === 2){
            $date = date('Y-m-d');
        }elseif(APP_LOG_FILE_MODE === 3){
            $date = date('Y-m');
        }else{
            $date = date('Y');
        }
        $filename = $date.APP_LOG_FILE_EXTENSION;
        if( ! is_dir(APP_PATH_WRITABLE_LOG)){
            mkdir(APP_PATH_WRITABLE_LOG,APP_LOG_FILE_PERMISSION,true);
        }
        $filePath = APP_PATH_WRITABLE_LOG . $filename;
        $newFile = false;
        if ( ! file_exists($filePath)) {
            $newFile = true;
        }
        if ( ! $fp = fopen($filePath, 'ab')) {
            return false;
        }
        flock($fp, LOCK_EX);
        for ($written = 0, $length = strlen($message); $written < $length; $written += $result) {
            if (($result = fwrite($fp, substr($message, $written))) === false) {
                break;
            }
        }
        flock($fp, LOCK_UN);
        fclose($fp);

        if ($newFile) {
            chmod($filePath, APP_LOG_FILE_PERMISSION);
        }
        return true;
    }
}