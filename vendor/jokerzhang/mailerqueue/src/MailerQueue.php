<?php

namespace jokerzhang\mailerqueue;
use Yii;
use yii\swiftmailer\Mailer;
use yii\base\InvalidConfigException;
use yii\web\ServerErrorHttpException;

class MailerQueue extends Mailer{
    
    public $messageClass = 'jokerzhang\mailerqueue\Message';
    
    public $db = 1;
    
    public $key = 'mailer';
    /**
     * 
     * @throws InvalidConfigException
     * @throws ServerErrorHttpException
     * @return boolean 从redis服务器获取发送的邮件信息，并依次发送
     */
    public function process(){
        
        $redis = Yii::$app->redis;
        if(empty($redis)){
            throw new InvalidConfigException('redis not found');
        }
        if($redis->select($this->db) && $messages = $redis->lrange($this->key,0,-1)){
            $messageObj = new Message();
            foreach ($messages as $v){
                $message = json_decode($v,true);
                if(empty($message) || !$this->setMessage($messageObj,$message)){
                    throw new ServerErrorHttpException('message error');
                }
                if($messageObj->send()){
                    $redis->lrem($this->key,1,$v);//删除redis的key的值
                }
            }
        }
        return true;
    }
    /**
     * @desc 设置发送邮件的基本信息
     * @param object $messageObj 发送邮件对象
     * @param array $message 邮件信息
     */
    public function setMessage($messageObj,$message){
        
        if(empty($messageObj)){
            return false;
        }
        if(!empty($message['to'])){
            $messageObj->setTo($message['to']);
        }else{
            return false;
        }
        if(!empty($message['from'])){
            $messageObj->setFrom($message['from']);
        }
        if (!empty($message['cc'])) {
            $messageObj->setCc($message['cc']);
        }
        if (!empty($message['bcc'])) {
            $messageObj->setBcc($message['bcc']);
        }
        if (!empty($message['reply_to'])) {
            $messageObj->setReplyTo($message['reply_to']);
        }
        if (!empty($message['charset'])) {
            $messageObj->setCharset($message['charset']);
        }
        if (!empty($message['subject'])) {
            $messageObj->setSubject($message['subject']);
        }
        if (!empty($message['html_body'])) {
            $messageObj->setHtmlBody($message['html_body']);
        }
        if (!empty($message['text_body'])) {
            $messageObj->setTextBody($message['text_body']);
        }
        return $messageObj;
    }
}