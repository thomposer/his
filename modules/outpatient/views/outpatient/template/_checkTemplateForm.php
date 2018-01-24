<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\modules\spot\models\RecipeList;
use yii\helpers\Json;
use app\modules\spot\models\CureList;
use rkit\yii2\plugins\ajaxform\Asset;
use johnitvn\ajaxcrud\CrudAsset;

Asset::register($this);
CrudAsset::register($this);

$attributeLabels = $model->getModel('checkTemplate')->attributeLabels();
$checkTemplateInfoLabels = $model->getModel('checkTemplateInfo')->attributeLabels();
?>


<div class="check-record-index col-xs-12">
    <?php $form = ActiveForm::begin(['id' => 'checkTemplate']) ?>
    <div class = 'row'>

        <div class = 'col-md-6'>
            <?= $form->field($model->getModel('checkTemplate'), 'name')->textInput(['maxlength' => true])->label($attributeLabels['name'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model->getModel('checkTemplate'), 'template_type_id')->dropDownList(ArrayHelper::map($type, 'id', 'name'), ['prompt' => '请选择']) ?>
        </div>
    </div>
    <label class="control-label" for="checkrecord-check_id"><?= $checkTemplateInfoLabels['checkName'] ?><span class="label-required">*</span></label>
    <div class = 'check-content'>
        <?php if ($checkTemplateInfoDataProvider): ?>
            <?php foreach ($checkTemplateInfoDataProvider as $v): ?>
                <div class = 'check-list'>
                    <div class = 'check-name' ><span  data-toggle="tooltip" data-html="true" data-placement="bottom"><?= Html::encode($v['name'])?></span></div>
                    <div class = 'check-id'>
                        <input type="hidden" class="form-control" name="CheckTemplateInfo[deleted][]" value="">
                        <input type="hidden" class="form-control" name="CheckTemplateInfo[clinic_check_id][]" value='<?= Html::encode(Json::encode(array_merge($v, ['isNewRecord' => 0]), JSON_ERROR_NONE)) ?>'>
                    </div>
                    <div class="op-group show"><?= Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png') ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class = 'box'>
        <?php
        echo $form->field($model->getModel('checkTemplateInfo'), 'checkName')->dropDownList([], [
            'class' => 'form-control CheckListSel',
            'style' => 'width:100%'
        ])->label(false);
        ?>
    </div>
    <div class="form-group">
        <?= Html::a('取消', Yii::$app->request->referrer, ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form ajaxform-btn']) ?>

    </div>
    <?php ActiveForm::end() ?>
</div>

