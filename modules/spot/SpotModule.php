<?php

namespace app\modules\spot;

class SpotModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\spot\controllers';

    public function init()
    {
        parent::init();
        $this->layout = false;

        // custom initialization code goes here
    }
}
