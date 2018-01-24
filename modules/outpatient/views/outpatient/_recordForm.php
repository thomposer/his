<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\modules\triage\models\TriageInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\modules\outpatient\models\Outpatient;
use yii\helpers\Url;
use app\modules\outpatient\models\AllergyOutpatient;

/* @var $this yii\web\View */
/* @var $model app\modules\triage\models\TriageInfo */
/* @var $form ActiveForm */
$getTriageInfoModel = $model->getModel('triageInfo');
$getTriageInfoRelationModel = $model->getModel('triageInfoRelation');
$getOutpatientRelationModel = $model->getModel('outpatientRelation');
$allergyModel = AllergyOutpatient::findAllergyOutpatient($getTriageInfoModel->record_id);
if (isset($allergyModel[0]->id) && $allergyModel[0]->id) {
    $getOutpatientRelationModel->hasAllergy = 2;
}
$attribute = $getTriageInfoModel->attributeLabels();
$outpatientRelationModelAttribute = $getOutpatientRelationModel->attributeLabels();

$has_save = Yii::$app->request->get('has_save');
$state = $getTriageInfoModel->state;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$required = '';
if($reportResult['type_description'] != '方便门诊'){
    $required = '<span class = "label-required">*</span>';
}
?>
<div class="outpatient-_recordForm col-sm-12 col-md-12 patient-basic">

    <?php
        $form = ActiveForm::begin([
                    'options' => ['id' => 'recordForm'],
        ]);
    ?>
    <div class="row basic-form">
        <div class=" basic-header">
            <span class='basic-left-info'>
                病历信息
            </span>
            <span class='basic-right-up basic-right-up-case'>
                <div class='col-md-6 case-template-select'>
                    <?php if (($hasTemplateCase == 2 || $has_save == 1) && $state == 1): ?>
                        <span
                            class="edit_button"><?php echo Html::button('修改', ['class' => 'btn btn-default btn-form']) ?></span>
                        <span class="case_template_none">
                            <?php echo $form->field($getTriageInfoModel, 'template')->dropDownList(ArrayHelper::map(Outpatient::templateList(), 'id', 'name', 'group'), ['class' => 'form-control', 'style' => 'width:300px;margin-right:15px', 'prompt' => '- 选择病历模板 -'])->label(false) ?>
                        </span>
                    <?php else: ?>
                        <span class="case_template">
                            <?php echo $form->field($getTriageInfoModel, 'template')->dropDownList(ArrayHelper::map(Outpatient::templateList(), 'id', 'name', 'group'), ['class' => 'form-control', 'style' => 'width:300px;margin-right:15px', 'prompt' => '- 选择病历模板 -'])->label(false) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </span>
        </div>

        <div class="basic-form-content">
            <?= $form->field($getOutpatientRelationModel, 'chiefcomplaint')->textarea(['rows' => 4, 'id' => 'chiefcomplaint'])->label($outpatientRelationModelAttribute['chiefcomplaint'] . $required) ?>
            <?= $form->field($getOutpatientRelationModel, 'historypresent')->textarea(['rows' => 4, 'id' => 'historypresent'])->label($outpatientRelationModelAttribute['historypresent'] . $required) ?>
            <?= $form->field($getOutpatientRelationModel, 'pasthistory')->textarea(['rows' => 4, 'id' => 'pasthistory']) ?>

            <?= $form->field($getTriageInfoRelationModel, 'pastdraghistory')->textarea(['rows' => 4]) ?>

            <?php // $form->field($getTriageInfoModel, 'food_allergy')->textarea(['rows' => 4, 'id' => 'food_allergy', 'placeholder' => '请分别记录下过敏源、症状及发生时间'])->label($attribute['food_allergy'] . '<span class = "label-required">*</span>') ?>
            <?php // $form->field($getTriageInfoModel, 'meditation_allergy')->textarea(['rows' => 4, 'id' => 'meditation_allergy', 'placeholder' => '请分别记录下过敏源、症状及发生时间'])->label($attribute['meditation_allergy'] . '<span class = "label-required">*</span>') ?>

            <?= $this->render('_allergyForm', ['model' => $getOutpatientRelationModel, 'form' => $form]) ?>

            <?= $form->field($getOutpatientRelationModel, 'personalhistory')->textarea(['rows' => 4, 'id' => 'personalhistory']) ?>
            <?= $form->field($getOutpatientRelationModel, 'genetichistory')->textarea(['rows' => 4, 'id' => 'genetichistory']) ?>
            <?= $form->field($getOutpatientRelationModel, 'physical_examination')->textarea(['rows' => 4, 'id' => 'physical_examination'])->label($outpatientRelationModelAttribute['physical_examination'] . $required) ?>
            <?= $form->field($getTriageInfoModel, 'examination_check')->textarea(['rows' => 4, 'id' => 'examination_check',])->label($attribute['examination_check'], ['class' => 'examination_check_label']) ?>

            <div class="form-group">
                <!-- 初步诊断 -->
                <label class="control-label" for="checkrecord-check_id">初步诊断<span style="color:#FF5000;">（若需要给患者开检验，检查，治疗，处方医嘱，请务必填写初步诊断）</span></label>
                
                <?= $this->render('_firstCheckForm', ['model' => $getOutpatientRelationModel, 'form' => $form,'firstCheckDataProvider'=>$firstCheckDataProvider]) ?>
<!--                <div class = 'first-check-content'>
                    <?php // if ($firstCheckDataProvider): ?>
                        <?php // foreach ($firstCheckDataProvider as $v): ?>
                            <div class = 'first-check-list'>
                                <div class = 'check-name'><?= Html::encode($v['content']); ?></div>
                                <div class = 'check-id'>
                                    <input type="hidden" class="form-control" name="FirstCheck[deleted][]" value="">
                                    <?php// Html::hiddenInput('FirstCheck[check_id][]', Json::encode(array_merge($v, ['isNewRecord' => 0]), JSON_ERROR_NONE), ['class' => 'form-control']) ?>
                                </div>
                                <div class="op-group"><?php// Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png') ?></div>
                            </div>
                        <?php // endforeach; ?>
                    <?php // endif; ?>
                </div>-->

                <!-- 下拉选择 -->
<!--                <div class="first-check-input-container">
                    <select id ="select-first-check" class="form-control first-check-left">
                        <option value=1>ICD-10</option>
                        <option value=2>自定义</option>
                    </select>

                    <div class="first-check-right">
                        <select id="CheckCodeSel">
                            <option>请选择</option>
                        </select>
                        <div class="first-check-custom" style="display: none">
                            <div class="first-check-box">
                                <input id = "input-first-check-custom" class="form-control input-first-check" placeholder="请输入">
                            </div>
                            <button type="button" class="btn-default-status-success btn-first-check">添加</button>
                        </div>

                    </div>
                </div>-->
                
                
                
                
            </div>




            <?= $form->field($getTriageInfoModel, 'cure_idea')->textarea(['rows' => 4, 'id' => 'cure_idea'])->label($attribute['cure_idea'], ['class' => 'cure_idea_label']) ?>

            <?= $form->field($getTriageInfoRelationModel, 'followup')->textarea(['rows' => 4]) ?>



            <div>
                <label class="control-label" for="upload-mediaFile">上传附件</label>
                <?= $this->render('_fileUpload', ['model' => $getTriageInfoModel, 'medicalFile' => $medicalFile]) ?>
            </div>
            <div class="form-group">
                <?= Html::button('打印护理记录', ['class' => 'btn btn-default btn-form print-nursing-record', 'name' => 'nursing-record-' . Yii::$app->request->get('id'), 'style' => 'float:right;']); ?>
                <?php if (($hasTemplateCase == 2 || $has_save == 1) && (empty($getTriageInfoModel->errors)) && $state == 1): ?>
                    <?= Html::button('修改', ['class' => 'btn btn-default btn-form reocrd-btn-custom']).Html::button('修改', ['class' => 'btn btn-default btn-form reocrd-btn-custom btn-fixed']) ?>
                    <?= Html::button('打印病历', ['class' => 'btn btn-default btn-form print-record  print-check', 'name' => 'record' . Yii::$app->request->get('id') . 'myshow']); ?>
                <?php else: ?>
                    <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form reocrd-btn-custom']).Html::submitButton('保存', ['class' => 'btn btn-default btn-form reocrd-btn-custom btn-fixed']) ?>
                <?php endif; ?>

                <?php
                $options = [
                    'class' => 'btn-form btn-case-template btn',
                    'record_id' => $getTriageInfoModel->id,
                    'role' => 'modal-remote',
                    'data-toggle' => 'tooltip',
                    'data-modal-size' => 'large'
                ];
                if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientCreateTemplate'), $this->params['permList'])) {
                    echo Html::a('存为病历模板', Url::to(['', 'id' => $getTriageInfoModel->record_id, 'saveCase' => 1]), $options);
                }
                ?>

            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <div id='record-print' class="tab-pane hide">
    </div>
    <div id="nursing-record-print"  class="tab-pane hide">
    </div>
    <!--<div id="birth-info-print"  class="tab-pane hide"></div>-->
</div>
