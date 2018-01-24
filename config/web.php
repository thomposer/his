<?php

$params = require(__DIR__ . '/params.php');
$modules = require(__DIR__ . '/modules.php');
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'charset' => 'utf-8',
    'aliases' => [
        '@jokerzhang/mailerqueue' => '@vendor/jokerzhang/mailerqueue/src'
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'joker',
        ],
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:i:s',
            'defaultTimeZone' => 'RPC',
            'nullDisplay' => '',
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
            'class' => 'yii\web\AssetManager',
            // 设置存放assets的目录
            'basePath'=>'@webroot/public/assets',
            // 设置访问assets目录的地址
            'baseUrl'=>'@web/public/assets',
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js'
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [
                        YII_ENV_DEV ? 'css/bootstrap.css' :  'css/bootstrap.min.css',
                    ]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [
                        YII_ENV_DEV ? 'js/bootstrap.js' : 'js/bootstrap.min.js',
                    ]
                ]
            ],
        ],
        'cache' => [
             'class' => 'yii\redis\Cache',
             'redis' => [
                 'hostname' => 'localhost',
                 'port' => 6379,
                 'database' => 2
             ]               
//            'class' => 'yii\caching\MemCache',
//         //   'persistentId' => 'hisaa',
//            'useMemcached' => true,
//            'servers' => [
//                [
//                    'host' => '10.66.187.189',
//                    'port' => 9101,
////                     'weight' => 100,
//                ],
//            ],
            
        ],
        'session' => [
            'class' => 'yii\redis\Session',
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 3,
            ],
            'keyPrefix' => 'session_',
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
            'class' => 'jokerzhang\mailerqueue\MailerQueue',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'db' => 1,//reis存在哪个redis库
            'key' => 'mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
//                 'host' => 'smtp.qq.com',  //每种邮箱的host配置不一样
                'host' => 'smtp.exmail.qq.com',
//                 'username' => 'zhangtuqiang@qq.com',
//                 'password' => 'xilzvqugtjvjbgfj',
                'username' => 'ehospital@easyhin.com',
                'password' => 'Ehis168',
                'port' => '25',
                'encryption' => 'tls',
                 
            ],
            'messageConfig'=>[
                'charset'=>'UTF-8',
                'from'=>['ehospital@easyhin.com'=>'医信科技有限公司']
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1000,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning','trace','info','profile'],
                    'exportInterval' => 1,//导出数量，默认1000
                    'except'=>['yii\db\*','app\models\*'],
                    'logFile' => '@runtime/logs/other/'.date("Y-m-d", time()).'.log',//定义日志路径
                    'logVars' => [],//这些变量值将被追加至日志中
               
                 ],
                'sql' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning','info','trace','profile'],
                    'logVars'=>[],
                    'exportInterval' => 1,//导出数量，默认1000
                    //表示以yii\db\或者app\models\开头的分类都会写入这个文件
                    'categories'=>['yii\db\*','app\models\*'],
                    //表示写入到文件sql文件夹下的log中
                    'logFile'=>'@runtime/logs/sql/'.date('Y-m-d',time()).'.log',                    
                ],
//                 'httpException' => [
                    
//                 ]
            ],
            
        ],
        'db' => require(__DIR__ . '/db.php'),
        'recordDb' => require(__DIR__.'/db/recordDb.php'),
        'cardCenter' => require(__DIR__.'/db/cardCenter.php'),
        'authManager' => [
            'class' =>'yii\rbac\DbManager',
        ],
        'elasticsearch' => [
            'class' => 'app\common\component\ElasticSearchConnection',
            'nodes' => [
                ['http_address' => 'localhost:9200'],
                // configure more hosts if you have a cluster
            ],
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
    ],
    'homeUrl' => '@web/manage/index/index.html',
    'defaultRoute' => 'manage',
    'params' => $params,
    'modules' => $modules,
       
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = ['class' => 'yii\debug\Module','allowedIPs' => ['*']];

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
