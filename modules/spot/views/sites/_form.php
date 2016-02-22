<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
/* @var $form yii\widgets\ActiveForm */
/* @var $this \yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

    <div class = 'col-md-6'>
        <?php $form = ActiveForm::begin([
                'id' => 'contact-form',
                'method' => 'post',
                'options' => ['class' => 'form-input'],
            ]); ?>
            <?= $form->field($model, 'spot_name')->label('<span class="need">*</span>站点名称') ?>
            <?= $form->field($model, 'spot')->label('<span class="need">*</span>英文简称') ?>
            <?= $form->field($model, 'template')->label('站点模板')->dropDownList(ArrayHelper::map($templateList,'spot','spot_name'),['class' => 'form-control select2','style' => 'width:100%']) ?>
           
               <div class="form-group" >
                    <?= Html::submitButton($model->isNewRecord?'申请站点':'修改', ['class' =>  'btn btn-success', 'name' => 'submit-button']) ?>
                </div>
            
        <?php ActiveForm::end(); ?>
	</div>
  