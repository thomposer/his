<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
$labels = $model->attributeLabels();
//$inspectList=  array_values($inspectList);

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\InspectClinic */
/* @var $form yii\widgets\ActiveForm */
$disabled=  isset($model->id)&&$model->id?true:false;
?>
<?php
$css = <<<CSS
.field-inspectclinic-deliver .help-block{
   clear:both;
}
#inspectclinic-item label{
    width:100%;
}
CSS;
$this->registerCss($css);
?>

<div class="inspect-clinic-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <div class="row">
        <div class = 'col-md-6'>
            <?= $form->field($model, 'inspect_id')->dropDownList(ArrayHelper::map($inspectList, 'id', 'name'),['prompt' => '请选择实验室检查', 'class' => 'form-control select2', 'style' => 'width:100%','disabled'=>$disabled])->label('实验室检查') ?>
        </div>
    </div>
    <div class="row">
        <div class = 'col-md-6'>
            <?= $form->field($model, 'englishName')->textInput(['readonly'=>true])->label($labels['englishName']) ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'inspectUnit')->textInput(['readonly'=>true])->label($labels['inspectUnit']) ?>
        </div>
        
    </div>
    <div class="row">
        <div class = 'col-md-6'>
            <?= $form->field($model, 'phonetic')->textInput(['readonly'=>true])->label($labels['phonetic']) ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'internationalCode')->textInput(['readonly'=>true])->label($labels['internationalCode']) ?>
        </div>
        
    </div>
    <div class="row">
        <div class = 'col-md-6'>
            <?= $form->field($model, 'tagId')->textInput(['readonly'=>true])->label($labels['tagId']) ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'doctorRemark')->textInput(['readonly'=>true])->label($labels['doctorRemark']) ?>
        </div>
        
    </div>
    <div class="row">
        <div class = 'col-md-6'>
            <?= $form->field($model, 'parentStatus')->textInput(['readonly'=>true])->label($labels['parentStatus']) ?>
        </div>
    </div>
    <div class="row">
         <div class = 'col-md-6'>
            <?= $form->field($model, 'inspect_price')->textInput(['maxlength' => true])->label($labels['inspect_price'].'<span class="label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'cost_price')->textInput(['maxlength' => true])->label($labels['cost_price']) ?>
        </div>
    </div>
    <div class="row">
        <div class = 'col-md-6'>
            <?= $form->field($model, 'specimen_type')->dropDownList($model::$getSpecimenType,['prompt' => '请选择'])->label($labels['specimen_type']. '<span class = "label-required">*</span>') ?>
        </div>
         
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'deliver')->radioList($model::$getDeliver)->label($labels['deliver']. '<span class = "label-required">*</span>') ?>
        </div>
        <?php 
                $hide=$model->deliver==1?"":' hide';
            ?>
        <div class = 'col-md-6 show_hide <?= $hide ?>'>
            <?= $form->field($model, 'deliver_organization')->dropDownList($model::$getDeliverOrganization,['prompt' => '请选择'])->label($labels['deliver_organization'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'inspect_type')->textInput(['maxlength' => true,'placeholder' => '请输入'.$labels['inspect_type']]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'cuvette')->dropDownList($model::$getCuvette,['prompt' => '请选择']) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'description')->textInput(['maxlength' => true,'placeholder' => '指示（试管，标本量，保存稳定性等），不超过100字']) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'remark')->textInput(['maxlength' => true,'placeholder' => '结果出具时间，不超过100字']) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'item')->checkboxList(ArrayHelper::map($itemList, 'id', 'item_name'),['itemOptions' => ['labelOptions' => ['class' => 'recipe-list-form-label']]])->label($labels['item'].'<span class = "label-required">*</span>'); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs("
                $('#inspectclinic-inspect_id').select2({language:'zh-CN'});
")
?>