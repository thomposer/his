<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\RecipeList;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\RecipeList */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/spot/recipeList.css') ?>
<?php $this->endBlock() ?>
    <div class="recipe-list-form col-md-12">

        <?php $form = ActiveForm::begin([
            'id' => 'recipe-list-clinic'
        ]); ?>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'recipelist_id')->dropDownList(ArrayHelper::map($recipeList,'id','name'),['prompt' => '请选择处方医嘱名称','disabled'=>($model->isNewRecord?false:'true'),'maxlength' => true,'class'=>'select2 form-control', 'placeholder' => '请选择' . $attribute['name']])->label($attribute['name'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'drug_type')->dropDownList(RecipeList::$getDrugType, ['prompt' => '请选择', 'disabled' => 'true'])->label($attribute['drug_type'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'specification')->textInput(['maxlength' => true, 'disabled' => 'true'])->label($attribute['specification'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'type')->dropDownList(RecipeList::$getType, ['prompt' => '请选择', 'disabled' => 'true'])->label($attribute['type'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-12'>
                <?= $form->field($model, 'dose_unit')->checkboxList(RecipeList::$getDoseUnit, ['itemOptions' => ['labelOptions' => ['class' => 'recipe-list-form-label'], 'disabled' => 'true']])->label($attribute['dose_unit'] . '<span class = "label-required">*</span>'); ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'unit')->dropDownList(RecipeList::$getUnit, ['prompt' => '请选择', 'disabled' => 'true'])->label($attribute['unit'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'manufactor')->textInput(['maxlength' => true, 'disabled' => 'true'])->label($attribute['manufactor'] . '<span class = "label-required">*</span>') ?>
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
                    <?= $form->field($model, 'high_risk')->radioList(RecipeList::$getHighRiskStatus, ['class' => 'high-risk', 'itemOptions' => ['labelOptions' => ['class' => 'recipe-list-form-label'], 'disabled' => 'true']])->label($attribute['high_risk'] . '<span class = "label-required">*</span>') ?>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'price')->textInput(['maxlength' => true, 'placeholder' => '请输入' . $attribute['price']])->label($attribute['price'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($model, 'default_price')->textInput(['maxlength' => true, 'placeholder' => '请输入' . $attribute['default_price']]) ?>
            </div>
        </div>
        <div class="row">
            <div class='col-md-6'>
                <?= $form->field($model, 'address')->dropDownList(RecipeList::$getAddress, ['prompt' => '请选择'])->label($attribute['address'] . '<span class = "label-required">*</span>') ?>
            </div>

            <div class='col-md-2'>
                <?= $form->field($model, 'shelves1')->textInput(['maxlength'=>true,'placeholder'=>'0-999']); ?>
            </div>
            <div class="recipe-list-line-1">-</div>
            <div class='col-md-2 recipe-list-line-shelves'>

                <?= $form->field($model, 'shelves2')->textInput(['maxlength'=>true,'placeholder'=>'0-999'])->label(false);?>
            </div>
            <div class="recipe-list-line-2 ">-</div>
            <div class='col-md-2 recipe-list-line-shelves'>

                <?= $form->field($model, 'shelves3')->textInput(['maxlength'=>true,'placeholder'=>'0-999'])->label(false); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
