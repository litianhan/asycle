<?php

namespace Asycle\Core\Mail;
/**
 * Date: 2017/9/7
 * Time: 20:57
 */
interface MailHandlerInterface{
    public function setConfig($config);
    public function getErrorInfo();
    public function send($to,$title = '',$body = '',$attach = [],$cc = [],$bcc = []):bool ;
}