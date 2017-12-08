<?php
namespace Asycle\Libs;
/**
 * Date: 2017/5/22
 * Time: 13:05
 */
use Asycle\Core\Cache\CacheHandlerInterface;

/**
 * 微信公众号js签名
 * Class WeChat
 * @package Asycle\Core\libs
 */
class WeChat{
    /**
     * 公众号平台id
     * @var
     */
    protected $appId;//公众号平台id
    /**
     * 公众号平台appsecret
     * @var
     */
    protected $appSecret;
    /**
     * 缓存句柄
     * @var CacheHandlerInterface
     */
    protected $handler;
    protected $keyPrefix = '';
    public function __construct(string $appId,string $appSecret,CacheHandlerInterface $handler)
    {
        $this->keyPrefix = str_replace('\\','_',__CLASS__);
        $this->appId=$appId;
        $this->appSecret=$appSecret;
        $this->handler = $handler;
    }
    public function renderConfig($isDebug=false) {
        $signPackage = $this->GetSignPackage();
        $debug=($isDebug)?'true':'false';
        $jsConfig = '
        <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        <script type="text/javascript">
        wx.config({
            debug:'.$debug.',
            appId: "' . $signPackage["appId"] . '",
            timestamp: ' . $signPackage["timestamp"] . ',
            nonceStr: "' . $signPackage["nonceStr"] . '",
            signature: "' . $signPackage["signature"] . '",
            jsApiList: [ "onMenuShareTimeline", "onMenuShareAppMessage", "onMenuShareQQ", "onMenuShareWeibo", "hideOptionMenu", "showOptionMenu", "addCard", "chooseCard", "openCard", "getNetworkType"]
        });
        </script>';
        return $jsConfig;
    }
    public function renderEmptyConfig() {
        $jsConfig = '
        <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        <script type="text/javascript">
        wx.config({
            debug:false,
            appId: "",
            timestamp:0 ,
            nonceStr: "",
            signature: "",
            jsApiList: [ "onMenuShareTimeline", "onMenuShareAppMessage", "onMenuShareQQ", "onMenuShareWeibo", "hideOptionMenu", "showOptionMenu", "addCard", "chooseCard", "openCard", "getNetworkType"]
        });
        </script>';
        return $jsConfig;
    }
    protected function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $maxRandom=strlen($chars)-1;
        $nonceStr = "";
        for ($i = 0; $i < 16; $i++) {
            $nonceStr .= substr($chars, mt_rand(0, $maxRandom), 1);
        }
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);
        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    protected function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $key = $this->getKeyPrefix() . 'jsapi_ticket_'.$this->appId;
        $data = json_decode($this->handler->get($key));
        $ticket='';
        if (is_null($data) OR ! isset($data->expire_time) OR $data->expire_time < time()) {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            if(empty($res) OR empty($res->ticket)){
                return '';
            }
            $data->expire_time = time() + 7000;
            $data->jsapi_ticket = $res->ticket;
            $this->handler->save($key, json_encode($data),7000);
        } else {
            $ticket = $data->jsapi_ticket;
        }
        return $ticket;
    }

    protected function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $key = $this->getKeyPrefix() . 'access_token_' . $this->appId;
        $data = json_decode($this->handler->get($key));
        $access_token='';
        if (is_null($data) OR ! isset($data->expire_time) OR $data->expire_time < time()) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            if(empty($res) OR empty($res->access_token)){
                return '';
            }
            $data->expire_time = time() + 7000;
            $data->access_token = $res->access_token;
            $this->handler->save($key, json_encode($data),7000);
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    protected function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，
        //所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
    public function getKeyPrefix():string{
        return $this->keyPrefix;
    }
}