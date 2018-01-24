<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\charge\models\ChargeRecord;
use app\common\Common;
use yii\helpers\Url;
use app\specialModules\recharge\models\MembershipPackageCardFlow;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
VAR_DUMP
?>
<?php  $this->beginBlock('renderCss')?>
<?php  $this->endBlock();?>
<div class="package-card-tips-form">
    <?php
    $form = ActiveForm::begin([
    ]);
    ?>
    <p class="package-card-tips-title">确认<?= $model->transaction_type == 1 ? '消费' : '消费退还' ?>？ </p>
    <p class="package-card-tips-content">确认后勾选的项目剩余次数会相应<?= $model->transaction_type == 1 ? '扣减' : '增加' ?>，请仔细核对您勾选的项目后再确认。</p>
    <?= $form->field($model, 'flow_item')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'transaction_type')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'remark')->hiddenInput()->label(false) ?>
    <?php foreach ($serviceIdList as $value): ?>
        <input type="hidden"class="form-control" name="PackageCardServiceId[]" value="<?= $value ?>">
    <?php endforeach; ?>
    <?php foreach ($serviceTimeList as $key => $value): ?>
        <input type="hidden" class="form-control" name="PackageCardServiceTime[<?= $key ?>]" value="<?= $value ?>">
    <?php endforeach; ?>
    <input type="hidden" id="chargerecord-card" class="form-control" name="packageCard" value="1">
<?php ActiveForm::end(); ?>

</div>
