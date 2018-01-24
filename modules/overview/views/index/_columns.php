<?php
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;
use \app\modules\spot\models\Spot;
use  \app\modules\overview\models\Overview;
return [
    [
        'class'=>'\kartik\grid\ExpandRowColumn',
        'defaultHeaderState' => 0,
        'enableRowClick' => true,
        'collapseIcon' => '<i class="fa fa-minus btn-box-tool"></i>',
        'expandIcon' => '<i class="fa fa-plus btn-box-tool"></i>',
        'detailUrl' => Url::to(['@overviewIndexDetail']),
        'value' => function ($model, $key, $index) {
            return GridView::ROW_EXPANDED;
        }

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'headerOptions'=> ['class'=>'col-sm-4 col-md-4 col-xs-4'],
        'contentOptions'=> ['class' => 'spot_num_font'],
        'attribute'=>'spot_name',
        'format' => 'raw',
        'value' => function($dataProvider){
            return Html::encode(Overview::getSpotName($dataProvider->id)['spot_name']).Html::tag('span','(代码:'.Overview::getSpotCode($dataProvider->id)['spot'].')',['class'=>'spot_code']);
        },

    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'contentOptions'=> ['class' => 'spot_num'],
        'attribute'=>'spot_num',
        'value' => function($dataProvider){
            return Overview::getTotal($dataProvider->id);
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'contact_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'contact_iphone',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'create_time',
        'format'=>'date'
    ],
    [
        'class' => 'app\common\component\ActionColumn',
        'template' => '{view}',
        'headerOptions' => ['class' => 'col-sm-2 col-md-2 '],
        'buttons' => [
            'view' => function ($url, $model, $key) {
                $options = [
                    'class' => ' icon_button_view fa fa-eye ',
                    'title'=>'查看' ,
                    'data-toggle'=>'tooltip',
                    'data-pjax' => 0
                ];
                /*查看*/
                return Html::a('',Url::to(['@spotOrganizationView', 'id' => $key]), $options);
            },
        ],


    ],

];   