<?php

namespace app\modules\apply;

class ApplyModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\apply\controllers';

    public function init()
    {
        parent::init();
        $this->layout = false;
        // custom initialization code goes here
    }
}
