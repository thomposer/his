<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\common\Common;
use app\modules\patient\models\PatientRecord;
?>
<div class="appointment-index">

    <?php Pjax ::begin(['id' => 'appointment-crud-datatable-pjax', 'timeout' => 5000,'enablePushState' => false]) ?>
    <?=
    GridView ::widget([
        'dataProvider' => $appointmentDataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border appointment-table'],
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
        'columns' => [
            [
                'attribute' => 'doctorName',
                'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
            ],
            [
                'attribute' => 'type_description',
                'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
            ],
            [
                'attribute' => 'time',
                'value' => function($searchModel) {
                    return date("Y-m-d H:i", $searchModel->time);
                },
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
            ],
                [
                    'attribute' => 'spot_name',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                ],
            [
                'attribute' => 'illness_description',
                'value' => function($searchModel) {
                    return Common::strTransfer($searchModel->illness_description, 16);
                },
//                         'visible' => ($entrance == 2) ? 1 : 0,
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
            ],
            [
                'attribute' => 'remarks',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
            ],
            [
                'attribute' => 'status',
                'value' => function ($searchModel) {
                    if ($searchModel->status == 1 && strtotime(date("Y-m-d", $searchModel->time)) + 86400 <= strtotime(date('Y-m-d'))) {
                        return PatientRecord::$getStatus[8];
                    }
                    return PatientRecord::$getStatus[$searchModel->status];
                },
                'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
            ],
            [
                'attribute' => 'appointmentOperator',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2','style' => 'width:100px;'],
            ],

            [
                'class' => 'app\common\component\ActionTextColumn',
                'template' => '{view}',
                'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                'buttons' => [
                    'view' => function ($url, $model, $key)use($spotId) {
                        
                        if ((!isset($this -> params['permList']['role']) && !in_array(Yii ::getAlias('@make_appointmentAppointmentView'), $this -> params['permList'])) || $model->spot_id != $spotId) {
                            return false;
                        }
                        return Html::a('查看',['@make_appointmentAppointmentView','id'=>$model->id],['target' => '_blank','data-pjax' => 0]);
                    },
                ],
            ],
        ],
    ]);
    ?>
    <?php Pjax ::end() ?>
</div>
