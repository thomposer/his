<?php
use yii\helpers\Url;

return [
    // [
         // 'class' => 'kartik\grid\CheckboxColumn',
         // 'width' => '20px',
    // ],
    // [
         // 'class' => 'kartik\grid\SerialColumn',
         // 'width' => '30px',
    // ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'spot_id',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'creater_id',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'executor',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'content',
    ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'execute_time',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'create_time',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'update_time',
    // ],
    [
        'class' => 'app\common\component\ActionColumn',
        'ajaxList' => [
            'update' => true, //默认开启ajax的update,delete,关闭view
            'delete' => true
        ],
    ],

];   