<?php
/*
 * time: 2016-11-16 10:35:46.
 * author : yu.li.
 * 病历登记
 */

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
$attribute = $model->attributeLabels();
?>


<div class="tab-pane" id="ptab3" data-type="3">
<?php $form = ActiveForm::begin(['id' => 'j_tabForm_3', 'action' => Url::to(['@triageTriageInfo'])]); ?>
<?php $model->scenario = 'saveType'; ?>
    <?php $model->modal_tab = 3; ?>
    <?= $form->field($model, 'modal_tab')->input('hidden')->label(false); ?>
    <?= $form->field($model, 'record_id')->input('hidden')->label(false); ?>
    <!--<form class="form-horizontal" id="j_tab4Form" novalidate="novalidate">-->
    <div class = 'row'>
        <div class = 'col-sm-12'>
<?= $form->field($model, 'chiefcomplaint')->textarea(['rows' => 4, 'placeholder' => '', 'maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
<?= $form->field($model, 'historypresent')->textarea(['rows' => 4, 'placeholder' => '', 'maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
<?= $form->field($model, 'pasthistory')->textarea(['rows' => 4, 'placeholder' => '', 'maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
<?= $form->field($model, 'physical_examination')->textarea(['rows' => 4, 'placeholder' => '', 'maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-sm-12'>
<?= $form->field($model, 'remark')->textarea(['rows' => 4, 'placeholder' => '', 'maxlength' => true]) ?>
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

