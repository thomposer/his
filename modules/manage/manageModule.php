<?php

namespace app\modules\manage;

class manageModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\manage\controllers';
    
    public function init()
    {
        parent::init();
        $this->layout = false;
        // custom initialization code goes here
    }
}
