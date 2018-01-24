<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CheckRecord */
/* @var $form ActiveForm */
$attributes = $checkModel->attributeLabels();
?>
<div class="package-check-index">

        <label class="control-label" for="checkrecord-check_id"><?= $attributes['checkName'] ?></label>
        <div class = 'package-inspect-form'>
            <?= $form->field($checkModel, 'checkName')->dropDownList(array(),['class' => 'form-control select2','style' => 'width:100%'])->label(false) ?>
    	</div>
        <div class = 'check-content'>
        <?php if ($checkDataProvider): ?>
        <?php foreach ($checkDataProvider as $v):?>
            <div class = 'check-list'>
                <div class = 'check-name'><?= Html::encode($v['name']); ?></div>
                    <div class = 'check-id'>
                        <input type="hidden" class="form-control" name="OutpatientPackageCheck[deleted][]" value="">
                        <?= Html::hiddenInput('OutpatientPackageCheck[check_id][]',$v['check_id'],['class' => 'form-control']) ?>
                    </div>
                    <div class="op-group"><?= Html::img(Yii::$app->request->baseUrl.'/public/img/common/delete.png') ?></div>
            </div>
        <?php endforeach;?>
        <?php endif;?>
        </div>
</div>
