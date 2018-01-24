<?php

namespace app\modules\user;

class UserModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\user\controllers';

    public function init()
    {
        parent::init();
        $this->layout = false;
        // custom initialization code goes here
    }
}
