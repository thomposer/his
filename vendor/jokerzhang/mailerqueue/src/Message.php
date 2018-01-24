<?php

namespace jokerzhang\mailerqueue;

use Yii;
use yii\base\InvalidConfigException;
class Message extends \yii\swiftmailer\Message{
    /**
     * 
     * @throws InvalidConfigException
     * @return 发送邮件时，将邮件信息存在在redis中
     */
    public function queue(){
        
        
        $redis = Yii::$app->redis;
        if(empty($redis)){
           throw new InvalidConfigException('redis not found in config.'); 
        }
        
        $mailer = Yii::$app->mailer;
        if(empty($mailer) || !$redis->select($mailer->db)){
            throw new InvalidConfigException('db not defined');
        }
        $message = [];
        $message['from'] = array_keys($this->getFrom());
        $message['to'] = array_keys($this->getTo());
        $message['cc'] = !empty($this->getCc())?array_keys($this->getCc()):'';
        $message['bcc'] = !empty($this->getBcc())?array_keys($this->getBcc()):'';
        $message['reply_to'] = !empty($this->getReplyTo())?array_keys($this->getReplyTo()):'';
        $message['charset'] = $this->getCharset();
        $message['subject'] = $this->getSubject();
        $parts = $this->getSwiftMessage()->getChildren();
        if(!is_array($parts) || !sizeof($parts)){
            $parts = [$this->getSwiftMessage()];
        }
        foreach ($parts as $v){
            
            if (! $v instanceof \Swift_Mime_Attachment){//若不是其对象，则$v为内容
                switch ($v->getContentType()){//判断其内容类型
                    
                    case 'text/html' : //html
                        $message['html_body'] = $v->getBody();
                        break;
                    case 'text/plain' : //纯文本
                        $message['text_body'] = $v->getBody();
                        break;
                }
                if(!$message['charset']){
                    $message['charset'] = $v->getCharset();
                }
            }
        }
        
        return $redis->rpush($mailer->key,json_encode($message));//向右依次添加进redis队列里
        
    }
    
}