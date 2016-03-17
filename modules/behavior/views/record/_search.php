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
    <div class ="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary btn-submit']) ?>
        <?= Html::a('重置', ['index'], ['class' => 'btn btn-default']) ?>
        <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/deletemonth', $this->params['permList'])):?>
        <?= Html::a('删除一个月前记录',['deletemonth'],[
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '你确定要删除一个月前记录吗?',
                        'method' => 'get',
                    ],
                ]) ?>
         <?php endif;?>
    </div>
    <?php ActiveForm::end(); ?>

  </div>
