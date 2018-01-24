<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$attribute = $model->attributeLabels();
/* @var $this yii\web\View */
/* @var $model app\modules\medicine\models\MedicineDescription */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="medicine-description-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'chinese_name')->textarea(['rows' => 6])->label($attribute['chinese_name'].'<span class = "label-required">*</span>') ?>

    <?= $form->field($model, 'english_name')->textarea(['rows' => 6])->label($attribute['english_name'].'<span class = "label-required">*</span>') ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
