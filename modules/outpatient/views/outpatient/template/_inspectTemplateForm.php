<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\modules\spot\models\RecipeList;
use yii\helpers\Json;
use app\modules\outpatient\models\RecipeTemplateInfo;
use app\modules\spot\models\CureList;
use app\modules\outpatient\models\CureRecord;
use rkit\yii2\plugins\ajaxform\Asset;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\outpatient\models\RecipeRecord;

Asset::register($this);
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CureRecord */
/* @var $form ActiveForm  */
$attributeLabels = $model->getModel('inspectTemplate')->attributeLabels();
$inspectTemplateInfoLabels = $model->getModel('inspectTemplateInfo')->attributeLabels();
?>


<div class="cure-record-index col-xs-12">
    <?php $form = ActiveForm::begin(['id' => 'inspectTemplate']) ?>
    <div class = 'row'>

        <div class = 'col-md-6'>
            <?= $form->field($model->getModel('inspectTemplate'), 'name')->textInput(['maxlength' => true])->label($attributeLabels['name'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model->getModel('inspectTemplate'), 'template_type_id')->dropDownList(ArrayHelper::map($type, 'id', 'name'), ['prompt' => '请选择']) ?>
        </div>
    </div>
    <label class="control-label" for="checkrecord-check_id"><?= $inspectTemplateInfoLabels['inspectName'] ?><span class="label-required">*</span></label>
    <div class = 'inspect-content'>
        <?php if ($inspectTemplateInfoDataProvider): ?>
            <?php foreach ($inspectTemplateInfoDataProvider as $v): ?>
                <?php
                $itmTitle = "";
                if ($v['inspectItem']) {
                    foreach ($v['inspectItem'] as $itm) {
                        $itmTitle .='<p>' . Html::encode($itm['item_name']);
                        $itmTitle .= $itm['english_name'] ? '(' . Html::encode($itm['english_name']) . ')</p>' : '</p>';
                    }
                }
                ?>
                <div class = 'inspect-list'>
                    <div class = 'check-name' ><span title= "<?= $itmTitle ?>"  data-toggle="tooltip" data-html="true" data-placement="bottom"><?= $v['name'] ?></span></div>
                    <div class = 'check-id'>
                        <input type="hidden" class="form-control" name="InspectTemplateInfo[deleted][]" value="">
                        <input type="hidden" class="form-control" name="InspectTemplateInfo[clinic_inspect_id][]" value='<?= Html::encode(Json::encode(array_merge($v, ['isNewRecord' => 0]), JSON_ERROR_NONE)) ?>'>
                    </div>
                    <div class="op-group show"><?= Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png') ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class = 'box'>
        <?php
        echo $form->field($model->getModel('inspectTemplateInfo'), 'inspectName')->dropDownList(array(), [
            'class' => 'form-control select2',
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

<?php
