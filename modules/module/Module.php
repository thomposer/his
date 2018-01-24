<?php

namespace app\modules\module;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\module\controllers';

    public function init()
    {
        parent::init();
        $this->layout = false;
        // custom initialization code goes here
    }
}
