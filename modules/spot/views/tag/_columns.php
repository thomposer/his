<?php
use yii\helpers\Url;
use yii\helpers\Html;

return [
    [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'id',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'description',
    ],
    [
        'class' => 'app\common\component\ActionColumn',
        'template' => '{view}{delete}',
        'buttons' => [
            'delete' => function ($url, $dataProvider, $key) {
                if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/delete', $this->params['permList'])) {
                    return false;
                }
                $options = [
                    'class' => 'icon_button_view fa fa-trash-o',
                    'role'=>"modal-remote",
                    'data-toggle'=>"tooltip",
                    'data-confirm-title'=>"系统提示",
                    'data-original-title'=>"删除",
                    'data-confirm-message' => '确定要删除该标签吗？同时将删除与标签相关的其他配置',
                    'data-pjax' => '1',
                    'data-request-method' => 'post',
                ];
                return Html::a('', Url::to(['@spotTagDelete', 'id' => $dataProvider['id']]), $options);
            },
        ],
        'ajaxList' => [
            'update' => true, //默认开启ajax的update,delete,关闭view
            'delete' => true
        ],
    ],

];   