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

?>
<div class="my-show rebate-print" style="page-break-after: always;">
    <div class="rebate-foot-bottom">
        <?php
        if ($spotInfo['icon_url']) {
            echo Html::img(Yii::$app->params['cdnHost'] . $spotInfo['icon_url'], ['class' => 'clinic-img', 'onerror' => 'javascript:this.src=\'' . $baseUrl . '/public/img/charge/img_click_moren.png\'']);
        }
        ?>
        <p class="rebate-date fr"><?= Html::encode($spotInfo['telephone']) ?></p>
        <div class="children-sign">儿科</div>
    </div>
    <span class="clearfix"></span>
    <?php if ($chargeRecordLogList['type'] == 1) :?>
        <p class='title rebate-title add-margin-bottom-20'>收费清单</p>
    <?php endif; ?>
    <?php if ($chargeRecordLogList['type'] == 2) :?>
        <p class='title rebate-title add-margin-bottom-20'>退费清单</p>
    <?php endif; ?>
    <div style="min-height: 600px;" class="print-main-contnet">
        <p class='title small-title-third'>病历号：<?= $chargeRecordLogList['patient_number'] ?></p>
        <div class="fill-info">
            <div class="patient-user">
                <div class="font-0px">
                    <div class="total-column-three-part"><span class="column-name">姓名</span><span
                            class="column-value"><?= Html::encode($chargeRecordLogList['username']) ?></span></div>
                    <div class="total-column-three-part"><span class="column-name">性别</span><span
                            class="column-value"><?= Patient::$getSex[$chargeRecordLogList['sex']] ?></span></div>
                    <div class="total-column-three-part"><span class="column-name">年龄</span><span style="width: 87%;" class="column-value"><?= Patient::dateDiffage($chargeRecordLogList['birthday'], time()) ?></span>
                    </div>
                </div>
                <div class="line-margin-top font-0px">
                    <div class="total-column-three-part">
                        <span class="column-name">出生日期</span>
                        <span style="width: 58%;" class="column-value"><?= date("Y-m-d", $chargeRecordLogList['birthday']) ?></span>
                    </div>
                    <div class="total-column-three-part">
                        <span class="column-name">TEL</span>
                        <span style="width: 71%;" class="column-value"><?= Html::encode($chargeRecordLogList['iphone']) ?></span>
                    </div>
                    <div class="total-column-three-part">
                        <span class="column-name">接诊医生</span>
                        <span style="width: 73%;" class="column-value"><?= $chargeRecordLogList['doctor_name'] ? Html::encode($chargeRecordLogList['doctor_name']) : '--'?></span>
                    </div>
                </div>
            </div>

        </div>
        <div class="charge-block-margin-top fill-info font-3rem">
            <?php if ($chargeRecordLogList['type'] == 1) :?>
                <div class='title small-title-third'>收费明细</div>
                <?php else: ?>
                <div class='title small-title-third'>退费明细</div>
            <?php endif; ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive charge-table-first inspect-table'],
                'tableOptions' => ['class' => 'table charge-table font-3rem inspect-table'],
                'layout' => '{items}',

                'columns' => [
                    [
                        'attribute' => 'name',
                        'headerOptions' => ['class' => 'col-sm-6 col-md-6', 'style' => 'width:20%;'],
                        'label' => $chargeRecordLogList['type'] == 2 ? "退费项 名称":"收费项 名称"

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
                        'headerOptions' => ['class' => 'col-sm-1 col-md-1 tr', 'style' => 'width:5%;'],
                        'value' => function ($dataProvider) {
                            if ($dataProvider->type == 1) {
                                return $dataProvider->num;
                            }
                            return "-".$dataProvider->num;
                        }
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
                            return Common::num(abs($dataProvider->unit_price * $dataProvider->num - $dataProvider->discount_price - $dataProvider->card_discount_price));
                        },
                        'headerOptions' => ['class' => 'col-sm-1 col-md-1 tr', 'style' => 'width:10%;']

                    ]
                ],
            ]); ?>
        </div>
        <div class="charge-item-block-margin-top fill-info col-xs-12 charge-table-first">
            <?php if ($chargeRecordLogList['inspect_price'] != null ||  $chargeRecordLogList['check_price'] != null || $chargeRecordLogList['recipe_price'] != null || $chargeRecordLogList['cure_price'] != null || $chargeRecordLogList['diagnosis_price'] != null): ?>
                <?php if ($chargeRecordLogList['type'] == 1) :?>
                    <div class='title small-title-third'>收费项目</div>
                    <?php else: ?>
                    <div class='title small-title-third'>退费项目</div>
                <?php endif; ?>
            <?php endif; ?>
            <div class='grid-view table-responsive '>
                <table class="table font-3rem charge-table inspect-table">
                    <thead>
                    <?php if ($chargeRecordLogList['inspect_price'] != null || $chargeRecordLogList['check_price'] != null ||  $chargeRecordLogList['recipe_price'] != null || $chargeRecordLogList['cure_price'] != null || $chargeRecordLogList['diagnosis_price'] != null): ?>
                    <tr>
                        <th style="width: 44%;">项目名称</th>
                        <?php if ($chargeRecordLogList['type'] == 1) :?>
                            <th style="width: 11%;">折后金额(元)</th>
                        <?php else: ?>
                            <th style="width: 11%;">退费金额(元)</th>
                        <?php endif; ?>
                        <th style="width: 45%;"></th>
                    </tr>
                    <?php endif; ?>
                    </thead>
                    <tbody>
                    <?php if ($chargeRecordLogList['inspect_price'] != null): ?>
                        <tr data-key="2">
                            <td>实验室检查费用</td>
                            <td class="tr"><?php echo  $chargeRecordLogList['type'] == 1?$chargeRecordLogList['inspect_price']:'-'.$chargeRecordLogList['inspect_price']; ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($chargeRecordLogList['check_price'] != null): ?>
                        <tr data-key="2">
                            <td>影像学检查总费用</td>
                            <td class="tr"><?php echo  $chargeRecordLogList['type'] == 1?$chargeRecordLogList['check_price']:'-'.$chargeRecordLogList['check_price']; ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($chargeRecordLogList['recipe_price'] != null): ?>
                        <tr data-key="2">
                            <td>处方总费用</td>
                            <td class="tr"><?php echo  $chargeRecordLogList['type'] == 1?$chargeRecordLogList['recipe_price']:'-'.$chargeRecordLogList['recipe_price']; ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($chargeRecordLogList['cure_price'] != null): ?>
                        <tr data-key="2">
                            <td>治疗总费用</td>
                            <td class="tr"><?php echo  $chargeRecordLogList['type'] == 1?$chargeRecordLogList['cure_price']:'-'.$chargeRecordLogList['cure_price']; ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (isset($chargeRecordLogList['diagnosis_price'])): ?>
                        <tr data-key="2">
                            <td>诊疗总费用</td>
                            <td class="tr"><?php echo  $chargeRecordLogList['type'] == 1?$chargeRecordLogList['diagnosis_price']:'-'.$chargeRecordLogList['diagnosis_price']; ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($chargeRecordLogList['material_price'] != null && $printWay != 2): ?>
                        <tr data-key="2">
                            <td>其他费用</td>
                            <td class="tr"><?php echo  $chargeRecordLogList['type'] == 1?$chargeRecordLogList['material_price']:'-'.$chargeRecordLogList['material_price']; ?></td>
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
    <?php if($chargeRecordLogList['type'] == 1) :?>
        <div style="position: relative;min-height: 100px;" class="rebate-foot font-0px">
            <div class='foot-left rebate-type' style="width: 75%;position:absolute;bottom:0px;left: 0px;">
                <?php if ($chargeRecordLogList['pay_type']): ?>
                <label class='border add-padding-round font-3rem'>支付方式&nbsp&nbsp<span
                        class='left-price recipe-price'><?= ChargeRecord::$getType[$chargeRecordLogList['pay_type']]; ?></span>
                    <?php endif; ?>
            </div>
            <div class="block-right">
                <div class="font-3rem foot-left">
                    <?php
                    if ($chargeRecordLogList['type'] == 2) {
                        $fee_type = "退费合计：";
                    } else {
                        $fee_type = "应收费用：";
                    }
                    ?>

                    <?php echo $fee_type ?>
                    <label>
                        <?php
                                if($printWay == 2){
                                    echo isset($chargeRecordLogList['otherPrice']) ? Common::num($chargeRecordLogList['otherPrice']) : '0';
                                }else{
                                    echo isset($chargeRecordLogList['order_price']) ? Common::num($chargeRecordLogList['order_price']) : '0';
                                }
                        ?>元
                    </label>
                </div>
                <?php if ($chargeRecordLogList['discount_price']): ?>
                    <div class="font-3rem foot-left">
                        优惠金额：
                        <label>
                            <?= $printWay == 2 ? Common::num($chargeRecordLogList['otherDiscount']) . '元' : $chargeRecordLogList['discount_price'] . '元' ?>
                        </label>
                    </div>
                <?php endif; ?>
                <div class="font-3rem foot-left">
                    实收费用：
                    <label>
                        <?php 
                                if($printWay == 2){
                                    echo $chargeRecordLogList['otherIncome'] ? Common::num($chargeRecordLogList['otherIncome']) . '元' : '0.00元';
                                }else{
                                    echo $chargeRecordLogList['income'] ? $chargeRecordLogList['income'] . '元' : '0.00元';
                                }
                        ?>
                    </label>
                </div>

                <?php if (1 == $chargeRecordLogList['pay_type']): ?>
                    <div class="font-3rem foot-left">
                        找&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;零：
                        <label>
                            <?php 
                                    if($printWay == 2){
                                        echo !empty($chargeRecordLogList['otherChange'])?Common::num($chargeRecordLogList['otherChange']).'元':'';
                                    }else{
                                        echo !empty($chargeRecordLogList['change'])?$chargeRecordLogList['change'].'元':'';
                                    }
                            ?>
                        </label>
                    </div>
                <?php endif; ?>
                <?php if ($chargeRecordLogList['refund_price'] > 0): ?>
                    <div class="font-3rem foot-left">退费金额：
                        <label>
                            <?= $chargeRecordLogList['refund_price'] . '元'; ?>
                        </label>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div style="position: relative;min-height: 100px;" class="rebate-foot font-0px">
            <div class="block-right">
                <div class="font-3rem foot-left">
                    退费合计:
                    <label>
                        <?= isset($chargeRecordLogList['refund_price']) ? '- '.Common::num($chargeRecordLogList['refund_price']) : '0' ?>元
                    </label>
                </div>
            </div>
        </div>

    <?php endif ?>



    <div class="fill-info-buttom font-0px">
        <div class="rebate-foot-bottom-second fl">
            <div class="tow-line-buttom fl">
                <div class="line-margin-top">
                    <?php if ($chargeRecordLogList['type'] == 1) :?>
                        <p class="font-3rem charge-bottom width-75 child-patient-number">收费员：</p>
                        <?php else: ?>
                        <p class="font-3rem charge-bottom width-75 child-patient-number">退费员：</p>
                    <?php endif; ?>
                    <p class="font-3rem charge-bottom child-patient-number">
                        日期：<?= date('Y-m-d', $chargeRecordLogList['create_time']); ?></p>
                </div>

            </div>
        </div>
    </div>
    <span class="clearfix"></span>
</div>
