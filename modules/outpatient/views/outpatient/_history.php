<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\patient\models\Patient;
use app\modules\outpatient\models\Outpatient;
use app\modules\patient\models\PatientRecord;

?>

<?=GridView::widget([
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
            'format' => 'raw',
            'contentOptions' => ['class' => 'score-warn'],
            'value' => function ($searchModel) {
                //$user_sex = $searchModel->user_sex == 1 ? '男' : '女';
                $user_sex = Patient::$getSex[$searchModel->user_sex];
                $dateDiffage = Patient::dateDiffage($searchModel->birthday, time());
                $text =  Html::encode($searchModel->username) . '(' . $user_sex . ' ' . $dateDiffage . ')'
                    .Patient::getUserScore($searchModel->pain_score,$searchModel->fall_score).Patient::getFirstRecord($searchModel->first_record);
                if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@patientIndexView'), $this->params['permList'])) {
                    return $text;
                }

                if ($searchModel->patient_number == '0000000') {
                    return $text;
                }
                return Html::a($text, ['@patientIndexView', 'id' => $searchModel->patient_id], ['data-pjax' => 0, 'target' => '_blank']);
            },
        ],
        'phone',
        [
            'attribute' => 'diagnosis_time',
            'value' => function ($searchModel) {
                return $searchModel->diagnosis_time?date('Y-m-d H:i:s', $searchModel->diagnosis_time):'';
            },
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
        ],
//        [
//            'attribute' => 'record_status',
//            'value' => function ($searchModel) {
//                return Outpatient::$getStatus[$searchModel->record_status];
//            }
//        ],
        'type_description',
        'chose_room',
        [
            'class' => 'app\common\component\ActionColumn',
            'template' => '{diagnosed}{follow}',
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
            'buttons' => [
                'diagnosed' => function ($url, $model, $key) {
                    if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@make_appointmentAppointmentCreate'), $this->params['permList'])) {
                        return false;
                    }

                    $options = array_merge([
                        'title' => '预约',
//                        'data-toggle'=>'tooltip',
//                        'aria-label' => '预约',
                        'data-pjax' => '0',
//                        'class' => 'icon_button_view fa fa-phone'
                        'class' => 'op-group-a'
                    ]);
                    return Html::a('预约', Url::to(['@make_appointmentAppointmentCreate', 'patientId' => $model->patient_id,'id'=>$model->id]), $options);
                },
                'follow' => function ($url, $model, $key)use($followData) {
                    if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@followIndexCreate'), $this->params['permList']) && !in_array(Yii::getAlias('@followIndexCreate'), $this->params['permList'])) {
                        return false;
                    }
                    if($model['record_status']!=5){
                        return false;
                    }
                    if(!isset($followData[$model['id']])){//没有随访过
                        $text='新增随访';
                        $url=Url::to(['@followIndexCreate','patientId'=>$model['patient_id'],'recordId'=>$model['id']]);
                    }elseif ($followData[$model['id']]['follow_state']==1) {
                        $text='执行随访';
                        $url=Url::to(['@followIndexExecute','id'=>$followData[$model['id']]['id']]);
                    }elseif ($followData[$model['id']]['follow_state']>1) {
                        $text='查看随访';
                        $url=Url::to(['@followIndexView','id'=>$followData[$model['id']]['id']]);
                    }
                    return Html::a($text,$url, ['class' => 'op-group-a']);
                }
                    ]
                ],
            ],
        ]);

        