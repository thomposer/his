<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\patient\models\Patient;
use dosamigos\datepicker\DatePicker;

$attribute = $model->attributeLabels();
?>
<?php
$form = ActiveForm::begin([
            'id' => 'family_modal_form'
        ]);
?>
<div class='row'>
    <div class="family-modal">
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'relation')->dropDownList(Patient::$getFamilyRelation, ['prompt' => '请选择'])->label($attribute['relation'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label($attribute['name'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?=
                $form->field($model, 'birthday')->widget(
                        DatePicker::className(), [
                    'inline' => false,
                    'language' => 'zh-CN',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                        ]
                )->label($attribute['birthday'])
                ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'sex')->radioList(Patient::$getSex,['class' => 'sex'])->label($attribute['sex'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>

        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'iphone')->textInput()->label($attribute['iphone'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'card')->textInput()->label($attribute['card']) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

