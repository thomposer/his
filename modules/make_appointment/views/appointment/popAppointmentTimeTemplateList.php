<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use yii\helpers\Url;


use app\modules\patient\models\PatientRecord;
use app\modules\patient\models\Patient;
use app\common\Common;
use app\modules\make_appointment\models\Appointment;
use app\modules\user\models\User;

CrudAsset::register($this);
$baseUrl = Yii::$app->request->baseUrl;
?>

<?= GridView::widget([

    'dataProvider' => $dataProvider,
    'options' => ['class' => 'grid-view table-responsive add-table-padding'],
    'tableOptions' => ['class' => 'table table-hover table-border header'],
    'layout'=> '{items}<div class="text-right">{pager}</div>',
    'pager'=>[
        'options'=>['class'=>'hidden'],//关闭自带分页
        'firstPageLabel'=> Yii::getAlias('@firstPageLabel'),
        'prevPageLabel'=> Yii::getAlias('@prevPageLabel'),
        'nextPageLabel'=> Yii::getAlias('@nextPageLabel'),
        'lastPageLabel'=> Yii::getAlias('@lastPageLabel'),
    ],
    'columns' => [
        [
            'attribute' => 'name',
            'headerOptions' => ['class' => 'col-sm-3 col-md-3'],
        ],
        [
            'attribute' => 'appointment_times',
            'value'=>  function ($model){
                return str_replace(',','，',$model->appointment_times);
            },
        ],

        [
            'class' => 'app\common\component\ActionColumn',
            'template' => '{use-template}',
            'headerOptions' => ['style' =>'width:90px'],
            'buttons' => [
                'use-template' => function ($url, $model, $key)use($doctorID,$date){
                    $options = array_merge([
                        'title' => '使用模板',
                        'target' => '_blank',
                        'class' => 'appointment-use-template',
                        'doctor_id' => $doctorID,
                        'date' => $date,
                        'appointment_times' => $model->appointment_times,
                    ]);
                    return Html::a('使用模板','javascript:void(0)',$options);
                },
            ]
        ],
    ],
]); ?>

<?php $this->beginBlock('renderJs');?>
<?php $this->endBlock();?>
