<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/25
 * Time: 10:30
 */
use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use app\common\Common;
use yii\helpers\Url;
?>
<div id="crud-datatable-pjax">
    <div class="box">
        <div class="nurse-appointment ">
            已到店 <span class="appointment-num"><?= $reportNum; ?></span> 人
            （ <?= $date . ' ' . Common::getWeekDay($date) ?> ）
        </div>
        <?=
        GridView::widget([
            'dataProvider' => $reportDataProvider,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => ['class' => 'table table-hover table-border', 'id' => 'nurse-report'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                'options' => ['class' => 'hidden'],//关闭自带分页
            ],
            'columns' => [
                [
                    'attribute' => 'patient_info',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'contentOptions' => ['class' => 'score-warn'],
                    'value' => function ($dataProvider) use ($reportCardInfo) {
                        $user_sex = Patient::$getSex[$dataProvider->sex];
                        $dateDiffage = Patient::dateDiffage($dataProvider->birthday, time());
                        $text = Html::encode($dataProvider->username) . '(' . $user_sex . ' ' . $dateDiffage . ')' . Patient::getUserScore($dataProvider->pain_score, $dataProvider->fall_score) . Patient::getUserVipInfo($reportCardInfo[$dataProvider->iphone]) . Patient::getFirstRecord($dataProvider->first_record);
                        if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@patientIndexView'), $this->params['permList'])) {
                            return $text;
                        }

                        if ($dataProvider->patient_number == '0000000') {
                            return $text;
                        }

                        return Html::a($text, ['@patientIndexView', 'id' => $dataProvider->patientId], ['data-pjax' => 0, 'target' => '_blank']);
                    },
                ],
                'patient_number',
                'iphone',
                [
                    'attribute' => 'time',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'value' => function ($dataProvider) {
                        if ($dataProvider->time) {
                            return date('H:i', $dataProvider->time);
                        }
                        return '';
                    },
                ],
                [
                    'attribute' => 'reportTime',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'value' => function ($dataProvider) {
                        return date('H:i', $dataProvider->reportTime);
                    },
                ],
                'reportDepartment',
                'reportDoctor',
                'reportType',
                [
                    'attribute' => 'status',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'value' => function ($dataProvider) {
//                                    if($dataProvider->status == 1 && strtotime(date("Y-m-d",$dataProvider->time))+86400 <= strtotime(date('Y-m-d')) ){
//                                        return PatientRecord::$getStatus[6];
//                                    }
                        return PatientRecord::$getStatus[$dataProvider->status];
                    },
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{print}{view}{user}{doctor}{room}{orders}{record-print}',
                    'headerOptions' => ['class' => 'col-sm-3 col-md-3'],
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            if ((!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@reportRecordView'), $this->params['permList']))) {
                                return false;
                            }
                            $options = array_merge([
                                'data-pjax' => '0',
                                'target' => '_blank',
                                'class' => ' op-group-a',
                            ]);
                            return Html::a('查看', ['@reportRecordView', 'id' => $model->record_id], $options) . '<span style="color:#99a3b1">丨</span>';
                        },
                        'doctor' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@triageTriageDoctor'), $this->params['permList'])) {
                                return false;
                            }
                            $class = $model->status <= 3 ? 'op-group-a' : 'op-group-a-disable';
                            $btn_title = $model->doctor_chose ? '换医生' : '选医生';
                            /* 选择医生和更换医生按钮 */
                            $doctor_id = $model->doctor_chose ? $model->doctor_chose : 0;
                            $appointment_doctor = $model->doctor_chose ? $model->doctor_chose : 0;
                            $options = [
                                'class' => $class,
                                'data-toggle' => 'modal',
                                'record_id' => $model->record_id,
                                'doctor_id' => $model->doctor_chose ? $model->doctor_chose : '',
                                'title' => $btn_title,
                                'data-modal-size' => 'large',
                                'data-url' => Url::to(['@triageTriageDoctor', 'record_id' => $model->record_id, 'doctor_id' => $doctor_id, 'appointment_doctor' => $appointment_doctor])
                            ];
                            $model->status <= 3 && $options['role'] = 'modal-remote';
                            return Html::a($btn_title, '#', $options) . '<span style="color:#99a3b1">丨</span>';
                            //                            return Html::a('', 'javascript:void(0)', $options);
                        },
                        'room' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@triageTriageRoom'), $this->params['permList'])) {
                                return false;
                            }
                            $btn_title = $model->room_chose ? '换诊室' : '选诊室';
                            /* 选择诊室和更换诊所按钮 */
                            $class = $model->status <= 3 ? 'op-group-a' : 'op-group-a-disable';
                            $room_id = $model->room_chose ? $model->room_chose : 0;
                            $options = [
                                'class' => $class,
                                'data-toggle' => 'modal',
                                'record_id' => $model->record_id,
                                'room_id' => $model->room_chose ? $model->room_chose : '',
                                'doctor_id' => $model->doctor_chose,
                                'title' => $btn_title,
                                'data-modal-size' => 'normal',
                                'data-url' => Url::to(['@triageTriageRoom', 'doctor_id' => $model->doctor_chose,'record_id' => $model->record_id, 'room_id' => $room_id])
                            ];
//                            var_dump($options);
                            $model->status <= 3 && $options['role'] = 'modal-remote';
                            return Html::a($btn_title, '#', $options) . '<span style="color:#99a3b1">丨</span>';
                            //                    return Html::a('', '#', $options);
                        },
                        'user' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@triageTriageModal'), $this->params['permList'])) {
                                return false;
                            }
                            $options = [
                                'record_id' => $model->record_id,
                                'role' => 'modal-remote',
                                'data-modal-size' => 'large',
                                'class' => ' op-group-a',
                                'data-pjax' => '0',
                                'data-url' => Url::to(['@triageTriageModal', 'id' => $model->record_id])
                            ];
                            /* 完善信息按钮 */
                            return Html::a('完善信息', '#', $options) . '<span style="color:#99a3b1">丨</span>';
                        },
                        'orders' => function ($url, $model, $key) use ($patientOrders) {
                            if ($patientOrders[$model->record_id]) {
                                $options = [
                                    'class' => 'op-group-a',
                                    'record_id' => $model->record_id,
                                    'data-modal-size' => 'normal',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['@apiAppointmentWorkstationGetOrdersData', 'id' => $model->record_id])
                                ];
                                $patientOrders[$model->record_id] && $options['role'] = 'modal-remote';
                                return Html::a('医嘱', '#', $options) . '<span style="color:#99a3b1">丨</span>';
                            } else {
                                return '<span class="op-group-a-disable">医嘱</span>' . '<span style="color:#99a3b1">丨</span>';
                            }
                        },
                        'print' => function ($url, $model, $key) {
                            $options = [
                                'style' => 'display: inline-block;',
                                'user_name' => $model->username,
                                'sex' => $model->sex,
                                'phone' => $model->iphone,
                                'birthday' => date('Y-m-d', $model->birthday),
                                'patient_number' => $model->patient_number,
                                'class' => ' op-group-a',
                            ];
                            return Html::a('<span class=" print_label">标签</span>', 'javascript:void(0)', $options) . '<span style="color:#99a3b1">丨</span>';
                        },
                        'record-print' => function ($url, $model, $key) {
                            if(5 == $model->status){
                                $options = [
                                    'style' => 'display: inline-block;',
                                    'record-id' => $model->record_id,
                                    'class' => ' op-group-a',
                                ];
                                if($model->record_type == 2){
                                    return Html::a('<span class="btn-nurse-list-print" record-id="'.$model->record_id.'">打印报告</span>', 'javascript:void(0)', $options);
                                }else{
                                    return Html::a('<span class="record-print" record-id="'.$model->record_id.'">打印病历</span>', 'javascript:void(0)', $options);
                                }
                            }else{
                                if($model->record_type == 2){
                                    return '<span class="op-group-a-disable">打印报告</span>';
                                }else{
                                    return '<span class="op-group-a-disable">打印病历</span>';
                                }
                            }
                            
                        }
                    ],
                ],
            ],
        ]);
        ?>
    </div>
</div>
