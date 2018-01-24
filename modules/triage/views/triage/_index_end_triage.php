<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\patient\models\Patient;
use yii\widgets\ActiveForm;

?>

<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'grid-view add-table-padding'],
    'tableOptions' => ['class' => 'table table-hover table-border'],
    'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
    'summary'=>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
    'pager' => [
        //'options'=>['class'=>'hidden']//关闭自带分页
        'hideOnSinglePage'=>false,
        'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
        'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
        'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
        'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
    ],
    'columns' => [
//        [
//            'attribute' => 'id',
//            'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
//        ],
        [
            'attribute' => 'username',
            'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
            'contentOptions' => ['class' => 'score-warn'],
            'format' => 'raw',
            'value' => function ($searchModel) {
                $user_sex = Patient::$getSex[$searchModel->user_sex];
                $dateDiffage = Patient::dateDiffage($searchModel->birthday, time());
                $firstRecord = Patient::getFirstRecord($searchModel->firstRecord);
                $text = Html::encode($searchModel->username) . '(' . $user_sex . ' ' . $dateDiffage . ')' . Patient::getUserScore($searchModel->pain_score, $searchModel->fall_score) . $firstRecord;

                if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@patientIndexView'), $this->params['permList'])) {
                    return $text;
                }
                return Html::a($text, ['@patientIndexView', 'id' => $searchModel->patient_id], ['data-pjax' => 0, 'target' => '_blank']);
            },
        ],
        [
            'attribute' => 'department_name',
            'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
        ],
        [
            'attribute' => 'doctor_name',
            'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
        ],
        [
            'attribute' => 'temperature',
            'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
        ],
        [
            'attribute' => 'breathing',
            'headerOptions' => ['class' => 'col-sm-1'],
        ],
        [
            'attribute' => 'pulse',
            'headerOptions' => ['class' => 'col-sm-1'],
        ],
//        [
//            'attribute' => 'shrinkpressure',
//            'format' => 'raw',
//            'value' => function ($searchModel) {
//                $shrinkpressure = '收缩压' . $searchModel->shrinkpressure . '<br>';
//                $diastolic_pressure = '舒张压' . $searchModel->diastolic_pressure;
//                return $shrinkpressure . $diastolic_pressure;
//            }
//        ],
        [
            'attribute' => 'arrival_time',
            'format' => 'datetime',
            'headerOptions' => ['class' => 'col-sm-2'],
        ],
//        [
//            'attribute' => 'arrival',
//            'format' => 'raw',
//            'value' => function ($searchModel) {
//                return Html::a('查看', ['@reportRecordView', 'id' => $searchModel->patient_id], ['class' => 'btn btn-default']);
//            },
//                    'headerOptions' => ['class' => 'col-sm-1'],
//                ],
        [
            'class' => 'app\common\component\ActionColumn',
            'template' => '{user}{doctor}{room}',
            'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
            'buttons' => [
                'user' => function ($url, $model, $key) {
                    if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/info', $this->params['permList']) || $model->status ==9) {
                        return false;
                    }
                    $options = [
                        'class' => 'icon_button_css icon_infomation j-modal1',
//                                        'data-target' => '#myModal1',
                        'record_id' => $model->id,
                        'title' => '完善信息',
                        'data-toggle' => 'tooltip',
                        'data-modal-size' => 'large',
                        'role' => 'modal-remote',
                        'data-url' => Url::to(['@triageTriageModal', 'id' => $model->id])
                    ];
                    /* 完善信息按钮 */
                    return Html::a('', '#', $options);
                },
                'doctor' => function ($url, $model, $key) {
                    if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/doctor', $this->params['permList']) || $model->status ==9) {
                        return false;
                    }
                    if ($model->status >= 4) {
                        return false;
                    }
                    $btn_title = $model->doctor_chose ? '更换医生' : '选择医生';
                    $class = ' fa fa-user-md j-modal2 ';
                    $class .= $model->doctor_chose ? 'choosedStatus' : 'icon_button_view';
                    /* 选择医生和更换医生按钮 */
                    $doctor_id = $model->doctor_chose ? $model->doctor_chose : 0;
                    $appointment_doctor = $model->doctor_chose ? $model->doctor_chose : 0;
                    $options = [
                        'class' => $class,
                        'data-toggle' => 'modal',
                        'record_id' => $model->id,
                        'doctor_id' => $model->doctor_chose ? $model->doctor_chose : '',
                        'title' => $btn_title,
                        'data-toggle' => 'tooltip',
                        'role' => 'modal-remote',
                        'data-modal-size' => 'large',
                        'data-url' => Url::to(['@triageTriageDoctor', 'record_id' => $model->id, 'doctor_id' => $doctor_id, 'appointment_doctor' => $appointment_doctor])
                    ];
                    return Html::a('', '#', $options);
                },
                'room' => function ($url, $model, $key) {
                    if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/room', $this->params['permList']) || $model->status ==9) {
                        return false;
                    }
                    if ($model->status >= 4) {
                        return false;
                    }
                    $doctor_id = $model->doctor_chose ? $model->doctor_chose : 0;
                    $btn_title = $model->room_chose ? '更换诊室' : '选择诊室';
                    $class = ' glyphicon glyphicon-home j-modal3 ';
                    $class .= $model->room_chose ? 'choosedStatus' : 'icon_button_view';
                    /* 选择诊室和更换诊所按钮 */
                    $room_id = $model->room_chose ? $model->room_chose : 0;
                    $options = [
                        'class' => $class,
                        'data-toggle' => 'modal',
                        'record_id' => $model->id,
                        'room_id' => $model->room_chose ? $model->room_chose : '',
                        'title' => $btn_title,
                        'data-toggle' => 'tooltip',
                        'role' => 'modal-remote',
                        'data-modal-size' => 'normal',
                        'data-url' => Url::to(['@triageTriageRoom', 'record_id' => $model->id, 'room_id' => $room_id, 'doctor_id'=>$doctor_id])
                    ];
                    return Html::a('', '#', $options);
                },
            ]
        ],
    ],
]);
        