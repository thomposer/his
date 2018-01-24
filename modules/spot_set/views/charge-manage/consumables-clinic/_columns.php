<?php
use yii\helpers\Url;
use app\modules\spot\models\Consumables;
use yii\helpers\Html;

return [
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'product_number',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
        'value' => function($dataProvider){
            return Consumables::$getType[$dataProvider->type];
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'specification',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'unit',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'price',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'remark',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
        'value' => function($dataProvider){
            return Consumables::$getStatus[$dataProvider->status];
        }
    ],
    [
        'class' => 'app\common\component\ActionTextColumn',
        'ajaxList' => [
            'update' => true, //默认开启ajax的update,delete,关闭view
            'delete' => true
        ],
        'template' => '{consumables-clinic-view}{consumables-clinic-update}{consumables-clinic-delete}',
        'buttons' => [
            'consumables-clinic-view' => function($url,$model,$key){
                if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/consumables-clinic-view', $this->params['permList'])) {
                    return false;
                }
                $options = array_merge([
                    'data-pjax' => '0',
                    'class' => 'op-group-a'
                ]);
                return Html::a('查看', $url, $options);
            },
            'consumables-clinic-update' => function ($url,$model,$key){
                if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/consumables-clinic-update', $this->params['permList'])) {
                    return false;
                }
                return Html::a('修改',$url,['data-pjax' => 0, 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large','class'=>'op-group-a']);
                },
            'consumables-clinic-delete' => function($url,$model,$key){
                if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/consumables-clinic-delete', $this->params['permList'])) {
                    return false;
                }
                $options = [
                    'data-pjax' => '0',
                    'role' => 'modal-remote',
                    'data-request-method' => 'post',
                    'data-confirm-title'=> '系统提示',
                    'data-confirm-message'=> Yii::t('yii', 'Are you sure you want to delete this item?')
                ];
                return Html::a('删除', $url, $options);
            }
        ]
    ],

];   