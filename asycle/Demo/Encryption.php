<?php

namespace Asycle\Demo;
/**
 * Date: 2017/9/8
 * Time: 14:36
 */
class Encryption{
    /**
     * AES加密解密实例
     */
    public static function aes(){
        header('Content-Type: text/plain;charset=utf-8');
        $data = 'plaintext';
        $key = base64_encode(openssl_random_pseudo_bytes(32));
        $iv = base64_encode(openssl_random_pseudo_bytes(16));
        echo '内容: '.$data."\n";

        $encrypted = openssl_encrypt($data, 'aes-256-cbc', base64_decode($key), OPENSSL_RAW_DATA, base64_decode($iv));
        echo '加密: '.base64_encode($encrypted)."\n";

        $encrypted = base64_decode('To3QFfvGJNm84KbKG1PLzA==');
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', base64_decode($key), OPENSSL_RAW_DATA, base64_decode($iv));
        echo '解密: '.$decrypted."\n";
    }
    /**
     * RAS加密解密实例
     */
    public static function rsa(){
        header('Content-Type: text/plain;charset=utf-8');
        $data = 'plaintext';
        echo '原始内容: '.$data."\n";

        openssl_public_encrypt($data, $encrypted, file_get_contents(dirname(__FILE__).'/rsa_public_key.pem'));
        echo '公钥加密: '.base64_encode($encrypted)."\n";

        $encrypted = base64_decode('nMD7Yrx37U5AZRpXukingESUNYiSUHWThekrmRA0oD0=');
        openssl_private_decrypt($encrypted, $decrypted, file_get_contents(dirname(__FILE__).'/rsa_private_key.pem'));
        echo '私钥解密: '.$decrypted."\n";
    }
}