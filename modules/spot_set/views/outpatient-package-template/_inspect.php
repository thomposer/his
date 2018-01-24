<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CheckRecord */
/* @var $form ActiveForm */
$attributes = $inspectModel->attributeLabels();
?>

<div class="package-inspect-index">
    <label class="control-label" for="checkrecord-check_id"><?= $attributes['inspectName'] ?></label>
    <div class = 'package-inspect-form'>
        <?= $form->field($inspectModel, 'inspectName')->dropDownList(array(), ['class' => 'form-control select2', 'style' => 'width:100%'])->label(false) ?>
    </div>
    <div class = 'inspect-content'>

        <?php if ($inspectDataProvider): ?>
            <?php foreach ($inspectDataProvider as $v): ?>
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
                    <div class = 'check-name' ><span title= "<?= $itmTitle ?>"  data-toggle="tooltip" data-html="true" data-placement="bottom"><?= Html::encode($v['name']) ?></span></div>
                        <div class = 'check-id'>
                            <input type="hidden" class="form-control" name="OutpatientPackageCheck[deleted][]" value="">
                            <input type="hidden" class="form-control" name="OutpatientPackageInspect[inspect_id][]" value='<?= $v['clinic_inspect_id'] ?>'>
                        </div>
                        <div class="op-group"><?= Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png') ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div><!-- _checkRecordForm -->