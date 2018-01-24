<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/2
 * Time: 9:41
 */
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
$attributeLabels = $model->attributeLabels();
?>
<div class = 'row title-patient-div'>
    <div class = 'col-sm-12'>
        <p class="titleP">
            <span class="circleSpan"></span>
            <span class="titleSpan">出生记录</span>
        </p>
    </div>
</div>
<div class = 'row'>
    <div class = 'col-md-4'>
        <?= $form->field($model, 'pregnancy_week')->textInput(['maxlength' => true]) ?>
    </div>
    <div class = 'col-md-4'>
        <?= $form->field($model, 'childbirth_weightkg')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class = 'row'>
    <div class = 'col-md-4'>
        <?= $form->field($model, 'childbirth_heightcm')->textInput(['maxlength' => true]) ?>
    </div>
    <div class = 'col-md-4'>
        <?= $form->field($model, 'childbirth_head_circumference')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class = 'row'>
    <div class = 'col-sm-12'>
        <?= $form->field($model, 'childbirth_way')->radioList($model::$childBirthWay)->label($attribute['childbirth_way']) ?>
    </div>
</div>
<div class = 'row'>
    <div class = 'col-sm-12'>
        <?= $form->field($model, 'childbirth_case')->checkboxList($model::$childBirthCase)->label('是否出现以下情况') ?>
    </div>
</div>
<div class = 'row'>
    <div class = 'col-md-5'>
        <?= $form->field($model, 'childbirth_time')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class = 'row'>
    <div class = 'col-md-12'>
        <?= $form->field($model, 'childbirth_situation')->textarea(['rows' => 5]) ?>
    </div>
</div>
<div class = 'row'>
    <div class = 'col-sm-12'>
        <?= $form->field($model, 'childbirth_hearing')->radioList($model::$childBirthHearing)->label($attribute['childbirth_hearing']) ?>
    </div>
</div>


<div class = 'row title-patient-div'>
    <div class = 'col-sm-12'>
        <p class="titleP">
            <span class="circleSpan"></span>
            <span class="titleSpan">妈妈孕期记录</span>
        </p>
    </div>
</div>

<div class = 'row'>
    <div class = 'col-md-4'>
        <?= $form->field($model, 'pre_pregnancy_weightkg')->textInput(['maxlength' => true]) ?>
    </div>
    <div class = 'col-md-4'>
        <?= $form->field($model, 'pregnancy_max_weightkg')->textInput(['maxlength' => true]) ?>
    </div>
</div>


<div class = 'row'>
    <div class = 'col-md-4'>
        <?= $form->field($model, 'pregnancy_min_weightkg')->textInput(['maxlength' => true]) ?>
    </div>
    <div class = 'col-md-4'>
        <?= $form->field($model, 'pregnancy_heightcm')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class = 'row'>
    <div class = 'col-md-12'>
        <?= $form->field($model, 'pregnancy_situation')->textarea(['rows' => 5]) ?>
        <?= Html::hiddenInput('PatientSubmeter[save_type]', 1,['class'=>'save_type'])?>
    </div>
</div>
<?php if(isset($showBtn)&&$showBtn==1):?>
<div class = 'modal-footer text-center'>
        <div class = 'form-group'>
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form ', 'data-dismiss' => "modal"]) ?>
            <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form save-birth']) ?>
            <?= Html::button('保存并打印', ['class' => 'btn btn-default btn-form save-birth-more']) ?>
        </div>
    </div>
<?php endif;?>