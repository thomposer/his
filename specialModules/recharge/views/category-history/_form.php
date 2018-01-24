<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot\models\CardRechargeCategory;
use dosamigos\datetimepicker\DateTimePickerAsset;

DateTimePickerAsset::register($this)->js[] = 'js/locales/bootstrap-datetimepicker.zh-CN.js';

/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CategoryHistory */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
$attribute = $model->attributeLabels();
?>

<div class="category-history-form col-md-12">

<?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group field-categoryhistory-f_change_reason">
                <label class="control-label" for="categoryhistory-f_change_reason">目前卡种：</label>
<?= Html::encode(CardRechargeCategory::getCategoryById($cardModel->f_category_id)['f_category_name']) ?>
            </div>       
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
<?= $form->field($model, 'f_end_category')->dropDownList($cardCategory, ['prompt' => '请选择', 'class' => 'form-control select2', 'style' => 'width:100%', 'options' => [$cardModel->f_category_id => ['disabled' => true]]])->label('选择卡种') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
<?= $form->field($model, 'f_change_reason')->textarea(['maxlength' => 50, 'rows' => 5, 'placeholder' => '请填写' . $attribute['f_change_reason'], 'maxlength' => true]) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
    <div class="card-service-container">

    </div>
</div>
<?php
$js = <<<JS
    var serviceJson=$cardService;
    require(["$baseUrl" + '/public/js/recharge/create.js?v=' + '<?= $versionNumber ?>'], function (main) {
        main.init();
    })
JS;
$this->registerJs($js);
?>

