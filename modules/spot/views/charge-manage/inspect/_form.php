<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot\models\Tag;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Inspect */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();

?>
<?php
$css = <<<CSS
#inspect-deliver label{
    padding-left: 25%;
}
.field-inspect-deliver .help-block{
    float: left;
}
CSS;
$this->registerCss($css);
?>

<div class="inspect-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>

    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'inspect_name')->textInput(['placeholder'=>'请填写名称，不超过100字'])->label($attribute['inspect_name'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'inspect_unit')->textInput() ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'inspect_english_name')->textInput(['placeholder'=>'请填写英文名，不超过100字'])->label($attribute['inspect_english_name']) ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'phonetic')->textInput() ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'international_code')->textInput() ?>
        </div>
        <div class = 'col-md-6'>
             <?= $form->field($model, 'remark')->textInput() ?>
        </div>
    </div>

    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'unionSpotId')->checkboxList(\yii\helpers\ArrayHelper::map($spotList,'id','spot_name'))->label($attribute['unionSpotId']. '<span class = "label-required">*</span>'); ?>
        </div>

    </div>


    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'tag_id')->dropDownList(array_column(Tag::getTagList(['id','name'],['type' => 1]), 'name','id'),['prompt' => '请选择充值卡折扣标签']) ?>
        </div>
        <div class = 'col-md-6'>
            <?php // $form->field($model, 'specimen_type')->dropDownList($model::$getSpecimenType,['prompt' => '请选择']) ?>
        </div>
    </div>
<!--    <div class="row">
        <div class="col-md-12">
            <?php // $form->field($model, 'deliver')->radioList($model::$getDeliver,['class'=>'col-md-9'])->label($attribute['deliver']. '<span class = "label-required">*</span>',['style'=>'float:left;']) ?>
        </div>
    </div>-->
    <div class="row">
        <div class="col-md-6">
            <?php // $form->field($model, 'cuvette')->dropDownList($model::$getCuvette,['prompt' => '请选择']) ?>
        </div>
        <div class="col-md-6">
           <?php // $form->field($model, 'inspect_type')->textInput(['maxlength' => 15,'placeholder' => '请输入'.$attribute['inspect_type']]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::a('取消', ['inspect-index'], ['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
