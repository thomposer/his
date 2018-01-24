<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\outpatient\models\ChildExaminationCheck;
use app\modules\outpatient\models\ChildExaminationAssessment;
use yii\helpers\ArrayHelper;
use app\modules\outpatient\models\Outpatient;
use app\modules\outpatient\models\ChildExaminationInfo;
use app\modules\spot\models\CheckCode;
use app\modules\outpatient\models\FirstCheck;

//$basicModel = $childMultiModel->getModel('basic');//基本信息
$checkModel = $childMultiModel->getModel('check');//体格检查
$growthModel = $childMultiModel->getModel('growth');//生长评估
$assessmentModel = $childMultiModel->getModel('assessment');//发育评估
$infoModel = $childMultiModel->getModel('info');//其他信息

//$basicModelAttributes = $basicModel->attributeLabels();
$checkModelAttributes = $checkModel->attributeLabels();
$growthModelAttributes = $growthModel->attributeLabels();
$infoModelAttributes = $infoModel->attributeLabels();
$assessmentModelAttributes = $assessmentModel->attributeLabels();  


$data = CheckCode::getData();
if($data){
    $childForm['count'] = FirstCheck::getCount($growthModel->record_id);
    $childForm['check_code_id'] = $data['id'];
    $childForm['name'] = $data['name'];
    $childForm['content'] = $data['name'] . '(' . $data['help_code'] .')' . '(' . $data['major_code'] .')';
}
/* @var $this yii\web\View */
/* @var $checkModel app\modules\outpatient\models\ChildExaminationBasic */
/* @var $form ActiveForm */
?>
    <div class="outpatient-_childForm col-sm-12 col-md-12">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'form-horizontal', 'id' => 'childForm'],
            ]);
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="testYears text-right">
                        <label class="control-label">测评年龄</label>
                    </div>
                    <div class="col-xs-6 col-sm-6">
                        <label class="control-label"><?= $triageInfo['ageAssessment'] ?></label>
                    </div>
                </div>
            </div>
        </div>
        <?= $this->render('_allergyForm', ['model' => $growthModel, 'form' => $form]) ?>
        <div class="form-group child-form-first-check-form">
                <label class="control-label" for="checkrecord-check_id">初步诊断<span style="color:#FF5000;">（若需要给患者开检验，检查，治疗，处方医嘱，请务必填写初步诊断）</span></label>
                <?= $this->render('_firstCheckForm', ['firstCheckDataProvider'=>$firstCheckDataProvider,'form' => $form, 'childForm' => $childForm]) ?>
                <!-- 下拉选择 -->
        </div>
        <div class='row title-patient-div'>
            <div class='col-sm-12'>
                <p class="titleP">
                    <span class="circleSpan"></span>
                    <span class="titleSpan">生长评估</span>
                </p>
            </div>
        </div>

        <div class="row growth-evaluation">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="labelWidth text-right percentage-label-width">
                        <label class="control-label" for="childexaminationinfo-sleep">身长或身高(cm)：</label>
                    </div>
                    <div class="col-xs-9 col-sm-9 childInput"><input type="hidden" name="ChildExaminationGrowth[result]" value="">
                        <div>
                            <label class="add-padding">第 <?= $triageInfo['percentageArr']['heightcm']?$triageInfo['percentageArr']['heightcm']:"--" ?> 百分位</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="labelWidth text-right percentage-label-width">
                        <label class="control-label" for="childexaminationinfo-sleep">体重(kg)：</label>
                    </div>
                    <div class="col-xs-9 col-sm-9 childInput"><input type="hidden" name="ChildExaminationGrowth[result]" value="">
                        <div>
                            <label class="add-padding">第 <?= $triageInfo['percentageArr']['weightkg']?$triageInfo['percentageArr']['weightkg']:"--" ?> 百分位</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row growth-evaluation">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="labelWidth text-right percentage-label-width">
                        <label class="control-label" for="childexaminationinfo-sleep">头围(cm)：</label>
                    </div>
                    <div class="col-xs-9 col-sm-9 childInput"><input type="hidden" name="ChildExaminationGrowth[result]" value="">
                        <div>
                            <label class="add-padding">第 <?= $triageInfo['percentageArr']['head_circumference']?$triageInfo['percentageArr']['head_circumference']:"--" ?> 百分位</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="labelWidth text-right percentage-label-width">
                        <label class="control-label" for="childexaminationinfo-sleep">BMI(kg/m<sup>2</sup>)：</label>
                    </div>
                    <div class="col-xs-9 col-sm-9 childInput"><input type="hidden" name="ChildExaminationGrowth[result]" value="">
                        <div>
                            <label class="add-padding">第 <?= $triageInfo['percentageArr']['bmi']?$triageInfo['percentageArr']['bmi']:"--" ?> 百分位</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
            $form->fieldConfig = [
                'template' => "<div class='labelWidth text-right'>{label}</div><div class='col-xs-9 col-sm-9 childInput'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'><div class = 'col-xs-2 widthLeft childForm-input-error-tips'></div><div class = 'col-xs-2 widthRightError'>{error}</div></div>",
            ];
        ?>
        
        
        <div class="row growth-evaluation">
            <div class='col-md-6 childexaminationgrowth-result'>
                <?= $form->field($growthModel, 'result')->radioList(ChildExaminationAssessment::$getSummary)->label($attribute['result']) ?>
            </div>
        </div>

        
        <div class='row title-patient-div'>
            <div class='col-sm-12'>
                <p class="titleP">
                    <span class="circleSpan"></span>
                    <span class="titleSpan">睡眠及大小便</span>
                </p>
            </div>
        </div>

        <div class='row sleep-defecation'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'sleep')->textarea(['maxlength' => 100, 'rows' => 3, 'placeholder' => '请输入' . $infoModelAttributes['sleep']]) ?>
            </div>
        </div>
        
        <div class='row sleep-defecation'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'shit')->textarea(['maxlength' => 100, 'rows' => 3, 'placeholder' => '请输入' . $infoModelAttributes['shit']]) ?>
            </div>
        </div>
        
        <div class='row sleep-defecation'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'pee')->textarea(['maxlength' => 100, 'rows' => 3, 'placeholder' => '请输入' . $infoModelAttributes['pee']]) ?>
            </div>
        </div>

        <div class='row title-patient-div'>
            <div class='col-sm-12'>
                <p class="titleP">
                    <span class="circleSpan"></span>
                    <span class="titleSpan">体格检查</span>
                </p>
            </div>
        </div>
        <div class="row" id="checkDiv">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="testYears text-right">
                        <label class="control-label">体格检查</label>
                    </div>
                    <div class="col-xs-8 col-sm-8 check-padding-left">
                        <label
                            class="control-label"><?= Html::checkbox('checkSelect', '', ['id' => 'checkSelect']) . '无特殊（全选框）' ?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class='checkContent'>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'appearance')->radioList(ChildExaminationCheck::$getType)->label($attribute['appearance']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'appearance_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['appearance_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'skin')->radioList(ChildExaminationCheck::$getType)->label($attribute['skin']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'skin_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['skin_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'headFace')->radioList(ChildExaminationCheck::$getType)->label($attribute['headFace']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'headFace_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['headFace_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'eye')->radioList(ChildExaminationCheck::$getType)->label($attribute['eye']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'eye_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['eye_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'ear')->radioList(ChildExaminationCheck::$getType)->label($attribute['ear']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'ear_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['ear_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'nose')->radioList(ChildExaminationCheck::$getType)->label($attribute['nose']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'nose_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['nose_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'throat')->radioList(ChildExaminationCheck::$getType)->label($attribute['throat']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'throat_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['throat_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'tooth')->radioList(ChildExaminationCheck::$getType)->label($attribute['tooth']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'tooth_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['tooth_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'chest')->radioList(ChildExaminationCheck::$getType)->label($attribute['chest']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'chest_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['chest_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'bellows')->radioList(ChildExaminationCheck::$getType)->label($attribute['bellows']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'bellows_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['bellows_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'cardiovascular')->radioList(ChildExaminationCheck::$getType)->label($attribute['cardiovascular']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'cardiovascular_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['cardiovascular_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'belly')->radioList(ChildExaminationCheck::$getType)->label($attribute['belly']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'belly_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['belly_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'genitals')->radioList(ChildExaminationCheck::$getType)->label($attribute['genitals']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'genitals_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['genitals_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'back')->radioList(ChildExaminationCheck::$getType)->label($attribute['back']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'back_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['back_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'limb')->radioList(ChildExaminationCheck::$getType)->label($attribute['limb']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'limb_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['limb_remark']]) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-5 select-option'>
                    <?= $form->field($checkModel, 'nerve')->radioList(ChildExaminationCheck::$getType)->label($attribute['nerve']) ?>
                </div>
                <div class='col-sm-7 option-remark'>
                    <?= $form->field($checkModel, 'nerve_remark')->textInput(['maxlength' => 64, 'placeholder' => '请输入' . $checkModelAttributes['nerve_remark']]) ?>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-sm-12'>
                <div class="text-center">
                    <?= Html::button('收起所有项<i class="fa fa-angle-up pull-right angle-icon"></i>', ['class' => 'btn check-close']) ?>
                </div>
            </div>
        </div>

        
        <div class='row title-patient-div'>
            <div class='col-sm-12'>
                <p class="titleP">
                    <span class="circleSpan"></span>
                    <span class="titleSpan">视力与听力</span>
                </p>
            </div>
        </div>

        <div class='row vision-hearing'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'visula_check')->textarea(['maxlength' => 100, 'rows' => 3, 'placeholder' => '请输入' . $infoModelAttributes['visula_check']]) ?>
            </div>
        </div>
        
        <div class='row vision-hearing'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'hearing_check')->textarea(['maxlength' => 100, 'rows' => 3, 'placeholder' => '请输入' . $infoModelAttributes['hearing_check']]) ?>
            </div>
        </div>
        
        
        <div class='row title-patient-div'>
            <div class='col-sm-12'>
                <p class="titleP">
                    <span class="circleSpan"></span>
                    <span class="titleSpan">营养与饮食</span>
                </p>
            </div>
        </div>

        <div class='row nutrition-diet'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'feeding_patterns')->textarea(['maxlength' => 100, 'rows' => 3, 'placeholder' => '请输入' . $infoModelAttributes['feeding_patterns']]) ?>
            </div>
        </div>
        
        <div class='row nutrition-diet'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'feeding_num')->textarea(['maxlength' => 100, 'rows' => 3, 'placeholder' => '请输入' . $infoModelAttributes['feeding_num']]) ?>
            </div>
        </div>
        
        <div class='row nutrition-diet'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'substitutes')->textarea(['maxlength' => 100, 'rows' => 3, 'placeholder' => '请输入' . $infoModelAttributes['substitutes']]) ?>
            </div>
        </div>
        
        <div class='row nutrition-diet'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'dietary_supplement')->textarea(['maxlength' => 100, 'rows' => 3, 'placeholder' => '请输入' . $infoModelAttributes['dietary_supplement']]) ?>
            </div>
        </div>
        
        <div class='row nutrition-diet'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'food_types')->checkboxList(ChildExaminationInfo::$getFoodType)->label('食物种类') ?>
            </div>
        </div>
        
        
        <div class='row title-patient-div'>
            <div class='col-sm-12'>
                <p class="titleP">
                    <span class="circleSpan"></span>
                    <span class="titleSpan">发育评估</span>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-xs-2 col-sm-2 asqTitle text-left">
                        <label class="control-label">ASQ-3评估结果</label>
                    </div>
                    <div class="childLine">
                    </div>
                </div>
            </div>
        </div>
        <div class='row asq-3'>
            <div class='col-sm-12'>
                <?= $form->field($assessmentModel, 'communicate')->radioList(ChildExaminationAssessment::$getCommunicate)->label($attribute['communicate']) ?>
            </div>
        </div>
        <div class="row asq-3">
            <div class='col-sm-12'>
                <?= $form->field($assessmentModel, 'coarse_action')->radioList(ChildExaminationAssessment::$getCoarseAction)->label($attribute['coarse_action']) ?>
            </div>
        </div>
        <div class='row asq-3'>
            <div class='col-sm-12'>
                <?= $form->field($assessmentModel, 'fine_action')->radioList(ChildExaminationAssessment::$getFineAction)->label($attribute['fine_action']) ?>
            </div>
        </div>
        <div class='row asq-3'>
            <div class='col-sm-12'>
                <?= $form->field($assessmentModel, 'solve_problem')->radioList(ChildExaminationAssessment::$getAolveProblem)->label($attribute['solve_problem']) ?>
            </div>
        </div>
        <div class='row asq-3'>
            <div class='col-sm-12'>
                <?= $form->field($assessmentModel, 'personal_society')->radioList(ChildExaminationAssessment::$getPersonalSociety)->label($attribute['personal_society']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="titleLabelAssessment text-left">
                        <label class="control-label">其他评估结果</label>
                    </div>
                    <div class="childLineAssessment">
                    </div>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-sm-12'>
                <?php
                if (!$assessmentModel->evaluation_type_result) {
                    $assessmentModel->evaluation_type_result = $assessmentModel->other_evaluation_type?$assessmentModel->other_evaluation_type . ';' . $assessmentModel->other_evaluation_result:$assessmentModel->other_evaluation_result;
                }
                ?>
                <?= $form->field($assessmentModel, 'evaluation_type_result')->textarea(['maxlength' => 1000, 'rows' => 6, 'placeholder' => '请输入' . $assessmentModelAttributes['evaluation_type_result']])->label(false, '其他评估结果') ?>
            </div>
        </div>

        
        <div class='row title-patient-div'>
            <div class='col-sm-12'>
                <p class="titleP">
                    <span class="circleSpan"></span>
                    <span class="titleSpan">实验室检查</span>
                </p>
            </div>
        </div>

        <div class='row'>
            <div class='col-sm-12'>
                <?= $form->field($infoModel, 'inspect_content')->textarea(['maxlength' => 1000, 'rows' => 6, 'placeholder' => '请输入' . $infoModelAttributes['inspect_content']])->label(false, '') ?>
            </div>
        </div>
        

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="titleLabelAssessment text-left" style="padding-left: 0px;">
                        <label class="circleSpan" style="margin-top: 10px;"></label>
                        <label class="control-label" style="font-size:15.5px;">指导意见</label>
                    </div>
                    <span class="case_template" style="width: 250px;">
                            <?php // echo $form->field($assessmentModel, 'evaluation_diagnosis')->dropDownList(ArrayHelper::map(Outpatient::templateList(), 'id', 'name', 'group'), ['class' => 'form-control', 'style' => 'width:300px;margin-right:15px', 'prompt' => '- 选择病例模板 -'])->label(false) ?>
                        <?php echo Html::dropDownList('child_template',0 , ArrayHelper::map(Outpatient::childTemplateList(), 'id', 'name', 'group'),['class'=>'form-control  field-child-template', 'prompt' => '- 指导意见模板 -']); ?>
                    </span>
                    <div class="childLineAssessment">
                    </div>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-sm-12'>
                <?= $form->field($assessmentModel, 'evaluation_guidance')->textarea([ 'rows' => 6, 'placeholder' => '请输入' . $assessmentModelAttributes['evaluation_guidance']])->label(false) ?>
            </div>
        </div>

        <div class='row'>
            <div class='col-sm-12 childFormButton'>
                <div class="form-group text-left">
                    <?php if ($triageInfo['child_check_status'] == 0) : ?>
                        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']).Html::submitButton('保存', ['class' => 'btn btn-default btn-form btn-fixed']) ?>
                    <?php else: ?>
                        <?= Html::button('修改', ['class' => 'btn btn-default btn-form child-print']).Html::button('修改', ['class' => 'btn btn-default btn-form child-print btn-fixed']) ?>
                        <?= Html::button('打印报告', ['class' => 'btn btn-default btn-form print-check', 'name' => Yii::$app->request->get('id') . 'child-myshow']); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <div id='child_print' class="tab-pane hide">
        </div>

    </div><!-- outpatient-_childForm -->
    <!--此处有作用，不能删-->
<div class="hide">
    <label class="control-label" for="upload-mediaFile">上传附件</label>
    <?= $this->render('_fileUpload', ['model' => $infoModel, 'medicalFile' => $medicalFile]) ?>
</div>
<?php
$status = $triageInfo['child_check_status'];
$this->registerJs("
	var status = $status;
    if(0 == status){
        $('#checkDiv').show();
    }else{
        $('#checkDiv').hide();
    }

")
?>