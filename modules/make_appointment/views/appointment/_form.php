<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\modules\patient\models\Patient;
use app\modules\make_appointment\models\Appointment;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\Appointment */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
$attribute = $model->attributeLabels();
$required = '';
if($onlyAppointmentDoctor){
    $required = '<span class = "label-required">*</span>';
}
?>
<div class="appointment-form">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'id' => 'appointmentForm',
        'options' =>  ['enctype' => 'multipart/form-data'],
    ]); ?>
    <div class = 'col-sm-2 col-md-2 col-custom'>
        <?= $form->field($model, 'head_img')->hiddenInput(['id' =>'avatar_url'])->label(false); ?>
        <div id="crop-avatar">
            <!-- Current avatar -->
            <div class="avatar-view" title="上传头像">
                <?php if($model->head_img):?>
                    <?= Html::img(Yii::$app->params['cdnHost'].$model->head_img,['alt' => '头像','onerror'=>"this.src='{$baseUrl}/public/img/user/img_user_big.png'"]) ?>
                <?php else:?>
                    <?= Html::img(Yii::$app->request->baseUrl.'/public/img/user/img_user_big.png',['alt' => '头像','onerror'=>"this.src='{$baseUrl}/public/img/user/img_user_big.png'"])?>
                <?php endif;?>
                <div class = 'btn btn-default font-body2 header_img'>上传头像</div>
            </div>

        </div>
    </div>
    <div class = 'col-sm-10 col-md-10'>
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
                <?= $form->field($model, 'username')->textInput(['autocomplete'=>'off'])->label($attribute['username'].'<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-md-4'>
                <?= $form->field($model, 'sex')->radioList(Patient::$getSex,['class' => 'sex'])->label($attribute['sex'].'<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-md-4'>
                <?= $form->field($model, 'iphone')->textInput(['autocomplete'=>'off'])->label($attribute['iphone'].'<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class = 'row'>
            
            <div class = 'col-md-4'>
                <?= $form->field($model, 'birthday')->widget(
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
                <?php echo $form->field($model, 'hourMin')->textInput(['class' => 'form-control timepicker']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'patient_source')->dropDownList(Patient::$getPatientSource, ['prompt' => '请选择'])->label($attribute['patient_source'].'<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class = 'row title-patient-div'>
                <div class = 'col-sm-12'>
                    <p class="titleP">
                        <span class="circleSpan"></span>
                        <span class="titleSpan">预约信息<span class = "label-required">（请依次填写医生、服务、日期、时间的预约信息）</span></span>
                    </p>
                </div>
        </div>
        <div class = 'row'>
            
<!--             <div class = 'col-md-4'> -->
                <?php // $form->field($model, 'second_department_id')->dropDownList(ArrayHelper::map($departmentInfo, 'id', 'name','onceName'),['prompt' => '请选择'])->label($attribute['second_department_id'].'<span class = "label-required">*</span>') ?>
<!--             </div> -->
            <?php if ($hasAppointmentDoctor) : ?>
            <div class = 'col-md-4'>
                <?= $form->field($model, 'doctor_id')->dropDownList(ArrayHelper::map($doctorInfo, 'id', 'username'),['prompt' => '请选择'])->label($attribute['doctor_id'].$required) ?>
            </div>
            <?php endif;?>
            <div class = 'col-md-4'>
                <?= $form->field($model, 'type')->dropDownList([],['prompt' => '请选择'])->label($attribute['type'].'<span class = "label-required">*</span>') ?>
            </div>
        </div>

    <div class = 'row'>
        <div class = 'col-md-4'>
           <?= $form->field($model, 'appointmentDate')->dropDownList(is_integer($model->time)?[date('Y-m-d',$model->time) => date('Y-m-d',$model->time)]:array(),['class' => 'form-control select2','style' => 'width:100%','prompt' => '请选择'.$attribute['appointmentDate']])->label($attribute['appointmentDate'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-4'>
           <?= $form->field($model, 'time')->dropDownList(is_integer($model->time)?[$model->time => date('H:i',$model->time)]:array(),['class' => 'form-control select2','style' => 'width:100%','prompt' => '请选择'.$attribute['time']])->label($attribute['time'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-4'>
                <?= $form->field($model, 'appointment_origin')->dropDownList(Appointment::$getAppointmentOrigin,['prompt' => '请选择'])->label($attribute['appointment_origin'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>
    <div class = 'row'>
            <div class = 'col-md-12'>
        		<?= $form->field($model, 'illness_description')->textarea(['rows' => 6,'maxlength'=>500,'placeholder' => '请输入'.$attribute['illness_description']])->label($attribute['illness_description'].'<span class = "label-required">*</span>') ?>
            </div>
    </div>
    <div class = 'row'>
            <div class = 'col-md-12'>
                <?= $form->field($model, 'remarks')->textarea(['rows' => 6,'placeholder' => '请输入'.$attribute['remarks']]) ?>
            </div>
    </div>
        <?= $form->field($model, 'hasAppointmentOperator')->hiddenInput(['value' => '0'])->label(false) ?>
    <div class="form-group">
        <?= Html::a('取消',['appointment-detail'],['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
