<?php

use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use yii\helpers\ArrayHelper;
use app\modules\triage\models\TriageInfo;
use yii\helpers\Json;
$getTriageInfoModel = $model->getModel('triageInfo');
$getTriageInfoRelationModel = $model->getModel('triageInfoRelation');
$getOutpatientRelationModel = $model->getModel('outpatientRelation');
$attribute = $getTriageInfoModel->attributeLabels();
$outpatientRelationAttributes = $getOutpatientRelationModel->attributeLabels();

?>
<?php
$form = ActiveForm::begin([
            'id' => '_recordInfo'
        ]);
?>
<div class='row'>
    <div class="family-modal">
        <div class='row'>
            <div class='col-md-12'>
                <?=
                $form->field($getTriageInfoModel, 'morbidityDate')->widget(
                        DatePicker::className(), [
                    'inline' => false,
                    'language' => 'zh-CN',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ]
                ])->label($attribute['incidence_date'])
                ?>
            </div>

        </div>
        <div class = 'row'>
            <div class = 'col-sm-12'>
                <?= $form->field($getOutpatientRelationModel, 'chiefcomplaint')->textarea(['rows' => 4, 'id' => 'chiefcomplaint'])->label($outpatientRelationAttributes['chiefcomplaint'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>

        <div class = 'row'>
            <div class = 'col-sm-12'>
                <?= $form->field($getOutpatientRelationModel, 'historypresent')->textarea(['rows' => 4, 'id' => 'historypresent']) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-12'>
                <?= $form->field($getOutpatientRelationModel, 'pasthistory')->textarea(['rows' => 4, 'id' => 'pasthistory']) ?>
            </div>
        </div>
        <?= $form->field($getTriageInfoRelationModel, 'pastdraghistory')->textarea(['rows' => 4]) ?>
        <?= $form->field($getTriageInfoModel, 'food_allergy')->textarea(['rows' => 4, 'id' => 'food_allergy','placeholder' => '请分别记录下过敏源、症状及发生时间']) ?>
        <?= $form->field($getTriageInfoModel, 'meditation_allergy')->textarea(['rows' => 4, 'id' => 'meditation_allergy','placeholder' => '请分别记录下过敏源、症状及发生时间']) ?>
        <?= $form->field($getOutpatientRelationModel, 'personalhistory')->textarea(['rows' => 4, 'id' => 'personalhistory']) ?>
        <?= $form->field($getOutpatientRelationModel, 'genetichistory')->textarea(['rows' => 4, 'id' => 'genetichistory']) ?>
        <?= $form->field($getOutpatientRelationModel, 'physical_examination')->textarea(['rows' => 4, 'id' => 'physical_examination']) ?>
        <?= $form->field($getTriageInfoModel, 'examination_check')->textarea(['rows' => 4, 'id' => 'examination_check']) ?>
        <?= $form->field($getTriageInfoModel, 'first_check')->textarea(['rows' => 4, 'id' => 'first_check']) ?>
        <?= $form->field($getTriageInfoModel, 'cure_idea')->textarea(['rows' => 4, 'id' => 'cure_idea']) ?>
        <?= $form->field($getTriageInfoRelationModel, 'followup')->textarea(['rows' => 4]) ?>
        <?= $form->field($getOutpatientRelationModel, 'remark')->textarea(['rows' => 4, 'id' => 'remark']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
