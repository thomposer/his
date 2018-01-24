<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\PaymentConfig */
/* @var $form yii\widgets\ActiveForm */
$labels = $model->attributeLabels();

?>

<?php 
$css = <<<CSS
   .qrcode-view{
            background: #fff;
            border: 1px solid #76a6ef;
            color: #76a6ef;
    }
    .qrcode-view:hover,.qrcode-view:active,.qrcode-view:visited {
            background-color: #fff;
            border: 1px solid #76a6ef;
            color: #76a6ef;
            opacity: 1;
        }
CSS;
$this->registerCss($css);
?>
<div class="payment-config-form col-md-12">

    <?php $form = ActiveForm::begin(['id' => 'qrcode-view']); ?>
    <div class = 'row'>
        <div class = 'col-sm-4'>
            <!--<label class="control-label" for="">微信支付</label>-->
        </div>
    </div>

    <div class = 'row'>
        <div class = 'col-sm-3'>
            <?= $form->field($model, 'appid')->textInput(['maxlength' => true])->label($labels['appid'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-sm-3'>
            <?= $form->field($model, 'mchid')->textInput(['maxlength' => true])->label($labels['mchid'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-sm-3'>
            <?= $form->field($model, 'payment_key')->textInput(['maxlength' => true])->label($labels['payment_key'] . '<span class = "label-required">*</span>') ?>
        </div>
    </div>


    <div class="form-group">
        <?php if (isset($model->appid) && $model->appid): ?>
            <?= Html::button('修改', ['class' => 'btn btn-default btn-form wechat_update']) ?>
        <?php endif; ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form hid wechat_save', 'disabled' => 'disabled']) ?>
        <?= Html::button('二维码预览', ['class' => 'btn btn-default btn-form qrcode-view', 'data-url' => Url::to(['@spot_setPaymentConfigQrcodeView']), 'role' => 'modal-create','data-modal-size'=>'small', 'data-toggle' => 'tooltip', 'data-request-method' => 'POST', 'contentType' => 'application/x-www-form-urlencoded']) ?>
        <?php if (!$model->isNewRecord): ?>
            <?=
            Html::a('删除配置', ['@spot_setPaymentConfigDelete', 'id' => $model->id], ['class' => 'btn btn-default btn-form', 'title' => '删除', 'aria-label' => '删除',
                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'data-method' => 'post',
                'data-toggle' => "tooltip",
            ])
            ?>
        <?php endif; ?>
    </div>
    <div class="form-group" style="color:#77A5F0;margin-top: -20px;font-size: 12px; ">
        提示：保存之前请确保二维码能够成功生成
    </div>

<?php ActiveForm::end(); ?>

</div>
