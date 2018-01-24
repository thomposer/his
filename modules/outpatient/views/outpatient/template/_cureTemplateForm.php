<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use rkit\yii2\plugins\ajaxform\Asset;
use johnitvn\ajaxcrud\CrudAsset;

Asset::register($this);
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CureRecord */
/* @var $form ActiveForm  */
$attributeLabels = $model->getModel('cureTemplate')->attributeLabels();
$cureTemplateInfoLabels = $model->getModel('cureTemplateInfo')->attributeLabels();
?>


<div class="cure-record-index col-xs-12">
    <?php $form = ActiveForm::begin(['id' => 'cureTemplate']) ?>
    <div class = 'row'>

        <div class = 'col-md-6'>
            <?= $form->field($model->getModel('cureTemplate'), 'name')->textInput(['maxlength' => false])->label($attributeLabels['name'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model->getModel('cureTemplate'), 'template_type_id')->dropDownList(ArrayHelper::map($type, 'id', 'name'), ['prompt' => '请选择']) ?>
        </div>
    </div>
    <div class = 'box'>
        <?php
        echo $form->field($model->getModel('cureTemplateInfo'), 'cureName')->dropDownList([], [
            'class' => 'form-control select2',
            'style' => 'width:100%'
        ])->label('新增治疗' . '<span class = "label-required">*</span>');
        ?>
    </div>
    <div class='box'>
        <div id="w3" class="grid-view table-responsive">
            <table class="table table-hover cure-form">
                <thead>
                    <tr class="header">
                        <th class="col-md-3"><?= $cureTemplateInfoLabels['cureName'] ?></th>
                        <th class="col-md-2"><?= $cureTemplateInfoLabels['unit'] ?></th>
                        <th class="col-md-2"><?= $cureTemplateInfoLabels['time'] ?></th>
                        <th class="col-md-3"><?= $cureTemplateInfoLabels['description'] ?></th>
                        <th class="col-md-1"></th>
                        <th class="col-md-1">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($cureTemplateInfoDataProvider): ?>
                        <?php foreach ($cureTemplateInfoDataProvider as $v): ?>
                            <tr class="cureNameTd">
                                <td class="cureName" data-type="cureName">
                                    <?= Html::encode($v['name']) ?>
                                    <?= Html::hiddenInput('CureTemplateInfo[clinic_cure_id][]', $v['clinic_cure_id'], ['class' => 'form-control']) ?>
                                </td>
                                <td><?= Html::encode($v['unit']) ?></td>
                                <td><?= Html::input('text', 'CureTemplateInfo[time][]', $v['time'], ['class' => 'form-control']) ?></td>
                                <td><?= Html::input('text', 'CureTemplateInfo[description][]', $v['description'], ['class' => 'form-control']) ?></td>
                                <td></td>
                                <td class='cure-delete op-group' style="display:table-cell;"><?= Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png');?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="form-group">
        <?= Html::a('取消', Yii::$app->request->referrer, ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form ajaxform-btn']) ?>

    </div>
    <?php ActiveForm::end() ?>
</div>


