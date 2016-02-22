<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\search\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "<div class='col-xs-3 col-sm-3 text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>",
        ]
    ]); ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'type')->dropDownList($typeList) ?>
    
    <?php if ($allspotLists && $spotList):?>
	   <?= $form->field($model, 'spot')->dropDownList($allspotLists) ?>
	<?php endif;?>
    
	 
    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="applySearch-button">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('重置', '@RbacAssignment', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
