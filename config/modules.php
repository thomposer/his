<?php
return  [
        'admin' => [
            'class' => 'app\modules\admin\AdminModule',
            
        ],
        'rbac' => ['class' => 'app\modules\rbac\RbacModule'],
        'newRbac' => [
            'class' => 'app\modules\newRbac\newRbacModule',
        ],
        'user' => [
            'class' => 'app\modules\user\UserModule',
        ],
        'test' => [
            'class' => 'app\test_modules\test\Module',
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ]
        
    ];