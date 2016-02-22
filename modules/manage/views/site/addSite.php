<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '添加站点';
?>
<div class="row">
    <div class="col-sm-12">
        <?php $form = ActiveForm::begin([
                'id' => 'contact-form',
                'layout' => 'horizontal',
                'method' => 'post',
                'action' => ['site/add'],
                'options'=>['enctype'=>'multipart/form-data','class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-5\">{input}</div>\n<div class=\"col-lg-5\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-2 control-label'],
                 ],
            ]); ?>
            <?= $form->field($model, 'appid')->label('AppID(应用ID)：') ?>
            <?= $form->field($model, 'appsecret')->label('AppSecret(应用密钥)：') ?>
            <?= $form->field($model, 'wxname')->label('公众号名称：') ?>
            <?= $form->field($model, 'wxcode')->label('微信号：') ?>
            <?= $form->field($model, 'url')->textInput(['readonly'=>true])->label('服务器地址Url：') ?>
            <?= $form->field($model, 'token')->textInput(['readonly'=>true])->label('微信开发模式token：') ?>
            <?= $form->field($model, 'aeskey')->label('EncodingAESKey(消息加解密密钥)：') ?>
            <?= $form->field($model, 'maininfo')->label('主体信息：') ?>
            <?= $form->field($model, 'remark')->label('备注信息：')->textArea(['rows' => 2]) ?>
            
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-10">
                    <?= Html::submitButton('提交', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>