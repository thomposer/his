<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Assignment */
/* @var $form yii\widgets\ActiveForm */
use app\common\AutoLayout;
?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php $this->registerCssFile('@web/public/css/bootstrap/bootstrap.css') ?>
    <?php $this->registerCssFile('@web/public/css/rbac/rbac.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="assignment-form">

    <?php $form = ActiveForm::begin([
      'options' => ['class' => 'form-horizontal'],
      'fieldConfig' => [
          'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>",
]]); ?> 
    <?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map($userlist, 'user_id', 'username')) ?> 
    <?= $form->field($model, 'item_name')->checkboxList($roles == ''?ArrayHelper::map($roles, 'name', 'description'):'')?>
    <div class="footer_button" >
        <?= Html::submitButton('修改', ['class' => 'btn btn-success']) ?>
        <?= Html::submitButton('返回列表',['class' =>'btn btn-primary returnindex' ]) ?>
    </div>
   
    
    <?php ActiveForm::end(); ?>
</div>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
