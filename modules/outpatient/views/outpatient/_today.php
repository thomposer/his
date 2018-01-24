<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\patient\models\Patient;
use app\modules\outpatient\models\Outpatient;
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
        'hideOnSinglePage' => false,
        'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
        'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
        'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
        'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
    ],
    /* 'filterModel' => $searchModel, */
    'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'username',
            'label' => '患者信息',
            'format' => 'raw',
            'contentOptions' => ['class' => 'score-warn'],
            'value' => function ($searchModel) {
                $user_sex = Patient::$getSex[$searchModel['user_sex']];
                $dateDiffage = Patient::dateDiffage($searchModel['birthday'], time());
                $text =  Html::encode($searchModel['username']) . '(' . $user_sex . ' ' . $dateDiffage . ')'
                . Patient::getUserScore($searchModel['pain_score'], $searchModel['fall_score']) . Patient::getFirstRecord($searchModel['first_record']);
                if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@patientIndexView'), $this->params['permList'])) {
                    return $text;
                }

                if ($searchModel['patient_number']== '0000000') {
                    return $text;
                }
                return Html::a($text, ['@patientIndexView', 'id' => $searchModel['patient_id']], ['data-pjax' => 0, 'target' => '_blank']);
            },
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
        ],
        [
            'attribute' => 'phone',
            'label' => '手机号',
            'value' => function ($searchModel) {
                return $searchModel['phone'];
            },
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
        ],
        [
            'attribute' => 'reg_time',
            'label' => '预约时间',
            'value' => function ($searchModel) {
                return $searchModel['reg_time'] ? date('Y-m-d H:i', $searchModel['reg_time']) : '';
            },
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
        ],
        [
            'attribute' => 'diagnosis_time:date',
            'label' => '接诊时间',
            'value' => function ($searchModel) {
                return $searchModel['diagnosis_time'] ? date('Y-m-d H:i', $searchModel['diagnosis_time']) : '';
            },
        ],
        [
            'attribute' => 'second_department',
            'label' => '科室',
            'value' => function ($searchModel) {
                return $searchModel['second_department'];
            },
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
        ],
//         'second_department',
        [
            'attribute' => 'reg_time',
            'label' => '服务类型',
            'value' => function ($searchModel) {
                return $searchModel['type_description'];
            },
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
        ],
        [
            'attribute' => 'chose_room',
            'label' => '诊室',
            'value' => function ($searchModel) {
                return Html::encode($searchModel['chose_room']);
            },
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
        ],
        [
            'attribute' => 'chose_room',
            'label' => '报告(已出|待出)',
            'value' => function ($searchModel) {
                $pendingReport = Yii::$app->cache->get(Yii::getAlias('@pendingReportNum') . $searchModel['spot_id'] . '_' . $searchModel['id']); //待出报告 
                $madeReport = Yii::$app->cache->get(Yii::getAlias('@madeReportNum') . $searchModel['spot_id'] . '_' . $searchModel['id']); //已出报告 
                if ($pendingReport || $madeReport) {
                    $text = ($madeReport?$madeReport:0).' | '.($pendingReport?$pendingReport:0) ;
                } else {
                    $text = '';
                }
                return $text;
            },
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
        ],
        [
            'attribute' => 'record_status',
            'format' => 'raw',
            'label' => '状态',
            'value' => function ($searchModel) {
                $isTexu = $searchModel['triage_time'] < mktime(0, 0, 0, date("m"), date("d"), date("Y")) ? true : false;
                $text = Outpatient::$getStatus[$searchModel['record_status']];
                $texu = '';
                if ($isTexu && in_array($searchModel['record_status'], [3, 4, 5])) {
                    $texu = '<span class="text-red-mine"> (特需) </span>';
                }
                return $text . $texu;
            }
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{diagnosis}{follow}',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                    'buttons' => [
                        'diagnosis' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/diagnosis', $this->params['permList']) && !in_array($this->params['requestModuleController'] . '/update', $this->params['permList'])) {
                                return false;
                            }
                            $isTexu = $model['triage_time'] < mktime(0, 0, 0, date("m"), date("d"), date("Y")) ? true : false;
//                            $options = array_merge([
//                                'title' => '接诊',
//                                'data-toggle' => 'tooltip',
//                                'aria-label' => '接诊',
//                                'data-pjax' => '0',
//                                'class' => $isTexu ? 'icon_button_view_red fa fa-stethoscope' : 'icon_button_view fa fa-stethoscope'
//                            ]);
                            $options = ['class' => 'op-group-a', 'data-pjax' => 0];
                            $text = '接诊';
                            if (in_array($model['record_status'], [3, 4, 5])) {
//                                $options = array_merge([
//                                    'title' => '接诊',
//                                    'data-toggle' => 'tooltip',
//                                    'aria-label' => '接诊',
//                                    'data-pjax' => '0',
//                                    'class' => $isTexu ? 'icon_button_view_red fa fa-stethoscope' : 'icon_button_view fa fa-stethoscope'
//                                ]);
                                $options = ['class' => 'op-group-a', 'data-pjax' => 0];
                                $text = '接诊';
                            } else if (in_array($model['record_status'], [1, 2])) {
//                                $options = array_merge([
//                                    'title' => '查看',
//                                    'data-toggle' => 'tooltip',
//                                    'aria-label' => '查看',
//                                    'data-pjax' => '0',
//                                    'class' => 'icon_button_view fa fa-eye'
//                                ]);
                                $options = ['class' => 'op-group-a', 'data-pjax' => 0];
                                $text = '查看';
                            }
                            if ($model['record_status'] == 3) { //跳转接诊  更改状态
                                $url = Url::to(['@outpatientOutpatientDiagnosis', 'id' => $model['id']]);
                            } else if ($model['record_status'] == 1) {
                                $url = Url::to(['@make_appointmentAppointmentView', 'id' => $model['appointment_id']]);
                            } else if ($model['record_status'] == 2) {
                                $url = Url::to(['@reportRecordView', 'id' => $model['id']]);
                            } else {//已接诊  跳转至病例TODO
                                $url = Url::to(['@outpatientOutpatientUpdate', 'id' => $model['id']]);
                            }
                            return Html::a($text, $url, $options);
                        },
                        'follow' => function ($url, $model, $key)use($followData) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@followIndexCreate'), $this->params['permList'])) {
                                return false;
                            }
                            if ($model['record_status'] != 5) {
                                return false;
                            }
                            if (!isset($followData[$model['id']])) {//没有随访过
                                $text = '新增随访';
                                $url = Url::to(['@followIndexCreate', 'patientId' => $model['patient_id'], 'recordId' => $model['id']]);
                            } elseif ($followData[$model['id']]['follow_state'] == 1) {
                                $text = '执行随访';
                                $url = Url::to(['@followIndexExecute', 'id' => $followData[$model['id']]['id']]);
                            } elseif ($followData[$model['id']]['follow_state'] > 1) {
                                $text = '查看随访';
                                $url = Url::to(['@followIndexView', 'id' => $followData[$model['id']]['id']]);
                            }
                            return  Html::a($text, $url, ['class' => 'op-group-a', 'data-pjax' => 0]);
                        }
                            ]
                        ],
                    ],
                ]);

                