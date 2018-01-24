<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\patient\models\PatientRecord;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\make_appointment\models\Appointment;
/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\Appointment */

$this->title = '预约详情';
$this->params['breadcrumbs'][] = ['label' => '预约列表', 'url' => ['list','appointment[type]' => 1]];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="appointment-view col-xs-12">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
     </div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'head_img',
                        'format' => 'raw',
                        'value' => $model->head_img ? Html::img(Yii::$app->params['cdnHost'] . $model->head_img, ['onerror' => "this.src='{$baseUrl}/public/img/user/img_user_small.png'"]) : Html::img($baseUrl . '/public/img/user/img_user_small.png')
                    ],
                    [
                        'attribute' => 'userName',
                        'value' => $model->username
                    ],
                    [
                        'attribute' => 'patientNumber',
                        'value' => $model->patientNumber=='0000000'?'':$model->patientNumber
                    ],
                    [
                        'attribute' => 'sex',
                        'value' => Patient::$getSex[$model->sex]
                    ],
                    'iphone',
                    [
                        'attribute' => 'birthday',
                        'value' => $model->birthday ? (date('Y-m-d',$model->birthday).' '.(date('H:i',$model->birthday) == '00:00'?'':date('H:i',$model->birthday))) : ''
                    ],
                    [
                        'attribute' => 'age',
                        'value' => Patient::dateDiffage($model->birthday,time())
                    ],
                    [
                        'attribute' => 'appointment_origin',
                        'value' => Appointment::$getAppointmentOrigin[$model->appointment_origin]
                    ],
                    [
                        'attribute' => 'patient_source',
                        'value' => Patient::$getPatientSource[$model->patient_source]
                    ],
                    [
                        'attribute' => 'type_description',
                    ],

//             'patient_id',
//             'record_id',
//             'department',


                    'doctorName',
                    [
                        'attribute' => 'appointment_operator',
                        'value' => User::getUserInfo($model->appointment_operator,['username'])['username']
                    ],
                    'time:datetime',
                    'create_time:datetime',
                    'illness_description:ntext',
                    'remarks:ntext',
                    [
                        'attribute' => 'appointment_cancel_operator',
                        'value' => function ($model) {
                            if ($model->cancel_online  == 1) {
                                return '线上取消';
                            }else{
                                return  User::getUserInfo($model->appointment_cancel_operator,['username'])['username'];
                            }
                        },
                        'visible' => $model->status == 7 || $model->status == 8?true:false

                    ],
                    [
                        'attribute' => 'appointment_cancel_reason',
                        'value'=> $model->appointment_cancel_reason,
                        'visible' => $model->status == 7  || $model->status == 8?true:false
                    ],


                ],
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
