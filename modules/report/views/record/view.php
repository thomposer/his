<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\patient\models\Patient;
use app\modules\make_appointment\models\Appointment;
use app\modules\patient\models\PatientSubmeter;
use app\modules\patient\models\PatientRecord;
use app\modules\spot_set\models\SecondDepartment;
/* @var $this yii\web\View */
/* @var $model app\modules\patient\models\Patient */

$this->title = '报到详情';
$this->params['breadcrumbs'][] = ['label' => '报到', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$attribute = $model->getModel('patient')->attributeLabels();
$reportattribute = $model->getModel('report')->attributeLabels();
$attributeLabels = $model->getModel('patientSubmeter')->attributeLabels();
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="patient-view col-xs-12">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
     </div>
        <div class = "box-body">  
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
            'attribute' => $attribute['head_img'],
            'format' => 'raw',
            'value' => Html::img($model->getModel('patient')->head_img?Yii::$app->params['cdnHost'].$model->getModel('patient')->head_img:$baseUrl.'/public/img/user/img_user_small.png',['onerror' => 'this.src = "'.$baseUrl . '/public/img/user/img_user_small.png"'])
            ],
            [
                'attribute' => $attribute['username'],
                'value' => $model->getModel('patient')->username
            ],
            [
                'attribute' => $attribute['patient_number'],
                'value' => $model->getModel('patient')->patient_number
            ],
            [
                'attribute' => $attribute['sex'],
                'value' => Patient::$getSex[$model->getModel('patient')->sex]
            ],
            [
                'attribute' => $attribute['iphone'],
                'value' => $model->getModel('patient')->iphone
            ],
            [
                'attribute' => $attribute['birthday'],
                'value' => date('Y-m-d',$model->getModel('patient')->birthday)
            ],
            [
                'attribute' => '出生时间',
                'value' => date('H:i',$model->getModel('patient')->birthday) == '00:00'?'':date('H:i',$model->getModel('patient')->birthday)
            ],
            
            [
                'attribute' => $attribute['patient_source'],
                'value'=> Patient::$getPatientSource[$model->getModel('patient')->patient_source]
            ],
            [
                'attribute' => $reportattribute['second_department_id'],
                'value' => SecondDepartment::getDepartmentFields($model->getModel('report')->second_department_id,['name'])['name']
            ],
            [
                'attribute' => $reportattribute['doctor_id'],
                'value' => $model->getModel('report')->doctorName
            ],
            [
                'attribute' => '服务类型',
                'value' => $model->getModel('report')->type_description
            ],
            [
                'attribute' => $attribute['card'],
                'value'=> $model->getModel('patient')->card
            ],
            [
                'attribute' => $attribute['email'],
                'value' => $model->getModel('patient')->email
            ],
            [
                'attribute' => $attribute['marriage'],
                'value' => Patient::$getMarriage[$model->getModel('patient')->marriage]
            ],
            [
                'attribute' => $attributeLabels['nationality'],
                'value' => PatientSubmeter::$getNationality[$model->getModel('patientSubmeter')->nationality]
            ],
            [
                'attribute' => $attribute['nation'],
                'value' => Patient::$getNation[$model->getModel('patient')->nation]
            ],
            [
                'attribute' => $attributeLabels['languages'],
                'value' => 5 == $model->getModel('patientSubmeter')->languages? $model->getModel('patientSubmeter')->other_languages:PatientSubmeter::$getLanguages[$model->getModel('patientSubmeter')->languages]
            ],
            [
                'attribute' => $attributeLabels['faiths'],
                'value' => 6 == $model->getModel('patientSubmeter')->faiths? $model->getModel('patientSubmeter')->other_faiths:PatientSubmeter::$getFaiths[$model->getModel('patientSubmeter')->faiths]
            ],
            [
                'attribute' => $attribute['occupation'],
                'value' => Patient::$getOccupation[$model->getModel('patient')->occupation]
            ],
            [
                'attribute' => $attribute['worker'],
                'value' => $model->getModel('patient')->worker
            ],
//            [
//                'attribute' => $attribute['age'],
//                'value' => Patient::dateDiffage($model->getModel('patient')->birthday, time())
//            ],
//            
//             [
//                 'attribute' => $attribute['province'],
//                 'value' => $model->getModel('patient')->province
//             ],
//             [
//                 'attribute' => $attribute['city'],
//                 'value' => $model->getModel('patient')->city
//             ],
//             [
//                 'attribute' => $attribute['area'],
//                 'value' => $model->getModel('patient')->area
//             ],
            [
                'attribute' => '地址',
                'value' => $model->getModel('patient')->province.$model->getModel('patient')->city. $model->getModel('patient')->area
            ],
            [
                'attribute' => $attribute['detail_address'],
                'value' => $model->getModel('patient')->detail_address
            ],
            [
                'attribute' => $attribute['remark'],
                'value' => $model->getModel('patient')->remark
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
