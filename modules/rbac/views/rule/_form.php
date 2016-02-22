<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Rule */
/* @var $form yii\widgets\ActiveForm */
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php $this->registerCssFile('@web/public/css/bootstrap/bootstrap.css') ?>
    <?php $this->registerCssFile('@web/public/css/rbac/rbac.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="rule-form">

    <?php $form = ActiveForm::begin([
      'options' => ['class' => 'form-horizontal'],
      'fieldConfig' => [
          'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>",
]]); ?> 

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

   

    <div class="footer_button" >
        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
        <?= Html::submitButton('返回列表',['class' =>'btn btn-success returnindex' ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>