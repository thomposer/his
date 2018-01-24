<?php
use yii\helpers\Url;
use yii\helpers\Html;

return [
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'id',
        'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1']
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'chinese_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'english_name',
    ],
//     [
//         'class'=>'\kartik\grid\DataColumn',
//         'attribute'=>'create_time',
//         'format' => 'datetime'
//     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'update_time',
    // ],
    [
        'class' => 'app\common\component\ActionColumn',
        'template' => '{update}{delete}',
        'ajaxList' => [
            'update' => true, //默认开启ajax的update,delete,关闭view
            'delete' => true
        ],
        'buttons' => [
            'update' => function($url,$model,$key){
                if(!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/update', $this->params['permList'])){
                    return false;
                }
                $options = [
                    'role'=>'modal-remote',
                    'data-toggle'=>'tooltip',
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                    'data-modal-size' => 'large'
                ];
                return Html::a('<span class="icon_button_view fa fa-pencil-square-o" title="修改", data-toggle="tooltip"></span>', $url, $options);
            }  
        ],
    ],

];   