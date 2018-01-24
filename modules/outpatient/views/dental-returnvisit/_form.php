<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\DentalFirstTemplate */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>

<div class="dental-first-template-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attribute['name'].'，不能超过64个字'])->label($attribute['name'] . '<span class = "label-required">*</span>') ?>

	<?= $form->field($model, 'returnvisit')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'oral_check')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'auxiliary_check')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'diagnosis')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cure_plan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cure')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'advice')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>



    <div class="form-group">
        <?= Html::a('取消',['dentalreturnvisit-index'],['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
