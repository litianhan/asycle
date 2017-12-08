<?php

namespace Asycle\Core\Encryption;

/**
 *
 * Time: 16:23
 */
class Encrypter{
    /**
     * 加密密钥
     *
     * @var string
     */
    protected static $key = '';

    /**
     * 加密算法
     *
     * @var string
     */
    protected static $cipher = '';

    public function __construct()
    {
    }
    public static function init(string $key,string $cipher = 'AES-128-CBC'){
        self::$key = $key;
        self::$cipher = $cipher;
    }

    /**
     *
     * 检查密钥和算法是否匹配
     * @param  string  $key
     * @param  string  $cipher
     * @return bool
     */
    public static function supported($key, $cipher)
    {
        $length = mb_strlen($key, '8bit');
        return ($cipher === 'AES-128-CBC' && $length === 16) || ($cipher === 'AES-256-CBC' && $length === 32);
    }

    /**
     *加密
     *
     * @param  mixed  $value
     * @param  bool  $throwException
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function encrypt(string $value, $throwException = true)
    {
        if( ! self::supported(self::$key,self::$cipher)){
            throw new \RuntimeException('The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.');
        }
        $iv = random_bytes(16);
        $value = \openssl_encrypt($value, self::$cipher, self::$key, 0, $iv);

        if ($value === false) {
            if($throwException){
                throw new \RuntimeException('Could not encrypt the data.');
            }else{
                return false;
            }

        }
        $iv = base64_encode($iv);

        $mac = self::hash($iv = base64_encode($iv), $value);

        $json = json_encode(compact('iv', 'value', 'mac'));

        if (! is_string($json)) {
            if($throwException){
                throw new \RuntimeException('Could not encrypt the data.');
            }else{
                return false;
            }
        }

        return base64_encode($json);
    }

    /**
     *
     * @param  mixed  $payload
     * @param  bool  $throwException
     * @return string
     *
     * @throws \RuntimeException
     */
    public function decrypt(string $payload, $throwException = true)
    {
        if( ! self::supported(self::$key,self::$cipher)){
            throw new \RuntimeException('The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.');
        }
        $payload = $this->getJsonPayload($payload);
        $iv = base64_decode($payload['iv']);
        $decrypted = \openssl_decrypt(
            $payload['value'], self::$cipher, self::$key, 0, $iv
        );

        if ($decrypted === false) {
            if($throwException){
                throw new \RuntimeException('Could not decrypt the data.');
            }else{
                return false;
            }

        }

        return  $decrypted;
    }
    /**
     *
     * @param  string  $iv
     * @param  mixed  $value
     * @return string
     */
    protected static function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv.$value, self::$key);
    }

    /**
     *
     * @param  string  $payload
     * @return array
     *
     * @throws \RuntimeException
     */
    protected static function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);
        if (! self::validPayload($payload)) {
            throw new \RuntimeException('The payload is invalid.');
        }
        if (! self::validMac($payload)) {
            throw new \RuntimeException('The MAC is invalid.');
        }
        return $payload;
    }

    /**
     *
     * @param  mixed  $payload
     * @return bool
     */
    protected static function validPayload($payload)
    {
        return is_array($payload) && isset(
                $payload['iv'], $payload['value'], $payload['mac']
            );
    }

    /**
     *
     * @param  array  $payload
     * @return bool
     */
    protected static function validMac(array $payload)
    {
        $calculated = self::calculateMac($payload, $bytes = random_bytes(16));

        return hash_equals(
            hash_hmac('sha256', $payload['mac'], $bytes, true), $calculated
        );
    }

    /**
     *
     * @param  array  $payload
     * @param  string  $bytes
     * @return string
     */
    protected static function calculateMac($payload, $bytes)
    {
        return hash_hmac(
            'sha256', self::hash($payload['iv'], $payload['value']), $bytes, true
        );
    }

    /**
     *
     * @return string
     */
    public function getKey()
    {
        return self::$key;
    }
}