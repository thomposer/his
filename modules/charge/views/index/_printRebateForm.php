<?php
use app\modules\charge\models\ChargeInfo;
use yii\grid\GridView;
use app\common\Common;
use yii\helpers\Html;
use app\modules\spot\models\RecipeList;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\charge\models\ChargeRecord;

$baseUrl = Yii::$app->request->baseUrl;
$action = Yii::$app->controller->action->id;
$oneDiscount = '';
$secondDiscount = '';
$total = 0;//费用合计
$receipt_amount = 0;//实收金额
$discount = [];
if (isset($chargeType['originalPrice'])) {
    $total = Common::num(array_sum($chargeType['originalPrice']));
    $discount = ChargeRecord::getDiscount($total, 1, $discountPrice,$chargeType['chargeTotalDiscount'],$chargeType['cardTotalDiscount']);
}
?>
<div class="<?= Yii::$app->request->get('id') ?>charge_show my-show rebate-print">
    <div class="rebate-foot-bottom">
        <?php
        if ($soptInfo['icon_url']) {
            echo Html::img(Yii::$app->params['cdnHost'] . $soptInfo['icon_url'], ['class' => 'clinic-img', 'onerror' => 'javascript:this.src=\'' . $baseUrl . '/public/img/charge/img_click_moren.png\'']);
        }
        ?>
        <p class="rebate-date fr"><?= Html::encode($soptInfo['telephone']) ?></p>
        <div class="children-sign">儿科</div>
    </div>
    <span class="clearfix"></span>
    <p class='title rebate-title add-margin-bottom-20'>收费清单</p>
    <div style="min-height: 600px;" class="print-main-contnet">
        <p class='title small-title-third'>病历号：<?= $userInfo['patient_number'] ?></p>
        <div class="fill-info">
            <div class="patient-user">
                <div class="font-0px">
                    <div class="total-column-three-part"><span class="column-name">姓名</span><span
                            class="column-value"><?= Html::encode($userInfo['username']) ?></span></div>
                    <div class="total-column-three-part"><span class="column-name">性别</span><span
                            class="column-value"><?= Patient::$getSex[$userInfo['sex']] ?></span></div>
                    <div class="total-column-three-part"><span class="column-name">年龄</span><span style="width: 87%;" class="column-value"><?= Patient::dateDiffage($userInfo['birthday'], time()) ?></span>
                    </div>
                </div>
                <div class="line-margin-top font-0px">
                    <div class="total-column-three-part">
                        <span class="column-name">出生日期</span>
                        <span style="width: 58%;" class="column-value"><?= date("Y-m-d", $userInfo['birthday']) ?></span>
                    </div>
                    <div class="total-column-three-part">
                        <span class="column-name">TEL</span>
                        <span style="width: 71%;" class="column-value"><?= Html::encode($userInfo['iphone']) ?></span>
                    </div>
                    <div class="total-column-three-part">
                        <span class="column-name">接诊医生</span>
                        <span style="width: 73%;" class="column-value"><?= $doctor_name ? Html::encode($doctor_name) : '--'?></span>
                    </div>
                </div>
            </div>

        </div>
        <div class="charge-block-margin-top fill-info font-3rem">
            <div class='title small-title-third'>收费明细</div>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive charge-table-first inspect-table'],
                'tableOptions' => ['class' => 'table charge-table font-3rem inspect-table'],
                'layout' => '{items}',

                'columns' => [
                    [
                        'attribute' => 'name',
                        'headerOptions' => ['class' => 'col-sm-6 col-md-6', 'style' => 'width:20%;']
                    ],
                    [
                        'attribute' => 'unit',
                        'headerOptions' => ['class' => 'col-sm-1 col-md-1', 'style' => 'width:3%;'],
                        'value' => function ($dataProvider) {
                            if ($dataProvider->type == ChargeInfo::$recipeType) {
                                return RecipeList::$getUnit[$dataProvider->unit];
                            }
                            return $dataProvider->unit;
                        }
                    ],
                    [
                        'attribute' => 'unit_price',
                        'contentOptions' => ['class' => 'tr'],
                        'headerOptions' => ['class' => 'col-sm-1 col-md-1 tr', 'style' => 'width:7%;']
                    ],
                    [
                        'attribute' => 'num',
                        'contentOptions' => ['class' => 'tr'],
                        'headerOptions' => ['class' => 'col-sm-1 col-md-1 tr', 'style' => 'width:5%;']
                    ],
                    [
                        'attribute' => 'discount_price',
                        'contentOptions' => ['class' => 'total_price tr'],
                        'value' => function ($dataProvider) {
                            return $returnData = ($dataProvider['discount_price'] != '0')?(Common::num($dataProvider['discount_price'])):'--';
                        },
                        'headerOptions' => ['class' => 'col-sm-1 col-md-1 tr', 'style' => 'width:10%;']

                    ],
                    [
                        'attribute' => 'card_discount_price',
                        'headerOptions' => [ 'class' => 'col-sm-1 col-md-1 tr', 'style' => 'width:10%;'],
                        'contentOptions' => [ 'class' => 'cardDiscountPrice total_price tr'],
                        'value' => function($dataProvider){
                            return $dataProvider['card_discount_price'] == 0.00 ?' --':$dataProvider['card_discount_price'];
                        },
                        'visible' => $action == 'create'?false:true
                    ],
                    [
                        'attribute' => '折后金额(元)',
                        'contentOptions' => ['class' => 'total_price tr'],
                        'value' => function ($dataProvider) {
                            return Common::num(abs($dataProvider['unit_price'] * $dataProvider['num'] - $dataProvider['discount_price'] - $dataProvider['card_discount_price']));
                        },
                        'headerOptions' => ['class' => 'col-sm-1 col-md-1 tr', 'style' => 'width:10%;']

                    ]
                ],
            ]); ?>
        </div>
        <div class="charge-item-block-margin-top fill-info col-xs-12 charge-table-first">
            <div class='title small-title-third'>收费项目</div>
            <div class='grid-view table-responsive '>
                <table class="table font-3rem charge-table inspect-table">
                    <thead>
                    <tr>
                        <th style="width: 44%;">项目名称</th>
                        <th style="width: 11%;">折后金额(元)</th>
                        <th style="width: 45%;"></th>
                    </thead>
                    <tbody>
                    <?php if (isset($chargeType[ChargeInfo::$inspectType])): ?>
                        <tr data-key="2">
                            <td>实验室检查费用</td>
                            <td class="tr"><?= Common::num(array_sum($chargeType[ChargeInfo::$inspectType])); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($chargeType[ChargeInfo::$checkType])): ?>
                        <tr data-key="2">
                            <td>影像学检查费用</td>
                            <td class="tr"><?= Common::num(array_sum($chargeType[ChargeInfo::$checkType])); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($chargeType[ChargeInfo::$recipeType])): ?>
                        <tr data-key="2">
                            <td>处方费用</td>
                            <td class="tr"><?= Common::num(array_sum($chargeType[ChargeInfo::$recipeType])); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($chargeType[ChargeInfo::$cureType])): ?>
                        <tr data-key="2">
                            <td>治疗费用</td>
                            <td class="tr"><?= Common::num(abs(array_sum($chargeType[ChargeInfo::$cureType]))); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($chargeType[ChargeInfo::$priceType])): ?>
                        <tr data-key="2">
                            <td>诊疗费用</td>
                            <td class="tr"><?= Common::num(abs(array_sum($chargeType[ChargeInfo::$priceType]))); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($chargeType[ChargeInfo::$materialType])): ?>
                        <tr data-key="2">
                            <td>其他费用</td>
                            <td class="tr"><?= Common::num(abs(array_sum($chargeType[ChargeInfo::$materialType]))); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <span class="clearfix"></span>
    </div>
    <span class="clearfix"></span>
    <div style="position: relative;min-height: 100px;" class="rebate-foot font-0px">
        <div class='foot-left rebate-type' style="width: 75%;position:absolute;bottom:0px;left: 0px;">
            <?php if ($type): ?>
            <label class='border add-padding-round font-3rem'>支付方式&nbsp&nbsp<span
                    class='left-price recipe-price'><?= ChargeRecord::$getType[$type]; ?></span>
                <?php endif; ?>
        </div>
        <div class="block-right">
            <div class="font-3rem foot-left">
                <?php
                if ($action == 'refund') {
                    $fee_type = "退费合计：";
                } else {
                    $fee_type = "应收费用：";
                }
                ?>

                <?php echo $fee_type ?>
                <label>
                    <?= isset($total) ? Common::num($total) : '0' ?>元
                </label>
            </div>
            <?php if ($discount['oneDiscount']): ?>
                <div class="font-3rem foot-left">
                    优惠金额：
                    <label>
                        <?= $discount['oneDiscount'] . '元' ?>
                    </label>
                </div>
            <?php endif; ?>
            <div class="font-3rem foot-left">
                实收费用：
                <label>
                    <?= $income ? $income . '元' : '0.00元' ?>
                </label>
            </div>

            <?php if (1 == $type): ?>
                <div class="font-3rem foot-left">
                    找&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;零：
                    <label>
                        <?= !empty($discount['receiptAmount'])?$change.'元':'';?>
                    </label>
                </div>
            <?php endif; ?>
            <?php if ($refundAmount > 0): ?>
                <div class="font-3rem foot-left">退费金额：
                    <label>
                        <?= $refundAmount . '元'; ?>
                    </label>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <div class="fill-info-buttom font-0px">
        <div class="rebate-foot-bottom-second fl">
            <div class="tow-line-buttom fl">
                <div class="line-margin-top">
                    <p class="font-3rem charge-bottom width-75 child-patient-number">收费员：</p>
                    <p class="font-3rem charge-bottom child-patient-number">
                        日期：<?= date('Y-m-d', $chargeCreateTime); ?></p>
                </div>

            </div>
        </div>
    </div>
    <span class="clearfix"></span>
</div>
