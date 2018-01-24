<?php

namespace app\modules\follow\models;

class Message extends Follow
{

    public $message; //消息内容
    public $attachment; //附件

    /**
     * @inheritdoc
     */

    public function rules() {
        return [
            [['message'], 'string', 'max' => 300],
            [['attachment'], 'string'],
            ['message', 'validateMessage', 'skipOnEmpty' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'message' => '消息内容',
            'attachment' => '附件'
        ];
    }

    public function validateMessage($attribute, $params) {
        if (!$this->hasErrors()) {
            if (!$this->message && !$this->attachment) {
                $this->addError($attribute, '请填写消息内容');
            }
        }
    }

}
