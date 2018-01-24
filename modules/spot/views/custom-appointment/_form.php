<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\OrganizationType;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\spotType */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="spot-type-form">

    <?php $form = ActiveForm::begin(); ?>


    <?php
//        if($model->is_delete){
//            echo $form->field($model, 'type')->textInput(['maxlength' => true,'readonly' => true])->label($attributeLabels['type'].'<span class = "label-required">*</span>');
//        }else{
            echo $form->field($model, 'name')->textInput(['maxlength' => true])->label($attributeLabels['name'].'<span class = "label-required">*</span>');
//        }
            ?>

    <?= $form->field($model, 'time')->dropDownList(OrganizationType::$getTime,['prompt'=>'请选择'])->label($attributeLabels['time'].'<span class = "label-required">*</span>'); ?>
    
    <?= $form->field($model, 'record_type')->dropDownList(OrganizationType::$getRecordType,['prompt'=>'请选择'])->label($attributeLabels['record_type'].'<span class = "label-required">*</span>'.'<span class = "label-required">（请选择该服务类型对应展示的病历类型）</span>'); ?>

    <?= $form->field($model, 'status')->dropDownList(OrganizationType::$getStatus,['prompt'=>'请选择'])->label($attributeLabels['status'].'<span class = "label-required">*</span>'); ?>


  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
