<?php
use yii\grid\GridView;
use app\modules\patient\models\Patient;
use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\patient\models\PatientRecord;

?>
<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'grid-view table-responsive add-table-padding'],
    'tableOptions' => ['class' => 'table table-hover table-border'],
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
        [
            'attribute' => 'patient_info',
            'headerOptions' => ['class' => 'col-sm-3 col-md-3'],
            'format' => 'raw',
            'value' => function ($searchModel) {
//                        $user_sex = $searchModel->sex == 1 ? '男' : '女';
                $user_sex = Patient::$getSex[$searchModel->sex];
                $dateDiffage = Patient::dateDiffage($searchModel->birthday, time());
                return Html::encode($searchModel->username). '(' . $user_sex . ' ' . $dateDiffage . ')'.Patient::getFirstRecord($searchModel->first_record);
            },
        ],
        [
            'attribute' => 'patient_number',
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
            'format' => 'raw',
        ],
        [
            'attribute' => 'birthday',
            'value' => function ($searchModel) {
                if ($searchModel->birthday != 0) {
                    return date('Y-m-d', $searchModel->birthday);
                }
                return '';
            },
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
        ],
        'iphone',
        [
            'attribute' => 'record_count',
            'format' => 'raw',
            'value' => function ($searchModel)use($recordNum){
                $count = $recordNum[$searchModel->id]['num'];
                if($count){
                    $len = strlen($count);
                    $len == 1 ? $padding = 45 : $padding = 45 - ($len - 1) * 8;
                    $options = [
                        'data-toggle' => 'tooltip',
                        'data-pjax' => 0,
                        'class' =>'op-group-a',
                        'style' => "padding-left:".$padding."px;",
                        'target' => "_blank",
                    ];
                    return Html::a($count, Url::to(['@patientIndexView', 'id' => $searchModel->id, '#' => 'treatment']), $options);
                }
                return '<span style="padding-left:45px;">0</span>';
            },
        ],
        [
            'class' => 'app\common\component\ActionColumn',
            'template' => '{view}{print}{appointment}{makeup}',
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
            'buttons' => [
//                     'add' => function ($url, $model, $key) {
//                         if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentCreate'), $this->params['permList'])) {
//                            return false;
//                         }
//                         $options = array_merge([
//                             'title' => '新增医嘱',
//                             'aria-label' => '新增医嘱',
//                             'data-toggle'=>'tooltip',
//                             'class' => 'icon_button_view fa fa-plus'
//                         ]);
//                         return Html::a('','#', $options);
//                     },

                'appointment' => function ($url, $searchModel, $key) {
                    if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentCreate'), $this->params['permList'])) {
                        return false;
                    }
                    $options = array_merge([
                        'title' => '预约',
                        'aria-label' => '预约',
                        'data-toggle' => 'tooltip',
                        'data-pjax' => 0,
                        'class' => 'icon_button_view  fa fa-phone'
                    ]);
                    return Html::a('', Url::to(['@make_appointmentAppointmentCreate', 'patientId' => $searchModel->id]), $options);
                },
                'view' => function ($url, $searchModel, $key) {
                    //                     if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentCreate'), $this->params['permList'])) {
                    //                         return false;
                    //                     }
                    $options = array_merge([
                        'title' => '查看',
                        'aria-label' => '查看',
                        'data-toggle' => 'tooltip',
                        'data-pjax' => 0,
                        'class' => 'icon_button_view fa fa-eye'
                    ]);

                    return Html::a('', Url::to(['@patientIndexView', 'id' => $searchModel->id]), $options);  // Url::to(['@make_appointmentAppointmentCreate', 'patientId' => $model->patient_id])
                },
                'makeup' => function ($url, $searchModel, $key)use($makeUpData) {
                    if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@patientIndexMakeup'), $this->params['permList'])) {
                        return false;
                    }
//                    if(PatientRecord::getMakeup($searchModel->id)==null){
//                        return false;
//                    }
                    if(!isset($makeUpData[$searchModel->id])){
                        return false;
                    }
                    $options = array_merge([
                        'title' => '补录',
                        'aria-label' => '补录',
                        'data-toggle' => 'tooltip',
                        'data-pjax' => 0,
                        'class' => 'icon_button_view fa fa-makeup'
                    ]);

                    return Html::a('', Url::to(['@patientIndexMakeup', 'patientId' => $searchModel->id, '#' => 'treatment']), $options);  // Url::to(['@make_appointmentAppointmentCreate', 'patientId' => $model->patient_id])
                },
                'print' => function($url, $model, $key) {
                    $options = [
                        'style' => 'display: inline-block;',
                        'user_name' => $model->username,
                        'sex' => $model->sex,
                        'phone' => $model->iphone,
                        'birthday' => date('Y-m-d', $model->birthday),
                        'patient_number' => $model->patient_number
                    ];
                    return Html::a('<span class="icon_button_view fa fa-print print_label"  title="打印标签", data-toggle="tooltip"></span>', 'javascript:void(0)', $options);
                }
            ]
        ]
    ],
]);
?>