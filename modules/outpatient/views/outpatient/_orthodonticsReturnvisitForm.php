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
use app\modules\report\models\Report;

/* @var $this yii\web\View */
/* @var $model app\modules\triage\models\TriageInfo */
/* @var $form ActiveForm */
$attribute = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
$orthodonticsReturnvisitHasSave = $model->id ? 1 : 2;//1-已保存 2未保存

?>
<div class="outpatient-orthodonticsReturnvisitForm col-sm-12 col-md-12 patient-basic">

    <?php
        $form = ActiveForm::begin([
                    'options' => ['id' => 'orthodonticsReturnvisitForm'],
        ]);
    ?>
    <div class="row basic-form">

        <div class="basic-form-content">
            <?= $form->field($model, 'returnvisit')->textarea(['rows' => 4])->label($attribute['returnvisit'] . '<span class = "label-required">*</span>') ?>
            
            <?= $this->render('_allergyForm', ['model' => $model, 'form' => $form]) ?>
            <div class="form-group child-form-first-check-form">
                <label class="control-label" for="checkrecord-check_id">初步诊断<span style="color:#FF5000;">（若需要给患者开检验，检查，治疗，处方医嘱，请务必填写初步诊断）</span></label>
                <?= $this->render('_firstCheckForm', ['firstCheckDataProvider' => $firstCheckDataProvider, 'form' => $form]) ?>
            </div>
            
            <?= $form->field($model, 'check')->textarea(['rows' => 4]) ?>
            
            <?= $form->field($model, 'treatment')->textarea(['rows' => 4])->label($attribute['treatment'] . '<span class = "label-required">*</span>') ?>

            <div>
                <label class="control-label" for="upload-mediaFile">上传附件</label>
                <?= $this->render('_fileUpload', ['model' => $model, 'medicalFile' => $medicalFile]) ?>
            </div>
            <div class="form-group">
                <?php if (($orthodonticsReturnvisitHasSave == 1) && (empty($model->errors))): ?>
                    <?= Html::button('修改', ['class' => 'btn btn-default btn-form reocrd-btn-custom']).Html::button('修改', ['class' => 'btn btn-default btn-form reocrd-btn-custom btn-fixed']) ?>
                    <?= Html::button('打印病历', ['class' => 'btn btn-default btn-form pull-right orthodontics-returnvisit-print', 'data-value' => Yii::$app->request->get('id'), 'name' => 'orthodonticsPrint' . Yii::$app->request->get('id')]); ?>
                <?php else: ?>
                    <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form reocrd-btn-custom']).Html::submitButton('保存', ['class' => 'btn btn-default btn-form reocrd-btn-custom btn-fixed']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <div id="orthodontics-print" class="hide"></div>
</div>

<?php
$js = <<<JS
   require(["$baseUrl/public/js/outpatient/orthodontics.js"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
