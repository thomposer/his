<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\Consumables;
use app\modules\spot\models\Tag;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\ConsumablesClinic */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="consumables-clinic-form">

    <?php $form = ActiveForm::begin(); ?>
	<div class="row">
        <div class='col-md-6'>
        	<?php if($model->isNewRecord):?>
            <?= $form -> field($model, 'consumables_id') -> dropDownList(ArrayHelper::map($consumablesList, 'id', 'name'),
                [
                    'prompt' => '请选择医疗耗材名称',
                    'class' => 'form-control select2',
                    'style' => 'width:100%;',
                    'disabled' => $model->isNewRecord ? false : true
                ])->label($attributeLabels['name'].'<span class = "label-required">*</span>') ?>
             <?php else: ?>
             <?= $form -> field($model, 'name')->textInput(['disabled' => true]) ?>
             <?php endif;?>   
        </div>
    </div>
    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'product_name') -> textInput(['maxlength' => true,'disabled' => true]) ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'en_name') -> textInput(['maxlength' => true,'disabled' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'type') -> dropDownList(Consumables::$getType,['disabled' => true])->label($attributeLabels['type'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'specification') -> textInput(['maxlength' => true,'disabled' => true])->label($attributeLabels['specification'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>
    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'unit') ->textInput(['disabled' => true])->label($attributeLabels['unit'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'meta') -> textInput(['maxlength' => true,'disabled' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'manufactor') -> textInput(['maxlength' => true,'disabled' => true]) ?>
        </div>
        <div class='col-md-6'>
			<?= $form -> field($model, 'remark') -> textInput(['maxlength' => true,'disabled' => true]) ?>        
		</div>
    </div>

    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'tag_name') -> textInput(['maxlength' => true,'disabled' => true]) ?>
        </div>
    </div>
	<div class="row">
    	<div class='col-md-6'>
			<?= $form -> field($model, 'price') -> textInput(['maxlength' => true])->label($attributeLabels['price'].'<span class = "label-required">*</span>')  ?>        
		</div>
        <div class='col-md-6'>
			<?= $form -> field($model, 'default_price') -> textInput(['maxlength' => true]) ?>        
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    
</div>
