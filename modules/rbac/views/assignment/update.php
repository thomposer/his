<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Assignment */
use app\common\AutoLayout;
$this->title = '分配角色: ' . ' ' . $userName;
$this->params['breadcrumbs'][] = ['label' => 'Assignments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->item_name, 'url' => ['view', 'item_name' => $model->item_name, 'user_id' => $model->user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php $this->registerCssFile('@web/public/css/bootstrap/bootstrap.css') ?>
    <?php $this->registerCssFile('@web/public/css/rbac/rbac.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="assignment-update">
    <?php $form = ActiveForm::begin([
      'options' => ['class' => 'form-horizontal'],
      'fieldConfig' => [
          'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>",
]]); ?> 
    
   <?= $form->field($model, 'user_id')->dropDownList(($user_data),['maxlength' => 255,'readonly'=>'true']) ?> 

   <?= $form->field($model, 'item_name')->checkboxList($item_name?ArrayHelper::map($item_name, 'name', 'description'):array())?>

    <div class="form-group" style="margin-left:25%;margin-right:25%;">
        <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('返回列表', ['@RbacAssignment'], ['class' => 'btn btn-success'])?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('artemplate')?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>