<?php
/*
 * time: 2016-11-16 10:37:04.
 * author : yu.li.
 * 既往病史
 */

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\triage\models\HealthEducation;
use yii\helpers\ArrayHelper;
$healthModel=new HealthEducation();
$attribute = $healthModel->attributeLabels();
?>


<div class="tab-pane" id="ptab5" data-type="5">
    <?php $form = ActiveForm::begin(['id' => 'j_tabForm_5', 'action' => Url::to(['@triageTriageInfo'])]); ?>
    <?php $model->modal_tab = 5; ?>
    <?= $form->field($model, 'modal_tab')->input('hidden')->label(false) ?>
    <?= $form->field($healthEduModel[0], 'record_id')->input('hidden')->label(false) ?>

    <div class="row">
        <div class = 'col-sm-12'>
            <?= Html::button("<i class='fa fa-plus'></i>新增", ['class' => 'btn btn-default font-body2 add-health-education', 'type' => 'button']) ?>
        </div>
    </div>
    <div class="health-edu-content">
        <?php foreach ($healthEduModel as $healthEducation): ?>
            <div class="health-edu-item">
                <div class = 'row health-edu-right-row'>
                    <div class = 'col-sm-12'>
                        <?= $form->field($healthEducation, 'education_content')->textarea(['rows' => 4, 'placeholder' => '请输入' . $attribute['education_content'].'(不多于100个字)','maxlength'=>100,'name'=>'HealthEducation[education_content][]']) ?>
                    </div>
                    <span class = 'health-edu-right-delete basic-family-right-up'>
                        <?= Html::a('<i class="fa-family-trash fa"></i>删除', 'javascript:void(0)', ['class' => 'del-health-education']); ?>
                    </span>
                </div>
                <div class = 'row'>
                    <div class = 'col-sm-6'>
                        <?= $form->field($healthEducation, 'education_object')->dropDownList(HealthEducation::$getEducationObject, ['prompt' => '请选择','name'=>'HealthEducation[education_object][]']) ?>
                    </div>
                    <div class = 'col-sm-6'>
                        <?= $form->field($healthEducation, 'education_method')->dropDownList(HealthEducation::$getEducationMethod, ['prompt' => '请选择','name'=>'HealthEducation[education_method][]']) ?>
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'col-sm-6'>
                            <?= $form->field($healthEducation, 'accept_barrier')->dropDownList(HealthEducation::$getAcceptBarrier, ['prompt' => '请选择','name'=>'HealthEducation[accept_barrier][]']) ?>
                    </div>
                    <div class = 'col-sm-6'>
                        <?= $form->field($healthEducation, 'accept_ability')->dropDownList(HealthEducation::$getAcceptAbility, ['prompt' => '请选择','name'=>'HealthEducation[accept_ability][]']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <div class = 'row'>
        <div class="button-center">
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form ', 'type' => 'button', 'data-dismiss' => 'modal']) ?>
            <?= Html::button('保存', ['class' => 'btn btn-default btn-form ', 'type' => 'submit']) ?>
        </div>
    </div>

    <!--</form>-->
    <?php ActiveForm::end(); ?>
</div>

