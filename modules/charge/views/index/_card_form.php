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
<div class="charge-record-form">
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal form-card'],
        'fieldConfig' => [
            'template' => "<div class='col-xs-3 col-sm-3 text-left pay-option'>{label}</div><div class='col-xs-9 col-sm-9'>{input}<div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'>{error}</div></div>",
        ]
    ]); ?>
    <div class="card-tips">
            <p class="card-tips-header">会员卡使用前请验证：</p>
            <ul class="card-tips-list">
                <li>1.会员卡真实性和有效性。</li>
                <li>2.持卡人身份信息。</li>
                <li>3.会员卡适用机构/诊所，可支付的服务内容。</li>
            </ul>
            <p class="card-tips-foot">验证通过后并记录服务内容后再确认支付成功。</p>
            <p class="card-tips-link">
                <?= Html::a('前往 [会员卡] 操作', ['@careCenterCardIndex'], ['target' => '_blank']) ?>
            </p>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'price')->hiddenInput()->label(false) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'allPrice')->hiddenInput()->label(false) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'out_trade_no')->hiddenInput()->label(false) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <input type="hidden" id="chargerecord-card" class="form-control" name="card" value="1">
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
