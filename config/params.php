<?php

return [
    'adminEmail' => 'admin@example.com',
    'hisApiHost' => YII_DEBUG ? 'http://dev.hisapi.easyhin.com' : 'http://hisapi.easyhin.com', //his-api域名
    'cdnHost' => YII_DEBUG ? 'http://dev.hiscdn.easyhin.com' : 'https://s.easyhin.com/his', //cdn资源服务器域名
    'uploadUrl' => 'uploads/', //上传图片和附件资源的文件夹
    'syncNum' => '108', //his上传资源的脚本编号
    'followChatHealthId' => YII_DEBUG ? 541 : 541, //cdn资源服务器域名
    'redis' => [
        'redisHost' => YII_DEBUG ? '10.66.153.202' : '10.66.106.109', //Redis服务的URI
        'redisPort' => YII_DEBUG ? '6379' : '6379', //Redis服务的PORT
        'redisInstanceid' => YII_DEBUG ? 'crs-flau3pzf' : 'crs-5i6fn2ax', //Redis服务的Instanceid
        'redisPwd' => YII_DEBUG ? '123!!123' : '123!!123', //Redis服务的Pwd
    ],
    'dataReport' => [//数据上报的模块
        'api' => [//模快api/medicine-description/item  用药指南
            'medicine-description' => ['item']//controller=>action 
        ],
        'make_appointment' => [//模快make_appointment/appointment/save-time-config  用药指南
            'appointment' => ['save-time-config']//controller=>action 
        ],
        'schedule' => [//模快schedule/schedule/add-scheduling  新增排班
            'schedule' => ['add-scheduling']//controller=>action 
        ]
    ],
    'daParamsConfig' => [//迪安参数配置
        'spotId' => YII_DEBUG ? 75 : 62,//只取上海诊所的数据  测试环境使用麦兜诊所
        'clientId' => '上海义信儿科门诊部有限公司', //账户
        'clientGUID' => '57DBCA857D444ECAE0530BF0000A29FD',//密码
    ]
];
