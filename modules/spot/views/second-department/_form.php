<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot\models\SecondDepartment;
use yii\helpers\ArrayHelper;
$attributeLabels = $model->attributeLabels();
?>

<div class="second-department-form col-md-12">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'placeholder' => '请输入科室名称'])->label($attributeLabels['name'].'<span class = "label-required">*</span>') ?>
    <div class = 'row'>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map($onceDepartmentInfo, 'id', 'name'),['prompt' => '请选择'])->label($attributeLabels['parent_id'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'status')->dropDownList(SecondDepartment::$getStatus,['prompt' => '请选择'])->label($attributeLabels['status'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'room_type')->dropDownList(SecondDepartment::$getRoomType,['prompt' => '请选择']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    var baseUrl = '<?= Yii::$app->request->baseUrl ?>';
    require([baseUrl + '/public/js/lib/common.js']);
</script>
