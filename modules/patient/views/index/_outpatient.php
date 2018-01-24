<?php
use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\patient\models\Patient;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php Pjax::begin(['id' => 'outpatient-pjax']) ?>
<div class="patient-table"></div>
<?=
    GridView::widget([
        'dataProvider' => $patient_appointment_info,
//        'options' => ['class' => 'grid-view table-responsive'],
        'tableOptions' => ['class' => 'table table-hover '],

        'layout' => '{items}<div class="text-right">{pager}</div>',
        'pager' => [
            
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
        'columns' => [

            [
                'attribute'=> 'appointmentType',
                'value'=>function($model){
                    if($model->appointmentType==1){
                        $appointmentType = '初诊';
                    }
                    if($model->appointmentType==2){
                        $appointmentType = '复诊';
                    }
                    return $appointmentType;
                },
            ],
            [
                'attribute' => 'appointmentTime',
                'value'=>function($model){
                    return date('Y-m-d H:i:s',$model->appointmentTime);
                },
            ],
            'appointmentDepartment',
            'doctorName',
            [
                'class' => 'app\common\component\ActionColumn',
                'template' => '{edit}',
                'buttons' => [
                    'edit' => function ($url, $model, $key) {
                        $options = [
                            'class' => ' icon_button_view fa fa-eye ',
                            'title'=>'查看' ,
                            'data-toggle'=>'tooltip'
                        ];
                        /*查看*/
                        return Html::a('',Url::to(['@make_appointmentAppointmentUpdate', 'id' => $model->appointmentId]), $options);
                    },
                ]
            ]
        ],
    ]);
?>
<?php Pjax::end() ?>

