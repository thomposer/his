<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\behavior\models\search\BehaviorRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="behavior-record-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    	'options' =>  ['class' => 'form-horizontal search-form'],
    	'fieldConfig' => [
    		'template' => "<div class='search-labels text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div>",
    	]
    ]); ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'spot')->dropDownList($spotList, ['prompt'=>'请选择']) ?>

    <?= $form->field($model, 'module')->dropDownList($moduleList, ['prompt'=>'请选择']) ?>

    <?= $form->field($model, 'action')->label('动作')->dropDownList($actionList, ['prompt'=>'请选择']) ?>

    <?= Html::submitButton('搜索', ['class' => 'btn btn-primary btn-submit']) ?>
    <?= Html::a('重置', ['index'], ['class' => 'btn btn-default']) ?>
    <?php ActiveForm::end(); ?>

    <div class="cl tr prg">
    	<?= Html::button('删除1个月前记录', ['class' => 'btn btn-danger js-del']) ?>
    </div>
</div>
