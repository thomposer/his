<?php

namespace app\modules\behavior;

class BehaviorModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\behavior\controllers';

    public function init()
    {
        parent::init();
        $this->layout = false;
    }
}
