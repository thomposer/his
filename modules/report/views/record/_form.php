<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\modules\patient\models\Patient;
use app\modules\make_appointment\models\Appointment;
use app\modules\patient\models\PatientSubmeter;
use app\modules\spot_set\models\SpotType;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
// use rkit\yii2\plugins\ajaxform\Asset;
// Asset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\patient\models\Patient */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->getModel('patient')->attributeLabels();
$isNewRecord = $model->getModel('patient')->isNewRecord?false:true;
$baseUrl = Yii::$app->request->baseUrl;
$attributeLabels = $model->getModel('patientSubmeter')->attributeLabels();
$reportModelAttributeLabels = $model->getModel('report')->attributeLabels();
?>

<div class="patient-form">

    <?php $form = ActiveForm::begin(['id'=>'report-patient']); ?>
    <div class = 'col-sm-2 col-md-2 col-custom upload-head'>
          <?= $form->field($model->getModel('patient'), 'head_img')->hiddenInput(['id' =>'avatar_url'])->label(false); ?>
          <div id="crop-avatar">
                <!-- Current avatar -->
                <div class="avatar-view">
                   <?php if($model->getModel('patient')->head_img):?>
                   <?= Html::img(Yii::$app->params['cdnHost'].$model->getModel('patient')->head_img,['alt' => '头像','onerror'=>'this.src=\''.$baseUrl.'/public/img/default.png\'']) ?>
                   <?php else:?>
                    <?= Html::img(Yii::$app->request->baseUrl.'/public/img/user/img_user_big.png',['alt' => '头像'])?>
                   <?php endif;?>
                   <div class = 'btn btn-default font-body2 header_img'>上传头像</div>
                </div>

            </div>
    </div>
    <div class = 'col-sm-10 col-md-10'>
        <div class = 'row title_patient_div'>
            <div class = 'col-sm-12'>
                <p class="title_p">
                    <span class="circle_span"></span>
                    <span class="title_span">患者信息</span>
                </p>
            </div>
        </div>
    <div class = 'row'>
        <div class="row">
            <div class = 'col-sm-4'>
                <?= $form->field($model->getModel('patient'), 'username')->textInput(['maxlength' => true,'readonly' => false,'autocomplete'=>'off'])->label($attribute['username'].'<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model->getModel('patient'), 'sex')->radioList(Patient::$getSex,['class' => 'sex'])->label($attribute['sex'].'<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model->getModel('patient'), 'iphone')->textInput(['maxlength' => 11,'readonly' => false])->label($attribute['iphone'].'<span class = "label-required">*</span>') ?>            
            </div>
        </div>
    <div class="row">
        <div class = 'col-sm-4'>
            <?= $form->field($model->getModel('patient'), 'birthTime')->widget(
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
        <div class="col-sm-4  bootstrap-timepicker">
            <?php echo $form->field($model->getModel('patient'), 'hourMin')->textInput(['class' => 'form-control timepicker']) ?>
        </div>
        <div class = 'col-sm-4'>
            <?= $form->field($model->getModel('patient'), 'patient_source')->dropDownList(Patient::$getPatientSource, ['prompt' => '请选择'])->label($attribute['patient_source'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>
    
    <div class = 'row'>
        
        <div class = 'col-sm-4'>
        <?= $form->field($model->getModel('report'), 'doctor_id')->dropDownList(ArrayHelper::map($doctorInfo, 'doctor_id', 'doctorName'),['prompt' => '请选择'])->label($reportModelAttributeLabels['doctor_id'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-sm-4'>
        <?= $form->field($model->getModel('report'), 'second_department_id')->dropDownList([],['prompt' => '请选择'])->label($reportModelAttributeLabels['second_department_id'].'<span class = "label-required">*<span style="font-size:12px">（科室与服务类型无关联，若不匹配请先修改）</span></span>') ?>
        </div>
        <div class = 'col-sm-4'>
            <?= $form->field($model->getModel('report'), 'type')->dropDownList([], ['prompt' => '请选择'])->label($reportModelAttributeLabels['type'].'<span class = "label-required">*</span>') ?>
        </div>
           <?= $form->field($model->getModel('report'), 'doctorName')->hiddenInput()->label(false) ?>
    </div>
   
    <div class = 'row'>
        <div class = 'col-sm-4'>
           	<?= $form->field($model->getModel('patient'), 'card')->textInput(['maxlength' => true,'placeholder'=>'请输入身份证号']) ?>
        </div>
        <div class = 'col-sm-4'>
        	<?= $form->field($model->getModel('patient'), 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class = 'col-sm-4'>
         	<?= $form->field($model->getModel('patient'), 'marriage')->dropDownList(Patient::$getMarriage,['prompt' => '请选择']) ?>
        </div>
    </div>
    
    <div class = 'row'>
        <div class = 'col-md-4'>
            <?= $form->field($model->getModel('patientSubmeter'), 'nationality')->dropDownList(PatientSubmeter::$getNationality,['class' => 'form-control select2','style' => 'width:100%','prompt' => '请选择国家/地区'])->label($attributeLabels['nationality']) ?>
        </div>
        <div class = 'col-sm-4'>
        <?= $form->field($model->getModel('patient'), 'nation')->dropDownList(Patient::$getNation,['prompt' => '请选择']) ?>
        </div>
        <div class = 'col-sm-4'>
            <?= $form->field($model->getModel('patient'), 'mommyknows_account')->textInput(['maxlength'=>true,'placeholder'=>'请输入妈咪知道注册的手机号'])->label(Html::encode($attribute['mommyknows_account']).'&nbsp;&nbsp;<span class = "fa fa-question-circle blue" data-toggle="tooltip" data-html="true" data-placement="right" data-original-title="患者信息保存时会自动完成妈咪知道账号的绑定，请确保账号有效"></span>') ?>
        </div>
    </div>

        <div class = 'row'>

            <div class = 'col-sm-4' id="languages_select_div">
                <?= $form->field($model->getModel('patientSubmeter'),  'languages')->dropDownList(PatientSubmeter::$getLanguages,['prompt' => '请选择']) ?>
            </div>

            <div class = 'col-sm-4 other_div' id="languages_input_div">
                <?= $form->field($model->getModel('patientSubmeter'), 'other_languages')->textInput(['maxlength' => true,'placeholder'=>'请输入第一语言'])->label(false) ?>
            </div>

            <div class = 'col-sm-4' id="faiths_select_div">
                <?= $form->field($model->getModel('patientSubmeter'), 'faiths')->dropDownList(PatientSubmeter::$getFaiths,['prompt' => '请选择']) ?>
            </div>

            <div  class = 'col-sm-4 other_div' id="faiths_input_div">
                <?= $form->field($model->getModel('patientSubmeter'), 'other_faiths')->textInput(['maxlength' => true,'placeholder'=>'请输入宗教信仰'])->label(false) ?>
            </div>

        </div>

    <div class = 'row'>
        <div class = 'col-sm-4'>
         <?= $form->field($model->getModel('patient'), 'occupation')->dropDownList(Patient::$getOccupation,['prompt' => '请选择']) ?>
        </div>
        <div class = 'col-sm-8'>
        <?= $form->field($model->getModel('patient'), 'worker')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    
    <div class = 'row'>
        <div class = 'col-sm-4'>
        <?= $form->field($model->getModel('patient'), 'address')->textInput(['maxlength' => true,'data-toggle' => 'city-picker']) ?>

        </div>
        <div class = 'col-sm-8'>
        <?= $form->field($model->getModel('patient'), 'detail_address')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
        <?= $form->field($model->getModel('patient'), 'remark')->textarea(['rows' => 2]) ?>
        </div>
    </div>

    <div class = 'row title_child_div'>
        <div class = 'col-sm-12'>
            <p class="title_p">
                <span class="circle_span"></span>
                <span class="title_span">儿童特有信息</span>
            </p>
        </div>
    </div>

        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model->getModel('patientSubmeter'), 'parent_education')->dropDownList(PatientSubmeter::$getParentEducation,['prompt' => '请选择']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model->getModel('patientSubmeter'), 'parent_occupation')->dropDownList(PatientSubmeter::$getParentOccupation,['prompt' => '请选择']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model->getModel('patientSubmeter'), 'parent_marriage')->dropDownList(PatientSubmeter::$getParentMarriage,['prompt' => '请选择']) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model->getModel('patientSubmeter'), 'guardian')->dropDownList(PatientSubmeter::$getGuardian,['prompt' => '请选择']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model->getModel('patientSubmeter'), 'other_guardian')->textInput(['maxlength' => true,'placeholder' => '“法定监护人”备注','style'=>'margin-top: 5px;'])->label(' ') ?>
            </div>
        </div>

    <div class = 'row title_family_div'>
        <div class = 'col-sm-12'>
            <p class="title_p">
                <span class="circle_span"></span>
                <span class="title_span">添加家庭成员</span>
            </p>
        </div>
    </div>
    <div class = 'form-margin' id = 'family'>
    
    <?php foreach ($familyInfo as $key => $v): ?>

    <?php 
        $model->getModel('patient')->family_relation = isset($v['relation'])?$v['relation']:'';
        $model->getModel('patient')->family_name = isset($v['name'])?$v['name']:'';
        $model->getModel('patient')->family_sex = isset($v['sex'])?$v['sex']:'';
        $model->getModel('patient')->family_iphone = isset($v['iphone'])?$v['iphone']:'';
        $model->getModel('patient')->family_card = isset($v['card'])?$v['card']:'';
        if(isset($v['birthday'])){
            if($v['birthday']==0){
                $model->getModel('patient')->family_birthday = '';
            }else{
                $model->getModel('patient')->family_birthday = date('Y-m-d',$v['birthday']);
            }
        }  else {
            $model->getModel('patient')->family_birthday = '';
        }
        
    ?>
    <div class = 'family-list'>
    <div class = 'row one'>
    <div class = 'col-sm-4 family_relation'>
    <?= $form->field($model->getModel('patient'), 'family_relation')->dropDownList(Patient::$getFamilyRelation,['prompt' => '请选择','class' => 'patient-family_relation form-control','name' => 'Patient[family_relation][]'])->label($attribute['family_relation'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-3 family_name'>
    <?= $form->field($model->getModel('patient'), 'family_name')->textInput(['class' => 'patient-family_name form-control','maxlength' => true,'name' => 'Patient[family_name][]'])->label($attribute['family_name'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-4 family_sex'>
    <?= $form->field($model->getModel('patient'), 'family_sex')->dropDownList(Patient::$getSex,['prompt' => '请选择','class' => 'patient-family_sex form-control','name' => 'Patient[family_sex][]'])->label($attribute['family_sex'].'<span class = "label-required">*</span>') ?>
    </div>
    </div>
    <div class = 'row second'>
        <div class = 'col-sm-4 family_card'>
            <?= $form->field($model->getModel('patient'), 'family_card')->textInput(['class' => 'patient-family_card form-control','maxlength' => 18,'name' => 'Patient[family_card][]'])->label($attribute['family_card']) ?>
        </div>
    <div class = 'col-sm-3 family_birthday'>
    <?= $form->field($model->getModel('patient'), 'family_birthday')->textInput(['class' => 'patient-family_birthday form-control','name' => 'Patient[family_birthday][]','autocomplete'=>'off'])->label($attribute['family_birthday']) ?>
    </div>
    <div class = 'col-sm-2 family_iphone'>
    <?= $form->field($model->getModel('patient'), 'family_iphone')->textInput(['class' => 'patient-family_iphone form-control','maxlength' => 11,'name' => 'Patient[family_iphone][]'])->label($attribute['family_iphone'].'<span class = "label-required">*</span>') ?>
    </div>
   
    <div class = 'col-sm-3'>
        <div class = 'form-group'>
            
            <a href = 'javascript:void(0);' class = 'btn-from-delete-add btn family-delete'>
                <i class = 'fa fa-minus'></i>
            </a>
             <a href = 'javascript:void(0);' class = 'btn-from-delete-add btn family-add'>
                <i class = 'fa fa-plus'></i>
            </a>
        </div>
    </div>
    </div>
    </div>
    
    <?php endforeach;?>
    </div>
    
    <div class="form-group">
        <?= Html::a('取消',Yii::$app->request->referrer,['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form','id' => 'reportForm','data-url'=>  Url::to(['confirm-report']),'contentType' => 'application/x-www-form-urlencoded','data-request-method'=>'post','data-modal-size'=>'normal','processData'=>1,'actionUrl'=>$actionUrl]) ?>
    </div>
    
    </div>
    <?php ActiveForm::end(); ?>

</div>
