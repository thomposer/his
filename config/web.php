<?php

$params = require(__DIR__ . '/params.php');
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'joker',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '.html',
            'rules' => [
                '' => 'manage/default/index',
                '<module:\w+>/<controller:\w+>/<id:\d+>' => '<module>/<controller>/view',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
            ],
            
        ],
        
        'assetManager'=>[
            // 设置存放assets的目录
            'basePath'=>'@webroot/public/assets',
            // 设置访问assets目录的地址
            'baseUrl'=>'@web/public/assets',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/index/login']
        ],
        'errorHandler' => [
            'errorAction' => 'user/default/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            //'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1000,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning','trace','info'],
                    'exportInterval' => 1000,//导出数量，默认1000
                    'logFile' => '@runtime/logs/'.date("Ymd", time()) .'/app.log',//定义日志路径
                    'logVars' => ['_SERVER']//这些变量值将被追加至日志中
                    
                    ],
            ],
            
        ],
        'db' => require(__DIR__ . '/db.php'),
        'authManager' => [
            'class' =>'yii\rbac\DbManager',
        ]
    ],
    'homeUrl' => '@web/manage/index/index.html',
    'params' => $params,
    'modules' => [
        
        'menu' => [
            'class' => 'app\modules\menu\MenuModule',
        ],
        'user' => [
            'class' => 'app\modules\user\UserModule',
        ],
        'rbac' => [
            'class' => 'app\modules\rbac\RbacModule',  
        ],
        'module' => [
            'class' => 'app\modules\module\Module',
        ],
        'behavior' => [
            'class' => 'app\modules\behavior\BehaviorModule',
        ],
        'apply' => [
            'class' => 'app\modules\apply\ApplyModule',  
        ],
        'spot' => [
            'class' => 'app\modules\spot\SpotModule'
        ],
        'manage' => [
            'class' => 'app\modules\manage\manageModule'
        ]
        
    ],
       
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
                'class' => 'yii\gii\Module',
            'generators' => [
                'crud' => ['class' => 'mdm\gii\generators\crud\Generator'],
                'mvc' => ['class' => 'mdm\gii\generators\mvc\Generator'],
                'migration' => ['class' => 'mdm\gii\generators\migration\Generator'],
            ],
        
        
    ];
}

return $config;
