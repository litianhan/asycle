<?php

namespace Asycle\Core\Mail\Handler;
use Asycle\Core\Mail\MailHandlerInterface;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Date: 2017/9/13
 * Time: 16:38
 */

class PHPMailHandler implements MailHandlerInterface {
    protected $config = [];
    protected $mailer = null;
    public function setConfig($config){
        $this->config = $config;
    }
    public function send($to = [],$title = '',$body = '',$attach = [],$cc = [],$bcc = []): bool
    {
        $config = &$this->config;
        if(is_null($this->mailer)){
            $this->mailer = new PHPMailer();
        }
        $mail = &$this->mailer;
        $mail->SMTPDebug = $config['debug'];
        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();
        //链接qq域名邮箱的服务器地址
        $mail->Host = $config['host'];
        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth = $config['auth'];
        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username = $config['username'];
        //smtp登录的密码 使用生成的授权码
        $mail->Password = $config['password'];
        //设置使用ssl加密方式登录鉴权
        if($config['ssl'] ?? false){
            $mail->SMTPSecure = 'ssl';
        }
        //设置ssl连接smtp服务器的远程服务器端口号
        $mail->Port = $config['port'];
        //设置发送的邮件的编码
        $mail->CharSet = $config['charset'] ?? 'utf-8';
        $mail->setFrom($config['from'], $config['from_name']);
        //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不
        //同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
        //添加多个收件人 则多次调用方法即可
        foreach($to as $val){
            $mail->addAddress($val);
        }

        //设置用户回复的邮箱
        $mail->addReplyTo($config['reply_to']);

        //设置抄送人
        foreach($cc as $val){
            $mail->addCC($val);
        }

        //密送者，Mail Header不会显示密送者信息
        foreach($cc as $val){
            $mail->addBCC($val);
        }
        foreach($attach as $key=>$val){
            $mail->addAttachment($val, $key);
        }

        //添加该邮件的主题
        $mail->Subject = $title;
        //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数>读取本地的html文件
        $mail->Body = $body;

        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        if($config['html']){
            $mail->isHTML($config['html']);
            //添加邮件正文 上方将isHTML设置成了false时调用
            $mail->AltBody = strip_tags($body);
        }
        $res = $mail->send();
        $mail->clearAllRecipients();
        $mail->clearAddresses();
        $mail->clearAttachments();
        $mail->clearBCCs();
        $mail->clearCCs();
        $mail->clearReplyTos();
        $mail->createBody();
        $mail->clearCustomHeaders();
        $mail->createHeader();
        return $res;
    }
    public function getErrorInfo()
    {
        if($this->mailer instanceof PHPMailer){
            return $this->mailer->ErrorInfo;
        }
        return '';
    }
}