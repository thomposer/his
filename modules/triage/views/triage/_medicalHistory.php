<?php
/*
 * time: 2016-11-16 10:37:04.
 * author : yu.li.
 * 既往病史
 */

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$attribute = $model->attributeLabels();
?>


<div class="tab-pane" id="ptab4" data-type="4">
    <?php $form = ActiveForm::begin(['id' => 'j_tabForm_4', 'action' => Url::to(['@triageTriageInfo'])]); ?>
    <?php $model->modal_tab = 4; ?>
    <?= $form->field($model, 'modal_tab')->input('hidden')->label(false) ?>
    <?= $form->field($model, 'record_id')->input('hidden')->label(false) ?>
    <?php
    if ($model->has_allergy_type == 1) {
        $display = 'display: block';
    } else {
        $display = 'display:none';
    }
    ?>
    <!--<form class="form-horizontal">-->
    <div class = 'row'>
    </div>


    <div class="row" id="allergy-list-modal" style="<?= $display ?>">

    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?= $form->field($model, 'food_allergy')->textarea(['rows' => 4, 'placeholder' => '请分别记录下过敏源、症状及发生时间', 'maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?= $form->field($model, 'meditation_allergy')->textarea(['rows' => 4, 'placeholder' => '请分别记录下过敏源、症状及发生时间', 'maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?= $form->field($model, 'personalhistory')->textarea(['rows' => 4, 'placeholder' => '', 'maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?= $form->field($model, 'genetichistory')->textarea(['rows' => 4, 'placeholder' => '', 'maxlength' => true]) ?>
        </div>
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

