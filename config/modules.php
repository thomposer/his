<?php

return [

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
    'spot' => [
        'class' => 'app\modules\spot\SpotModule'
    ],
    'manage' => [
        'class' => 'app\modules\manage\manageModule'
    ],
    'make_appointment' => [
        'class' => 'app\modules\make_appointment\makeAppointmentModule',
    ],
    'spot_set' => [
        'class' => 'app\modules\spot_set\spotSet',
    ],
    'patient' => [
        'class' => 'app\modules\patient\PatientModule',
    ],
    'report' => [
        'class' => 'app\modules\report\ReportModule',
    ],
    'triage' => [
        'class' => 'app\modules\triage\TriageModule',
    ],
    'api' => [
        'class' => 'app\modules\api\apiModule',
    ],
    'outpatient' => [
        'class' => 'app\modules\outpatient\OutpatientModule',
    ],
    'schedule' => [
        'class' => 'app\modules\schedule\ScheduleModule',
    ],
    'charge' => [
        'class' => 'app\modules\charge\ChargeModule',
    ],
    'room' => [
        'class' => 'app\modules\room\RoomModule',
    ],
    'pharmacy' => [
        'class' => 'app\modules\pharmacy\PharmacyModule',
    ],
    'cure' => [
        'class' => 'app\modules\cure\CureModule',
    ],
    'check' => [
        'class' => 'app\modules\check\Module',
    ],

    'gridview' => [
        'class' => '\kartik\grid\Module',
    ],
    'inspect' => [
        'class' => 'app\modules\inspect\InspectModule',
    ],
    'nurse' => [
        'class' => 'app\modules\nurse\NurseModule',
    ],
    'reception' => [
        'class' => 'app\modules\reception\ReceptionModule',
    ],
    'doctor' => [
            'class' => 'app\modules\doctor\DoctorModule',
    ],
    'overview' => [
            'class' => 'app\modules\overview\OverviewModule',
    ],
    'wechat' => [
            'class' => 'app\modules\wechat\WechatModule',
    ],
    'card' => [
            'class' => 'app\modules\card\CardModule',
    ],
    'message' => [

        'class' => 'app\modules\message\MessageModule',
    ],
    'medicine' => [
        'class' => 'app\modules\medicine\Module',
    ],
    'recharge' => [
        'class' => 'app\specialModules\recharge\Module',
    ],
    'growth' => [
        'class' => 'app\modules\growth\Module',
    ],
    'route' => [
        'class' => 'deepziyu\yii\rest\module\RouteModule'
    ],
    'data' => [
        'class' => 'app\modules\data\dataModule'
    ],
    'material' => [
        'class' => 'app\modules\material\Module',
    ],
    'check_code' => [
        'class' => 'app\modules\check_code\CheckCode',
    ],
    'audit' => [
            'class' => 'bedezign\yii2\audit\Audit',
            'accessIps' => ['*'], 
            'accessRoles' => null,
            'accessUsers' => [1],
            'db' => 'recordDb',
            'ignoreActions' => ['*'],
            'layout' => 'main',
        
    ],
    'follow' => [
        'class' => 'app\modules\follow\FollowModule',
    ],
    'stock' => [
        'class' => 'app\modules\stock\Module',
    ],
];
