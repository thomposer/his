<?php

use app\modules\spot\models\Tag;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
$attribute = $model -> attributeLabels();
?>
<?php AppAsset ::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<div class="clinic-cure-form col-md-12">

    <?php $form = ActiveForm ::begin(); ?>

    <div class="row">
        <div class="col-md-6 cure-record-form">
            <?php
                if(!$model->isNewRecord){
                    echo $form -> field($model, 'name')->textInput(['disabled' => true]);
                }else{
                    echo $form -> field($model, 'cure_id') -> dropDownList(ArrayHelper ::map($parentCureList, 'id', 'name'), ['prompt' => '请选择治疗医嘱名称', 'class' => 'form-control select2', 'style' => 'width:100%']);
                }
            ?>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-6'>
            <?= $form -> field($model, 'unit') -> textInput(['maxlength' => true, "disabled" => '']) ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'meta') -> textInput(['maxlength' => true, "disabled" => '']) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-6'>
            <?= $form -> field($model, 'international_code') -> textInput(['maxlength' => true, "disabled" => '']) ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'remark') -> textInput(["disabled" => '']) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-6'>
            <?= $form -> field($model, 'tag_name') -> textInput(["disabled" => '']) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-6'>
            <?= $form -> field($model, 'price') -> textInput()->label($attribute['price'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'default_price') -> textInput() ?>
        </div>
    </div>
    <?php ActiveForm ::end(); ?>

</div>
<script>
    var cureList = <?= json_encode($parentCureList, true) ?>;
    var error = '<?= $model->errors?1:0 ?>';
    require([baseUrl + "/public/js/spot_set/clinic_cure.js"], function (main) {
        main.init();
    });
</script>