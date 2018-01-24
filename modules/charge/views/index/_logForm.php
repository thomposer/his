<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use app\modules\patient\models\Patient;
use app\modules\charge\models\ChargeInfo;
use app\common\Common;
use app\modules\charge\models\ChargeRecord;
use app\modules\spot\models\RecipeList;
$baseUrl = Yii::$app->request->baseUrl;
?>

<div class = 'patient-info'>
    <p class = 'title'>患者信息</p>
    <div class = 'row patient-margin'>
        <div class = 'patient-img-header'>
            <div class = 'patient-img'>
                <?= Html::img($chargeRecordLogList['head_img']?Yii::$app->params['cdnHost'].$chargeRecordLogList['head_img']:'@web/public/img/common/default_patient.png',['class' => 'charge-img','onerror' => 'this.src = "'.$baseUrl . '/public/img/common/default_patient.png"']) ?>
            </div>
        </div>
        <div class = 'patient-user'>

            <p>姓名：<?= Html::encode($chargeRecordLogList['username']) ?></p>
            <p>性别：<?= Patient::$getSex[$chargeRecordLogList['sex']] ?></p>
            <p>年龄：<?= $chargeRecordLogList['age'] ?></p>
            <p>手机号：<?= Html::encode($chargeRecordLogList['iphone']) ?> <span class="charge-doctor">接诊医生：<?= $chargeRecordLogList['doctor_name']?Html::encode($chargeRecordLogList['doctor_name']):'--' ?></span></p>
        </div>
    </div>
</div>
<p class='title float'><?= $chargeRecordLogList['type'] == 1? '收费明细': '退费明细'?></p>
<?=

GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'grid-view table-responsive'],
    'tableOptions' => ['class' => 'table table-hover table-bordered'],
    'layout' => '{items}',
    'columns' => [

        [
            'attribute' => 'name',
            'headerOptions' => ['class' => 'col-sm-3 col-md-3'],
            'format' => 'raw',
            'value' => function ($dataProvider) {
                 $text = Html::encode($dataProvider->name) . $html = $dataProvider->is_charge_again ? '<span class="text-red-mine">（重收）</span>' : '';
                if ($dataProvider['outpatientType'] == 9) {
                    $text .= Html::a(Html::tag('i', '', [
                        'class' => 'fa fa-question-circle package-charge'
                    ]), [
                        '@apiChargePackageRecord',
                        'packageRecordId' => $dataProvider['packageRecordId'],
                        'record_id' =>$dataProvider['record_id']
                    ], [
                        'role' => 'modal-remote',
                        'data-toggle' => 'tooltip',
                        'data-modal-size' => 'large',
                        'data-request-method' => 'post'
                    ]);
                }

                return $text;
            },
            'label' => $chargeRecordLogList['type'] == 2 ? "退费项 名称":"收费项 名称"
        ],
        [
            'attribute' => 'unit',
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
            'value' => function($dataProvider){
                if ($dataProvider['outpatientType'] == ChargeInfo::$recipeType) {
                    return RecipeList::$getUnit[$dataProvider['unit']];
                }
                return $dataProvider['unit'];
            }
        ],
        [
            'attribute' => 'unit_price',
            'headerOptions' => ['class' => 'col-sm-2 col-md-2']
        ],
        [
            'attribute' => 'num',
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
            'value' => function ($dataProvider)use($chargeRecordLogList) {
                if ($chargeRecordLogList['type']== 1) {
                    return $dataProvider->num;
                }
                return "-".$dataProvider->num;
            }
        ],
        [
            'attribute' => 'discount_price',
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
            'value' => function ($dataProvider){
                return $dataProvider->discount_price != '0.00' ? $dataProvider->discount_price : '--';
            }
        ],
        [
            'attribute' => 'discount_reason',
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
            'value' => function ($dataProvider) {
                return $dataProvider->discount_reason != '' ? $dataProvider->discount_reason : '--';
            }
        ],
        [
            'attribute' => 'card_discount_price',
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
            'value' => function($dataProvider){
                return $dataProvider->card_discount_price != '0.00' ? $dataProvider->card_discount_price : '--';
            }
        ],
        [
            'attribute' => '折后金额（元）',
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
            'value' => function($dataProvider){
                return Common::num(abs($dataProvider->unit_price * $dataProvider->num - $dataProvider->discount_price - $dataProvider->card_discount_price));
            }
        ]
    ],
]);
?>
<div class='row margin'>
    <div class='col-xs-6 col-md-6'>
        <?php if (isset($chargeRecordLogList['diagnosis_price'])): ?>
            <div class='padding'>
                诊疗费用：
                <span class='left-price five-price'><?= $chargeRecordLogList['diagnosis_price'] . ($chargeRecordLogList['diagnosis_discount_price'] != 0 ? '（已优惠' .$chargeRecordLogList['diagnosis_discount_price']. '）' : ''); ?></span>
                <?php
                if ($chargeRecordLogList['fee_remarks'] != '') {
                    echo '（' . Html::encode($chargeRecordLogList['fee_remarks']) . '）';
                }
                ?>
            </div>
        <?php endif; ?>
        <?php if ($chargeRecordLogList['inspect_price'] != null): ?>
            <?php $inspectDiscountInfo = $chargeRecordLogList['inspect_discount_price'] != 0 ? '（已优惠' . $chargeRecordLogList['inspect_discount_price'] . '）' : ''; ?>
            <div class='padding'>
                实验室检查总费用：<span
                    class='left-price inspect-price'><?= $chargeRecordLogList['inspect_price'] . $inspectDiscountInfo; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($chargeRecordLogList['check_price'] != null): ?>
            <?php $checkDiscountInfo = $chargeRecordLogList['check_discount_price'] != 0 ? '（已优惠' . $chargeRecordLogList['check_discount_price'] . '）' : ''; ?>
            <div class='padding'>
                影像学检查总费用：<span
                    class='left-price check-price'><?= $chargeRecordLogList['check_price'] . $checkDiscountInfo; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($chargeRecordLogList['cure_price'] != null): ?>
            <?php $cureDiscountInfo = $chargeRecordLogList['cure_discount_price'] != 0 ? '（已优惠' . $chargeRecordLogList['cure_discount_price'] . '）' : ''; ?>
            <div class='padding'>
                治疗总费用：<span
                    class='left-price cure-price'><?= $chargeRecordLogList['cure_price'] . $cureDiscountInfo; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($chargeRecordLogList['recipe_price'] != null): ?>
            <?php $recipeDiscountInfo = $chargeRecordLogList['recipe_discount_price'] != 0 ? '（已优惠' . $chargeRecordLogList['recipe_discount_price'] . '）' : ''; ?>

            <div class='padding'>
                处方总费用：<span
                    class='left-price recipe-price'><?php echo $chargeRecordLogList['recipe_price'] . $recipeDiscountInfo; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($chargeRecordLogList['material_price'] != null): ?>
            <?php $materialDiscountInfo = $chargeRecordLogList['material_discount_price'] != 0 ? '（已优惠' . $chargeRecordLogList['material_discount_price'] . '）' : ''; ?>

            <div class='padding'>
                其他总费用：<span
                    class='left-price recipe-price'><?php echo $chargeRecordLogList['material_price'] . $materialDiscountInfo; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($chargeRecordLogList['package_price'] != null): ?>
            <?php $packageDiscountInfo = $chargeRecordLogList['package_discount_price'] != 0 ? '（已优惠' . $chargeRecordLogList['package_discount_price'] . '）' : ''; ?>

            <div class='padding'>
                医嘱套餐总费用：<span
                    class='left-price package-price'><?php echo $chargeRecordLogList['package_price'] . $packageDiscountInfo; ?></span>
            </div>
        <?php endif; ?>
        <div class="padding">
            <?php
            if ($chargeRecordLogList['type'] == 2) {
                echo '<span class="refund-reason-color">退费原因：</span>';
                echo Html::encode(ChargeInfo::$getRefundChargeReason[$chargeRecordLogList['refund_reason']]);

            }
            ?>
            <?php
            if ($chargeRecordLogList['type'] == 2 && $chargeRecordLogList['refund_reason_description'] != '') {
                echo ' — ';
                echo Html::encode($chargeRecordLogList['refund_reason_description']);
            }
            ?>
        </div>
    </div>
    <div class='col-xs-6 col-md-6 right'>
        <?php
        if ($chargeRecordLogList['type'] == 2) {
            $fee_type = "退费合计";
        } else {
            $fee_type = "应收费用";
        }
        ?>
        <label class='selected-text col-xs-12 col-md-12'>应收费用：
            <div class='total-price old-selected-total-price'><?= $chargeRecordLogList['order_price'] ?></div>
        </label>
        <label class='selected-text col-xs-12 col-md-12'>优惠金额：
            <div class='total-price discount-total-price'>
                <?php
                echo $chargeRecordLogList['discount_price'];
                ?>
            </div>
        </label>

        <label class='selected-text col-xs-12 col-md-12'>实际应付：
            <div
                class='total-price receiptAmount'><?= Common::num($chargeRecordLogList['order_price'] - $chargeRecordLogList['discount_price']); ?></div>
        </label>

        <?php if ($chargeRecordLogList['type'] == 1): ?>

            <label class='selected-text col-xs-12 col-md-12 income'>实收费用：
                <div class='total-price'>
                    <?= $chargeRecordLogList['income']; ?>
                </div>
            </label>

            <?php if (1 == $chargeRecordLogList['pay_type']): ?>
                <label class='selected-text col-xs-12 col-md-12'>找&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;零：
                    <div class='total-price'>
                        <?= $chargeRecordLogList['change'] ?>
                    </div>
                </label>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($chargeRecordLogList['pay_type']): ?>
            <label class='selected-text col-xs-12 col-md-12'>支付方式：
                <div class='payType'>
                    <?= ChargeRecord::$getType[$chargeRecordLogList['pay_type']]; ?>
                </div>
            </label>
        <?php endif; ?>
        <?php if ($chargeRecordLogList['type'] == 2): ?>
            <label class='selected-text col-xs-12 col-md-12 refundAmount'><?php echo $fee_type ?>：
                <div
                    class='total-price'><?= '- '.$chargeRecordLogList['refund_price']; ?></div>
            </label>
        <?php endif; ?>


    </div>
</div>
        