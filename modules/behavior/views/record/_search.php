<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\behavior\models\search\BehaviorRecordSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="behavior-record-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    	'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => 1],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入用户名']) ?>

    <?= $form->field($model, 'spot_id')->dropDownList($spotList, ['prompt'=>'请选择诊所/机构']) ?>

    <?= $form->field($model, 'module')->dropDownList($moduleList, ['prompt'=>'请选择模块']) ?>

    <?= $form->field($model, 'action')->label('动作')->dropDownList($actionList, ['prompt'=>'请选择动作']) ?>
    
    <?= $form->field($model, 'data')->textInput(['placeholder' => '请输入参数']) ?>
    <div class ="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/delete-month', $this->params['permList'])):?>
        <?=  Html::a('批量删除',['delete-month'],[
                    'class' => 'btn btn-delete',
                    'data-confirm'=>false, 
                    'data-method'=>false,
                    'data-request-method'=>'post',
                    'role'=>'modal-remote',
                    'data-toggle'=>'tooltip',
                    'data-confirm-title'=>'系统提示',
                    'data-delete' => false,
                    'data-confirm-message'=>'你确定要删除一个月前记录吗?',
                ])  ?>
         <?php endif;?>
    </div>
    <?php ActiveForm::end(); ?>

  </div>
