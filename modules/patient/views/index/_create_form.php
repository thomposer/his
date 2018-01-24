<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\modules\patient\models\Patient;
$attribute = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
		    'method' => 'post',
		    'options' =>  ['enctype' => 'multipart/form-data'],
		]); ?>
    <div class = 'col-sm-2 col-md-2 col-custom'>
      <?= $form->field($model, 'head_img')->hiddenInput(['id' =>'avatar_url'])->label(false); ?>
      <div id="crop-avatar">
            <!-- Current avatar -->
            <div class="avatar-view" title="上传头像">
               <?php if($model->head_img):?>
               <?= Html::img($model->head_img,['alt' => '头像']) ?>
               <?php else:?>
                <?= Html::img(Yii::$app->request->baseUrl.'/public/img/user/img_user_big.png',['alt' => '头像'])?>
               <?php endif;?> 
               <div class = 'btn btn-default font-body2 header_img'>上传头像</div>             
            </div> 
                       
        </div>
      
    </div>
    <div class = 'col-sm-10 col-md-10'>
    <div class = 'row'>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'username')->textInput(['maxlength' => true])->label($attribute['username'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'birthTime')->widget(
        DatePicker::className(),[
            'addon' => false,
            'inline' => false,
            'language' => 'zh-CN',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ]
        )->label($attribute['birthTime'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'sex')->radioList(Patient::$getSex,['class' => 'sex'])->label($attribute['sex'].'<span class = "label-required">*</span>') ?>
    </div>
    </div>
    <div class = 'row'>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'iphone')->textInput(['maxlength' => 11])->label($attribute['iphone'].'<span class = "label-required">*</span>') ?>
    </div>

    <div class = 'col-sm-4'>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label($attribute['email']) ?> 
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'card')->textInput(['maxlength' => true])->label($attribute['card']) ?> 
    </div>
    </div>
    
    <div class = 'row'>
    
    <div class = 'col-sm-4'>
     <?= $form->field($model, 'marriage')->dropDownList(Patient::$getMarriage,['prompt' => '请选择']) ?>
    </div>
    <div class = 'col-sm-4'>
     <?= $form->field($model, 'nation')->dropDownList(Patient::$getNation,['prompt' => '请选择']) ?>
    </div>
     </div>
     
    <div class = 'row'>
        <div class = 'col-sm-4'>
     <?= $form->field($model, 'occupation')->dropDownList(Patient::$getOccupation,['prompt' => '请选择']) ?>
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'worker')->textInput(['maxlength' => true])->label($attribute['worker']) ?> 
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'wechat_num')->textInput(['maxlength' => true])->label($attribute['wechat_num']) ?> 
    </div>
    </div>
    
    <div class = 'row'>
    <div class = 'col-sm-4'>
        <?= $form->field($model, 'patient_source')->dropDownList(Patient::$getPatientSource,['prompt' => '请选择']) ?>
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'address')->textInput(['maxlength' => true,'data-toggle' => 'city-picker']) ?>
    </div>
    <div class = 'col-sm-4'>
     <?= $form->field($model, 'detail_address')->textInput(['maxlength' => true])->label($attribute['detail_address']) ?> 
    </div>
    
    </div>
    
    <div class = 'row'>
    <div class = 'col-sm-12'>
        <?= $form->field($model, 'remark')->textarea(['maxlength' => true])->label($attribute['remark']) ?>
    </div>
    </div>
    
    
    <div class="form-group">
        <?= Html::a('取消',['index'],['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>
        

    </div>
    
    <?php ActiveForm::end(); ?>

</div>
