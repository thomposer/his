<?php

namespace app\modules\rbac;

class RbacModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\rbac\controllers';

    public function init()
    {
        parent::init();
        $this->layout = false;
        // custom initialization code goes here
    }
}
