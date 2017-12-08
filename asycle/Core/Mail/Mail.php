<?php

namespace Asycle\Core\Mail;
/**
 * Date: 2017/9/7
 * Time: 20:57
 */
class Mail{
    protected $handler = null;
    protected $to = [];
    protected $title = '';
    protected $body = '';
    protected $cc = [];
    protected $bcc = [];
    protected $attach = [];
    protected $config = [];
    public function __construct($config = [])
    {
        $this->config = $config;
    }
    public function getErrorInfo(){
        if($this->handler instanceof MailHandlerInterface){
            return $this->handler->getErrorInfo();
        }
        return '';
    }
    public function setHandler(MailHandlerInterface $handler){
        $this->handler = $handler;
        $this->handler->setConfig($this->config);
        return $this;
    }
    public function to(array $to = []){
        $this->to = $to;
        return $this;
    }
    public function title(string $name){
        $this->title = $name;
        return $this;
    }
    public function body(string $body){
        $this->body = $body;
        return $this;
    }
    public function attach($file = []){
        $this->attach = $file;
        return $this;
    }
    public function send(){
        $result = false;
        if($this->handler instanceof MailHandlerInterface){
            $result = $this->handler->send(
                $this->to,
                $this->title,
                $this->body,
                $this->attach,
                $this->cc,
                $this->bcc
                );
        }
        $this->clear();
        return $result;

    }
    protected function clear(){
        $this->to = '';
        $this->title = '';
        $this->body = '';
        $this->attach = [];
        $this->cc = [];
        $this->bcc = [];
    }
}