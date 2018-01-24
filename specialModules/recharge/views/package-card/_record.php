<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\specialModules\recharge\models\MembershipPackageCardFlow;

/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CardRecharge */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
$attributes = $model->attributeLabels();
?>

<div class="package-card-form col-md-12">

    <?php
    $form = ActiveForm::begin([
                'id' => 'package-card-record-form',
    ]);
    ?>
    <?= $form->field($model, 'flow_item')->textInput(['maxlength' => true])->label($attributes['flow_item'] . '<span class = "label-required">*</span>') ?>
    <?= $form->field($model, 'transaction_type')->dropDownList(MembershipPackageCardFlow::$getEditRecordType, ['prompt' => '请选择', 'data-value' => $model->transaction_type ? $model->transaction_type : 0])->label($attributes['transaction_type'] . '<span class = "label-required">*</span>') ?>

    <div class="package-card-content">
        <label class="control-label">明细<span class="label-required">*</span></label>
        <div class="package-card-info">
            <div class="package-card-name"><?= $cardBasic[0]['cardName'] ?></div>
            <?php foreach ($cardBasic as $serviceInfo): ?>
                <div class="package-card-service-list">
                        <div class="package-card-service-item">
                            <div class="package-card-service-title">
                                <input type="checkbox" name="PackageCardServiceId[]" value="<?= $serviceInfo['serviceId'] ?>" <?= in_array($serviceInfo['serviceId'],$serviceIdList) ? 'checked' : '' ?>> 
                                <span class="package-card-service-name"><?= $serviceInfo['serviceName'] ?></span>
                                （剩下 <span class="package-card-service-time"><?= $serviceInfo['remainTime'] ?></span> 次）
                            </div>
                            <div class="add-deduct">
                                <span class="add-deduct-title">扣减</span> <input name="PackageCardServiceTime[<?= $serviceInfo['serviceId'] ?>]" <?= isset($serviceTimeList[$serviceInfo['serviceId']]) ? 'value="' . $serviceTimeList[$serviceInfo['serviceId']] . '"' : 'disabled' ?> class="package-card-service-input" type="text"> 次
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                    <div class="help-block" style="color: rgb(255, 80, 0);padding-left: 367.5px;"></div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

<?= $form->field($model, 'remark')->textarea(['maxlength' => true, 'rows' => 4])->label($attributes['remark'] . '<span class = "label-required">*</span>') ?>


<?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    require(["<?= $baseUrl ?>" + "/public/js/recharge/recordForm.js?v=" + '<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>

