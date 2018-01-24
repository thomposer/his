<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\charge\models\ChargeRecord;
use app\common\Common;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
// $type = [
//     1 => Html::img($baseUrl.'/public/img/charge/cash.png').'现金',
//     2 => Html::img($baseUrl.'/public/img/charge/card.png').'刷卡',
// ];
?>
<?php  $this->beginBlock('renderCss')?>
<?php  $this->endBlock();?>
<div class="package-card-tips-form">
    <?php
    $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal form-card'],
                'fieldConfig' => [
                    'template' => "<div class='col-xs-3 col-sm-3 text-left pay-option'>{label}</div><div class='col-xs-9 col-sm-9'>{input}<div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'>{error}</div></div>",
                ]
    ]);
    ?>
    <p class="package-card-tips-title">确认支付？ </p>
    <p class="package-card-tips-content">确认支付后勾选的项目剩余次数会相应扣减，请仔细核对您勾选的项目后再确认支付。</p>
    <?= $form->field($model, 'price')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'allPrice')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'out_trade_no')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>
    <?php foreach ($serviceIdArr as $cardId => $serviceCardList): ?>
        <?php foreach ($serviceCardList as $value): ?>
            <input type="hidden"class="form-control" name="ServiceCardServiceId[<?= $cardId ?>][]" value="<?= $value ?>">
        <?php endforeach; ?>
    <?php endforeach; ?>
    <?php foreach ($serviceTime as $cardId => $serviceList): ?>
        <?php foreach ($serviceList as $serviceId => $value): ?>
            <input type="hidden" class="form-control" name="ServiceCardServiceTime[<?= $cardId ?>][<?= $serviceId ?>]" value="<?= $value ?>">
        <?php endforeach; ?>
    <?php endforeach; ?>
    <input type="hidden" id="chargerecord-card" class="form-control" name="serviceCard" value="1">
<?php ActiveForm::end(); ?>

</div>
<?php
    $this->registerCss('
            .form-group {
                margin-bottom: 15px;
            }
            ');
?>
