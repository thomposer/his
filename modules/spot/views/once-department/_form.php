<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\OnceDepartment */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="once-department-form col-md-12">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'placeholder' => '请输入科室名称'])->label($attributeLabels['name'].'<span class = "label-required">*</span>') ?>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    var baseUrl = '<?= Yii::$app->request->baseUrl ?>';
    require([baseUrl + '/public/js/lib/common.js']);
</script>
