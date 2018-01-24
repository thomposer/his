<?php
use app\modules\outpatient\models\AllergyOutpatient;


$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$allergyModel = AllergyOutpatient::findAllergyOutpatient($model->record_id);
if (isset($allergyModel[0]->id) && $allergyModel[0]->id) {
    $model->hasAllergy = 2;
}else{
    $model->hasAllergy = 1;
}
$length = count($allergyModel);
?>

<div class="allergy-form">
<div class="row">
    <div class="col-sm-12">
        <?= $form->field($model, 'hasAllergy')->radioList(['1' => '无', '2' => '有'], ['itemOptions' => ['class' => 'have-allergy'],'class'=>'has-allergy-label'])->label('过敏史<span class = "label-required">*</span>') ?>
    </div>

</div>
<div class="row">
    <div class="col-sm-11 allergy-content" style="<?= $model->hasAllergy == 1 ? 'display:none;' : 'display:block;'?> margin-left: 85px;">
        <?php foreach ($allergyModel as $key => $allergyOutpatient): ?>
            <div class = 'row allergy-line'>
                <div class = 'col-sm-3 select-allery'>
                    <?= $form->field($allergyOutpatient, 'type')->dropDownList(AllergyOutpatient::$getAllergyType, ['prompt' => '请选择过敏类型', 'name' => 'allergyOutpatient[type][]'])->label(false) ?>
                </div>
                <div class = 'col-sm-4 allery-input'>
                    <?= $form->field($allergyOutpatient, 'allergy_content')->textInput(['placeholder' => '请填写引起过敏的食物或者物品的名称', 'maxlength' => 255, 'name' => 'allergyOutpatient[allergy_content][]'])->label(false) ?>
                </div>
                <div class = 'col-sm-3' style = 'width:22%;padding-left:0px;padding-right:0;margin-top: 5px;'>
                    <?= $form->field($allergyOutpatient, 'allergy_degree')->radioList(AllergyOutpatient::$getAllergyDegreeItems, ['name' => 'allergyOutpatient[allergy_degree][' . $key . ']'])->label(false) ?>
                </div>
                <div class = 'col-sm-2 allergy-line-button'>
                    <a href="javascript:void(0);" class="btn-from-delete-add btn allergy-delete" style="display: inline-block;">
                        <i class="fa fa-minus"></i>
                    </a>
                    <a href="javascript:void(0);" class="btn-from-delete-add btn allergy-add" style="display: <?= ($key == ($length - 1) ) ? 'inline-block' : 'none' ?>;"  data-key="<?= $key ?>">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</div>