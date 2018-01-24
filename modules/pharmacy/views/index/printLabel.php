<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/check/common.css');
AppAsset::addCss($this, '@web/public/css/lib/search.css');
?>

<div class="charge-record-form">
    <div class = 'cost-bg'>
        <h5 class = 'title'>请选择您要打印标签的药品</h5>
    </div>
    <?php
    $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal common'],
                'id' => 'printLabelForm'
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'recipeList')->checkboxList(ArrayHelper::map($recipeList, 'id', 'name'),['value'=>  array_column($recipeList, 'id')])->label(false); ?>
        </div>
    </div>
     <div class = 'modal-footer text-center'>
        <div class = 'form-group'>
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) ?>
            <?= Html::submitButton('打印', ['class' => 'btn btn-default btn-form']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    var inspectList = <?= json_encode($inspectList, true) ?>;
    require(["<?= $baseUrl ?>" + "/public/js/pharmacy/printLabel.js"], function (main) {
        main.init();
    });
</script>