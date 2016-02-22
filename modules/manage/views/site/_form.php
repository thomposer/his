<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\awpmanage\models\Service */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="service-form">

    <?php $form = ActiveForm::begin([
                'id' => 'contact-form',
                'method' => 'post',
                'options'=>['enctype'=>'multipart/form-data','class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-5\">{input}</div>\n<div class=\"col-lg-5\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-2 control-label'],
                 ],
            ]); ?>
    <?= $form->field($model, 'appid')->textInput(['maxlength' => true])->label('AppID(应用ID)：') ?>
    
    <?= $form->field($model, 'appsecret')->textInput(['maxlength' => true])->label('AppSecret(应用密钥)：') ?>
    
    <?= $form->field($model, 'wxname')->textInput(['maxlength' => true])->label('公众号名称：') ?>
    
    <?= $form->field($model, 'wxcode')->textInput(['maxlength' => true])->label('微信号：') ?>
   
    <?= $form->field($model, 'url')->textarea(['readonly'=>true, 'rows' => 6])->label('服务器地址Url：') ?>
    
    <?= $form->field($model, 'token')->textInput(['readonly'=>true])->label('微信开发模式token：') ?>
    
    <?= $form->field($model, 'aeskey')->textInput(['maxlength' => true])->label('EncodingAESKey(消息加解密密钥)：') ?>
    
    <?= $form->field($model, 'maininfo')->label('主体信息：') ?>
    
    <?= $form->field($model, 'remark')->textArea(['rows' => 6])->label('备注信息：') ?>

    <div class="form-group">
        <div class="col-sm-offset-4 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? '提交' : '修改', ['class' =>  $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'submit-button']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
