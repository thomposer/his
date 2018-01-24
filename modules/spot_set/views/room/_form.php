<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot_set\models\Room;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\Room */
/* @var $form yii\widgets\ActiveForm */
$labels = $model->attributeLabels();
?>

<div class="room-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'clinic_name')->textInput(['maxlength' => 30])->label($labels['clinic_name'] . '<span class = "label-required">*</span>') ?>

    <?= $form->field($model, 'floor')->textInput()->label($labels['floor'] . '<span class = "label-required">*</span>') ?>

    <?= $form->field($model, 'clinic_type')->dropDownList(Room::$getClinicType,['prompt' => '请选择'])->label($labels['clinic_type'] . '<span class = "label-required">*</span>') ?>

    <?=
    $form->field($model, 'status')->dropDownList(Room::$getStatus,['prompt' => '请选择'])->label($labels['status'] . '<span class = "label-required">*</span>')
    ?>



    <div class="form-group">
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
