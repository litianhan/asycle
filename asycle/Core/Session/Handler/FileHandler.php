<?php

namespace Asycle\Core\Session\Handler;

/**
 * Date: 2017/12/1
 * Time: 15:03
 */
class FileHandler implements \Asycle\Core\Session\SessionHandlerInterface{
    /**
     * 会话文件夹
     * @var string
     */
    protected $sessionFilePath = APP_PATH_WRITABLE_SESSION;
    public function __construct()
    {
    }
    /**
     * 开启会话
     * @param $sid
     * @param $uid
     * @param $data
     * @param $expired
     * @return bool
     */
    public function start(&$sid, &$uid, &$data, &$expired): bool
    {
        if(empty($sid)){
            return false;
        }
        $filename = $this->sessionFilePath.$sid;
        $json = file_get_contents($filename);
        if(empty($json)){
            unlink($filename);
            return false;
        }
        $content = json_decode($json,true);
        if(! is_array($content) or ! isset($content['uid'],$content['expired'],$content['data'])){
            return false;
        }
        $expired = intval($content['expired']);
        if($expired < time()){
            //已过期，删除文件
            unlink($filename);
            return true;
        }
        $uid = $content['uid'];
        $data = $content['data'];
        return true;
    }

    /**
     * 保存会话
     * @param $sid
     * @param $uid
     * @param $data
     * @param $ttl
     * @return bool
     */
    public function store($sid, $uid, $data, $ttl): bool
    {
        if(empty($sid) or empty($uid)){
            return false;
        }
        @$fp = fopen($this->sessionFilePath.$sid,'w');
        if( ! $fp){
            return false;
        }

        $data = [
            'expired'=>$ttl == 0 ? 0 : time() + $ttl,
            'uid'=>$uid,
            'data'=>$data
        ];
        $res  = fwrite($fp,json_encode($data));
        fclose($fp);
        return $res;
    }

    /**
     * 销毁会话
     * @param $sid
     * @param $uid
     * @return bool
     */
    public function destroy($sid, $uid): bool
    {
        if(empty($sid)){
            return false;
        }
        return unlink($this->sessionFilePath.$sid);
    }
}