<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\modules\patient\models\Patient;
use app\modules\make_appointment\models\Appointment;
use rkit\yii2\plugins\ajaxform\Asset;
/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\Appointment */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
$attribute = $model->getModel('patientModel')->attributeLabels();
$required = '<span class = "label-required">*</span>';
Asset::register($this);

?>
<div class="appointment-form">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'id' => 'createMaterialForm',
        'options' =>  ['enctype' => 'multipart/form-data'],
    ]); ?>
    <div class = 'col-sm-2 col-md-2 col-custom'>
        <?= $form->field($model->getModel('patientModel'), 'head_img')->hiddenInput(['id' =>'avatar_url'])->label(false); ?>
        <div id="crop-avatar">
            <!-- Current avatar -->
            <div class="avatar-view" title="上传头像">
                    <?= Html::img($model->getModel('patientModel')->head_img?Yii::$app->params['cdnHost'].$model->getModel('patientModel')->head_img:Yii::$app->request->baseUrl.'/public/img/user/img_user_big.png',['alt' => '头像','onerror'=>"this.src='{$baseUrl}/public/img/user/img_user_big.png'"])?>
                <div class = 'btn btn-default font-body2 header_img'>上传头像</div>
            </div>

        </div>
        
    </div>
    <div class = 'col-sm-10 col-md-10' style = "margin-bottom : 35px;">
        <div class = 'row title-patient-div'>
                <div class = 'col-sm-12'>
                    <p class="titleP">
                        <span class="circleSpan"></span>
                        <span class="titleSpan">基本信息</span>
                    </p>
                </div>
        </div>
        <div class = 'row'>
            <div class = 'col-md-4'>
                <?= $form->field($model->getModel('patientModel'), 'username')->textInput(['autocomplete'=>'off'])->label($attribute['username'].'<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-md-4'>
                <?= $form->field($model->getModel('patientModel'), 'sex')->radioList(Patient::$getSex,['class' => 'sex'])->label($attribute['sex'].'<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-md-4'>
                <?= $form->field($model->getModel('patientModel'), 'iphone')->textInput(['autocomplete'=>'off','maxlength'=>11])->label($attribute['iphone'].'<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class = 'row'>
            
            <div class = 'col-md-4'>
                <?= $form->field($model->getModel('patientModel'), 'birthTime')->widget(
                    DatePicker::className(),[
                        'addon' => false,
                        'inline' => false,
                        'language' => 'zh-CN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd'
                        ],
                        'options' => [
                            'autocomplete' => 'off'
                        ]
                    ]
                )->label($attribute['birthday'].'<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-md-4 bootstrap-timepicker'>
                <?php echo $form->field($model->getModel('patientModel'), 'hourMin')->textInput(['class' => 'form-control timepicker']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model->getModel('patientModel'), 'patient_source')->dropDownList(Patient::$getPatientSource, ['prompt' => '请选择'])->label($attribute['patient_source'].'<span class = "label-required">*</span>') ?>
            </div>
        </div>
    </div>
    
    <?= $this->render('_createMaterial',['list' => $list,'model' => $model,'dataProvider' => $dataProvider,'form' => $form,'materialTotal' => $materialTotal]) ?> 
    
    <?php ActiveForm::end(); ?>

</div>
