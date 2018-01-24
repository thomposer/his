<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\triage\models\TriageInfo;
use yii\widgets\Pjax;
use app\modules\outpatient\models\AllergyOutpatient;

$attribute = $model->attributeLabels();
$allergyModel=$model->patientAllergy;
if(!empty($allergyModel)){
    $model->hasAllergy=2;
}
?>

<?php
$form = ActiveForm::begin([
            'id' => 'basic-allergy',
            'options' => ['data' => ['pjax' => true]],
        ]);
?>
<div class='row basic-form patient-form-top'>
    <div class=" basic-header">
        <span class = 'basic-left-info'>
            过敏史
        </span>
    </div>
    <div class="basic-form-content basic-form-content-allergy">
        <div class="row">
    <div class="col-sm-12">
        <?= $form->field($model, 'hasAllergy')->radioList(['1' => '无', '2' => '有'], ['itemOptions' => ['class' => 'have-allergy'],'class'=>'has-allergy-label'])->label(false) ?>
    </div>

</div>
<div class="allergy-content" style="<?php echo $model->hasAllergy == 1 ? 'display:none' : 'display:block' ?>">
    <?php if(!empty($allergyModel)): ?>
    <?php foreach ($allergyModel as $key => $allergyOutpatient): ?>
        <div class = 'row allergy-line'>
            <div class = 'col-sm-3'>
                <?= $form->field($allergyOutpatient, 'type')->dropDownList(AllergyOutpatient::$getAllergyType, ['prompt' => '请选择过敏类型', 'name' => 'allergyOutpatient[type][]'])->label(false) ?>
            </div>
            <div class = 'col-sm-4' style="padding-left: 0;">
                <?= $form->field($allergyOutpatient, 'allergy_content')->textInput(['placeholder' => '请填写引起过敏的食物或者物品的名称', 'maxlength' => 255, 'name' => 'allergyOutpatient[allergy_content][]'])->label(false) ?>
            </div>
            <div class = 'col-sm-3' style="padding-left: 5px;margin-top: 5px;">
                <?= $form->field($allergyOutpatient, 'allergy_degree')->radioList(AllergyOutpatient::$getAllergyDegreeItems, ['name' => 'allergyOutpatient[allergy_degree][' . $key . ']'])->label(false) ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php endif;?>
</div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php $this->registerJs("
    $('#other-pjax .form-control').attr({'disabled': true});
") ?>

