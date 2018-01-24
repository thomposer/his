<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\CureList;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\spot\models\Tag;
use app\assets\AppAsset;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\RecipeList */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/spot/recipeList.css') ?>
<?php $this->endBlock() ?>
<div class="recipe-list-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>
    <div class="module-title">
        <div class='row'>
            <div class="col-sm-6">
                <span class="module-title-adorn"></span><span class="module-title-content">基本信息</span>
            </div>
        </div>
    </div>
    <div class="module-content">
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => '请填写药品名，不超过20个字'])->label($attribute['name'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'product_name')->textInput(['maxlength' => true, 'placeholder' => '请填写商品名，不超过30个字']) ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'en_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'drug_type')->dropDownList(RecipeList::$getDrugType, ['prompt' => '请选择'])->label($attribute['drug_type'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'specification')->textInput(['maxlength' => true, 'placeholder' => '请填写规格，不超过15个字'])->label($attribute['specification'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'type')->dropDownList(RecipeList::$getType, ['prompt' => '请选择'])->label($attribute['type'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-12'>
                <?= $form->field($model, 'dose_unit')->checkboxList(RecipeList::$getDoseUnit, ['itemOptions' => ['labelOptions' => ['class' => 'recipe-list-form-label']]])->label($attribute['dose_unit'] . '<span class = "label-required">*</span>'); ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'unit')->dropDownList(RecipeList::$getUnit, ['prompt' => '请选择'])->label($attribute['unit'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'insurance')->dropDownList(RecipeList::$getInsurance, ['prompt' => '请选择']) ?>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'manufactor')->textInput(['maxlength' => true])->label($attribute['manufactor'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'app_number')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'import_regist_no')->textInput(['maxlength' => true]) ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'international_code')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'meta')->textInput(['maxlength' => true]) ?>
            </div>
            <div class='col-md-4'>
                <?= $form->field($model, 'medicine_description_id')->dropDownList(ArrayHelper::map($medicineDescription, 'id', 'chinese_name'), ['prompt' => '请选择', 'class' => 'form-control select2', 'style' => 'width:100%']) ?>
            </div>
            <div class='col-md-2'>
                <label class="control-label" for="recipelist-medicine_description_id"
                       style="display: block">&nbsp;</label>
                       <?= Html::button('预览', ['disabled' => true, 'class' => 'btn btn-cancel btn-form disabled review', 'style' => 'margin-top:0px', 'data-url' => Url::to(['@apiMedicineDescriptionItem']), 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']) ?>
            </div>

        </div>

        <div class='row'>
            <div class='col-md-12'>
                <?= $form->field($model, 'remark')->textarea(['placeholder' => '请填写用药须知，不超过100个字']) ?>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-12'>
                <?= $form->field($model, 'unionSpotId')->checkboxList(ArrayHelper::map($spotList, 'id', 'spot_name'))->label($attribute['unionSpotId'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
    </div>

    <div class="module-title">
        <div class='row'>
            <div class="col-sm-6">
                <span class="module-title-adorn"></span><span class="module-title-content">药品安全</span>
            </div>
        </div>
    </div>

    <div class="module-content">
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'high_risk')->radioList(RecipeList::$getHighRiskStatus, ['class' => 'high-risk', 'itemOptions' => ['labelOptions' => ['class' => 'recipe-list-form-label']]])->label($attribute['high_risk'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
    </div>


    <div class="module-title">
        <div class='row'>
            <div class="col-sm-6">
                <span class="module-title-adorn"></span><span class="module-title-content">皮试信息</span>
            </div>
        </div>
    </div>

    <div class="module-content">
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'skin_test_status')->dropDownList(RecipeList::$getSkinTestStatus) ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'skin_test')->textInput(['maxlength' => 64]) ?>
            </div>
        </div>
    </div>

    <div class="module-title">
        <div class='row'>
            <div class="col-sm-6">
                <span class="module-title-adorn"></span><span class="module-title-content">默认用法用量</span>
            </div>
        </div>
    </div>

    <div class="module-content">
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'default_used')->dropDownList(RecipeList::$getDefaultUsed, ['prompt' => '请选择'])->label($attribute['default_used']) ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'default_consumption')->dropDownList(RecipeList::$getDefaultConsumption, ['prompt' => '请选择'])->label($attribute['default_frequency']) ?>
            </div>
        </div>
    </div>
    <div class="module-title">
        <div class='row'>
            <div class="col-sm-6">
                <span class="module-title-adorn"></span><span class="module-title-content">药品标签</span>
            </div>
        </div>
    </div>
    <div class = 'module-content'>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'tag_id')->dropDownList(array_column(Tag::getTagList(['id', 'name'], ['type' => 1]), 'name', 'id'), ['prompt' => '请选择']) ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-12'>
                <?= $form->field($model, 'adviceTagId')->checkboxList(ArrayHelper::map($commonTagList, 'id', 'name'))->label($attribute['adviceTagId'] . '<span class = "title-label">（最多关联5个）</span>') ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <?= Html::a('取消', ['recipe-index'], ['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>