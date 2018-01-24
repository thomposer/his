<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
$attributes = $model->attributeLabels();
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\CheckListClinic */
/* @var $form yii\widgets\ActiveForm */
$disabled=  isset($model->id)&&$model->id?true:false;
?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>
<div class="check-list-clinic-form col-md-12">

    <?php $form = ActiveForm::begin(); ?>

    <div class = 'row'>
        <div class = 'col-md-6'>
            <?php
                echo $form->field($model, 'check_id')->dropDownList(ArrayHelper::map($checkList, 'id', 'name'), [
                    'prompt' => '请选择影像学检查',
                    'class' => 'form-control select2',
                    'style' => 'width:100%;',
                    'disabled' => $disabled
                ]);
            ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'unit')->textInput(['maxlength' => true,'readonly' => true]) ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'meta')->textInput(['maxlength' => true,'readonly' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'international_code')->textInput(['maxlength' => true,'readonly' => true]) ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'tagName')->textInput(['maxlength' => true,'readonly' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'remark')->textInput(['maxlength' => true,'readonly' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'price')->textInput(['maxlength' => true])->label($attributes['price'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'default_price')->textInput() ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    var baseUrl = '<?= Yii::$app->request->baseUrl ?>';
    var checkList = <?= json_encode($checkList,true) ?>;
    require([baseUrl + '/public/js/spot_set/checkListClinic.js'], function (main) {
        main.init();
    });
</script>
