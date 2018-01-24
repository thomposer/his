<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\modules\charge\models\ChargeRecordLog;
use yii\helpers\Url;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\charge\models\ChargeRecord;
?>


<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'grid-view table-responsive add-table-padding'],
    'tableOptions' => ['class' => 'table table-hover table-border header'],
    'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
    'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
    'pager' => [
        //'options'=>['class'=>'hidden']//关闭自带分页
        'hideOnSinglePage' => false,//在只有一页时也显示分页
        'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
        'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
        'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
        'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
    ],
    /* 'filterModel' => $searchModel, */
    'columns' => [
        'username',
        [
            'attribute' => 'sex',
            'headerOptions' => ['class' => 'table-sex-width'],
            'value' => function ($model) {
                return Patient::$getSex[$model->sex];
            }
        ],
        'age',
        [
            'attribute' => 'diagnosis_time',
//             'headerOptions' => ['class' => 'table-time-width'],
            'value' => function ($model) {
                return $model->diagnosis_time?date('Y-m-d H:i', $model->diagnosis_time):'--';
            }
        ],
        [
            'attribute' => 'doctor_name',
            'value' => function ($model) {
                return $model->doctor_name?$model->doctor_name : '--';
            }
        ],
        [
            'attribute' => 'type_description',
            'value' => function ($model) {
                return $model->type_description?$model->type_description : '--';
            }
        ],
        [
            'attribute' => 'create_time',
            'headerOptions' => ['class' => 'table-time-width'],
            'value' => function ($model) {
                return date('Y-m-d', $model->create_time);
            }
        ],
        [
            'attribute' => 'type',
            'value' => function ($model) {
                return ChargeRecordLog::$getType[$model->type];
            }
        ],
        [
            'attribute' => 'pay_type',
            'value' => function($model){
                return ChargeRecord::$getType[$model->pay_type];
            }
        ],
        [
            'attribute' => 'price',
            'value'=>function($model){
                if($model->price != '0.00'){
                    $price = $model->type == 1?'+'.$model->price:'<span style="color:red;">-'.$model->price.'</span>';
                }else{
                    $price = $model->price;
                }
                return $price;
            },
            'format'=>'raw'
        ],
        [
            'class' => 'app\common\component\ActionColumn',
            'template' => "{view}{print}",
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@chargeIndexTradeLog'), $this->params['permList'])) {
                        return false;
                    }

                    $options = array_merge([
                        'data-pjax' => '0',
                        'target' => '_blank',
                        'class' => ' op-group-a',
                    ]);
                    return Html::a('查看', ['@chargeIndexTradeLog', 'id' => $model->id], $options) . '<span style="color:#99a3b1">丨</span>';
                },

                'print' => function($url, $model, $key) {
                    $options = [
                        'style' => 'display: inline-block;',
//                        'id' => $model->id,
                        'class' => ' op-group-a',
                        'data-pjax' => '0',
                        'role' => 'modal-remote',
                        'data-modal-size' => 'middle',
                    ];
                    return Html::a('打印', ['@apiChargePrintList', 'id' => $model->id], $options);
                }

            ]
        ],
    ],
]);
?>

