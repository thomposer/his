<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/25
 * Time: 10:24
 */
use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\patient\models\Patient;
use app\common\Common;
use yii\helpers\Url;
use yii\widgets\Pjax;

?>
<div class="box">
    <div class="nurse-appointment ">
        预约未到店 <span class="appointment-num"><?= $appointmentNum; ?> </span>人
        （ <?= $date . ' ' . Common::getWeekDay($date) ?> ）
    </div>
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <?=
    GridView::widget([
        'dataProvider' => $appointmentDataProvider,
        'options' => ['class' => 'grid-view table-responsive '],
        'tableOptions' => ['class' => 'table table-hover table-border', 'id' => 'nurse-appointment'],
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
                    'value' => function ($dataProvider) use ($appointmentCardInfo) {
                        $user_sex = Patient::$getSex[$dataProvider->sex];
                        $dateDiffage = Patient::dateDiffage($dataProvider->birthday, time());
                        $text = Html::encode($dataProvider->username) . '(' . $user_sex . ' ' . $dateDiffage . ')' . Patient::getUserScore($dataProvider->pain_score, $dataProvider->fall_score) . Patient::getUserVipInfo($appointmentCardInfo[$dataProvider->iphone]) . Patient::getFirstRecord($dataProvider->first_record);
                        if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@patientIndexView'), $this->params['permList'])) {
                            return $text;
                        }

                        if($dataProvider->patient_number == '0000000'){
                            return $text;
                        }

                        return Html::a($text, ['@patientIndexView', 'id' => $dataProvider->patientId], ['data-pjax' => 0, 'target' => '_blank']);
                    },
                ],
                'iphone',
                [
                    'attribute' => 'time',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'value' => function ($dataProvider) {
                        return date('H:i', $dataProvider->time);
                    },
                ],
               /* 'appointmentDepartment',*/
                'doctorName',
                'appointmentType',
                'illnessDescription',

                [
                    'attribute' => 'remarks',
                    'contentOptions' => ['class' => 'col-sm-2 col-md-2 appointment-remark'],
                    'value' => function ($dataProvider) {
                        return $dataProvider->remarks;
                    },
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{view}{update}{delete}{report}',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'buttons' => [

                        'view' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentView'), $this->params['permList'])) {
                                return false;
                            }
                            return Html::a('查看', ['@make_appointmentAppointmentView', 'id' => $model->appointmentId], ['target' => '_blank', 'class' => 'op-group-a', 'data-pjax' => 0]) . '<span style="color:#99a3b1">丨</span>';
                        },
                        'update' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentUpdate'), $this->params['permList'])) {
                                return false;
                            }
                            if (($model->status != 1 || ($model->status == 1 && strtotime(date("Y-m-d", $model->time)) + 86400 <= strtotime(date('Y-m-d'))))) {
                                $class = 'op-group-a-disable';
                                $url = 'javascript:void(0)';
                            } else {
                                $class = 'op-group-a';
                                $url = ['@make_appointmentAppointmentUpdate', 'id' => $model->appointmentId];
                            }

                            return Html::a('修改', $url, ['class' => $class, 'target' => '_blank', 'data-pjax' => 0]) . '<span style="color:#99a3b1">丨</span>';
                        },
                        'delete' => function ($url, $model, $key) {

                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentDelete'), $this->params['permList'])) {
                                return false;
                            }
                            $options = '';
                            if (($model->status != 1 || ($model->status == 1 && strtotime(date("Y-m-d", $model->time)) + 86400 <= strtotime(date('Y-m-d'))))) {
                                $class = 'op-group-a-disable';
                                $url = 'javascript:void(0)';
                            } else {
                                $class = 'op-group-a';
                                $url = ['@make_appointmentAppointmentDelete', 'id' => $model->record_id, 'entrance' => 2];
                                $options = [
                                    'data-request-method' => 'post',
                                    'role' => 'modal-remote',
                                    'data-modal-size' => 'normal'
                                ];
                            }
                            $options['class'] = $class;


                            return Html::a('关闭', $url, $options) . '<span style="color:#99a3b1">丨</span>';
                        },
                        'report' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@reportRecordUpdate'), $this->params['permList'])) {
                                return false;
                            }
                            if ($model->status == 1 && strtotime(date("Y-m-d", $model->time)) == strtotime(date('Y-m-d'))) {
                                $class = 'op-group-a report-confirm';
                                $url = ['@reportRecordUpdate', 'id' => $model->record_id];
                            } else {
                                $class = 'op-group-a-disable';
                                $url = 'javascript:void(0)';
                            }
                            return Html::a('一键报到',
                                '#',
                                ['class' => $class,'data-pjax' => 0,
                                'id'=>$model->record_id,
                                'actionUrl'=>  Url::to(['@reportRecordUpdate','id'=>$model->record_id]),
                                'data-url'=>  Url::to(['@reportRecordConfirmReport']),
                                'contentType' => 'application/x-www-form-urlencoded','data-request-method'=>'post','processData'=>1, 'data-modal-size' => 'normal'
                                ]);
                        },
                    ],
                ],

            ],

        ]);
    ?>
    <?php Pjax::end(); ?>
</div>