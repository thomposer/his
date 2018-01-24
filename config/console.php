<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
//           'class' => 'app\common\component\CommonMemCache',
//             'useMemcached' => true,
//             'type' => true,
//             'servers' => [
//                 [
//                     'host' => '10.66.187.189',
//                     'port' => 9101,
//                     'weight' => 100,
//                 ],
//             ],
         //   'class' => 'yii\caching\DbCache',
         //   'db' => 'recordDb',
         //   'cacheTable' => 'gzh_cache'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qq.com', //每种邮箱的host配置不一样
                'username' => 'zhangtuqiang@qq.com',
                'password' => 'lodmpeikcchwbgjc',
                'port' => '25',
                'encryption' => 'tls',
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['zhangtuqiang@qq.com' => '医信科技有限公司']
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1000,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'trace', 'info', 'profile'],
                    'exportInterval' => 100, //导出数量，默认1000
                    'except' => ['yii\db\*', 'app\models\*'],
                    'logFile' => '@runtime/other/' . date("Y-m-d", time()) . '.log', //定义日志路径
                    'logVars' => [], //这些变量值将被追加至日志中
                ],
                'sql' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info', 'trace', 'profile'],
                    'logVars' => [],
                    'exportInterval' => 100, //导出数量，默认1000
                    //表示以yii\db\或者app\models\开头的分类都会写入这个文件
                    'categories' => ['yii\db\*', 'app\models\*'],
                    //表示写入到文件sql文件夹下的log中
                    'logFile' => '@runtime/sql/' . date('Y-m-d', time()) . '.log',
                ],
            ],
        ],
        'db' => $db,
        'recordDb' => require(__DIR__ . '/db/recordDb.php'),
        'cardCenter' => require(__DIR__ . '/db/cardCenter.php'),
        'errorHandler' => [
            // web error handler
            // 'class' => '\bedezign\yii2\audit\components\web\ErrorHandler',
            // console error handler
            'class' => '\bedezign\yii2\audit\components\console\ErrorHandler',
        ],
    ],
    'params' => $params,
];

