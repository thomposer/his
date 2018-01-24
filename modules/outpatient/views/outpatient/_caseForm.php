<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\CaseTemplate;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CaseTemplate */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();

$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>

<div class="case-template-form col-md-12">

    <?php $form = ActiveForm::begin([
        'id' => 'caseTemplateForm',
        'action' => Url::to(['@outpatientOutpatientCreateTemplate'])
    ]); ?>

    <?= $form->field($model, 'caseId')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'saveType')->radioList(CaseTemplate::$getSaveType, ['class' => 'radio-inline', 'itemOptions' => ['labelOptions' => ['class' => 'radio-inline']]])->label($attribute['saveType'] . '<span class = "label-required">*</span>') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 64])->label($attribute['name'] . '<span class = "label-required">*</span>') ?>

    <?php $model->type = 2 ?>
    <?= $form->field($model, 'type')->radioList(CaseTemplate::$getType, ['class' => 'radio-inline', 'itemOptions' => ['labelOptions' => ['class' => 'radio-inline']]])->label($attribute['type'] . '<span class = "label-required">*</span>') ?>

    <?= $form->field($model, 'chiefcomplaint')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'historypresent')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'pasthistory')->textarea(['rows' => 5 ]) ?>

    <?= $form->field($model, 'pastdraghistory')->textarea(['rows' => 5 ]) ?>

    <?= $form->field($model, 'personalhistory')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'genetichistory')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'physical_examination')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'cure_idea')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'followup')->textarea(['rows' => 5]) ?>

    <div class="form-group">
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
   require(["$baseUrl/public/js/outpatient/caseForm.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
