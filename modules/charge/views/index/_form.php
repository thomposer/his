<?php
use app\modules\charge\models\ChargeInfo;
use yii\grid\GridView;
use app\common\Common;
use yii\helpers\Html;
use app\modules\spot\models\RecipeList;
use app\modules\charge\models\Order;
use app\modules\charge\models\ChargeRecord;
use yii\widgets\Pjax;

$baseUrl = Yii::$app->request->baseUrl;
$action = Yii::$app->controller->action->id;
$total = 0;//费用合计
$discount = [];
if (isset($chargeType['originalPrice'])) {
    $total = Common::num(array_sum($chargeType['originalPrice']));
    $discount = ChargeRecord::getDiscount($total, 1, $discountPrice, $chargeType['chargeTotalDiscount'], $chargeType['cardTotalDiscount']);
}

?>

<?= $this->render('userInfo', ['userInfo' => $userInfo, 'baseUrl' => $baseUrl, 'doctorName' => $doctorName,'recordId' =>$recordId]) ?>
<p class='title float'><?php if ($action == 'refund') {
        echo '退费明细';
    } else {
        echo '收费明细';
    } ?></p>

<?php if ($action == 'update' && $userInfo['makeup'] == 1): ?>
    <p class='refund'><?= Html::img($baseUrl . '/public/img/charge/default_refund.png') ?><span>退费</span></p>
<?php elseif ($action == 'refund' && $userInfo['makeup'] == 1) : ?>
    <p class='charge-again'><?= Html::img($baseUrl . '/public/img/charge/default_refund.png') ?><span>重新收费</span></p>
<?php elseif ($action == 'create' && $userInfo['makeup'] == 1) : ?>
	
    <p class='create-discount'>
    	<?= Html::a('<i class = "fa fa-dollar discount-dollar"></i><span>折扣</span>', ['@apiChargeCreateDiscount', 'id' => $id], ['role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']) ?>
   	</p>
   	<p class = "update-material">
	   <?= Html::a('<i class = "material-dollar fa fa-pencil-square-o"></i><span>修改</span>', ['@apiChargeUpdateMaterial', 'id' => $id], ['role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']) ?>
	</p>
<?php endif; ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'grid-view table-responsive'],
    'tableOptions' => ['class' => 'table table-hover table-bordered'],
    'layout' => '{items}',

    'columns' => [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function ($dataProvider) use ($action){
                $cardType = ($action == 'update' && in_array($dataProvider['pay_type'],[7]));//服务卡支付项目不给退费
                $zeroType = ($action == 'update' && $dataProvider['pay_type'] != 8 && ($dataProvider['num'] * $dataProvider['unit_price'] - ($dataProvider['discount_price'] + $dataProvider['card_discount_price'])) == 0);//0元项不给退费
                if($cardType || ($dataProvider['pay_type'] == 5 && $zeroType)){//兼容历史数据
                    return ['data-type' => $dataProvider['type'], 'class' => 'check-type', 'data-value' =>2];
                }else if($zeroType){
                    return ['data-type' => $dataProvider['type'], 'class' => 'check-type', 'disabled' => 'disabled','data-value' => '1'];
                }else{
                    return ['data-type' => $dataProvider['type'], 'class' => 'check-type','data-paytype' => $dataProvider['pay_type'],'data-chargerecordid' => $dataProvider['charge_record_id']];
                }
                
            },
            'headerOptions' => ['class' => 'col-sm-1 col-md-1']
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'headerOptions' => ['class' => 'col-sm-3 col-md-3'],
            'value' => function ($dataProvider)use($recipeInspectState) {
                $text=Html::encode($dataProvider['name']);
                ($dataProvider['type'] == ChargeInfo::$inspectType)&&(isset($recipeInspectState['inspectState'][$dataProvider['outpatient_id']])&&($recipeInspectState['inspectState'][$dataProvider['outpatient_id']]['status']==4))&&$text.='<span class="text-red-mine">（已取消）</span>';
                $dataProvider['is_charge_again']&&$text.='<span class="text-red-mine">（重收）</span>';
                ($dataProvider['type'] == ChargeInfo::$recipeType)&&(isset($recipeInspectState['recipe'][$dataProvider['outpatient_id']])&&($recipeInspectState['recipe'][$dataProvider['outpatient_id']]['status']==5))&&$text.='<span class="text-red-mine">（已退药）</span>';

                if ($dataProvider['type'] == 9) {
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
            'label' => $entrance == 2 ? "退费项 名称":"收费项 名称"
        ],
        [
            'attribute' => 'unit',
            'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
            'value' => function ($dataProvider) {
                if ($dataProvider['type'] == ChargeInfo::$recipeType) {
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
            'value' => function ($dataProvider)use($entrance ){
                if ($entrance == 1) {
                    return $dataProvider['num'];
                }
                return "-".$dataProvider['num'];
            }
        ],
        [
            'attribute' => 'discount_price',
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
            'contentOptions' => ['class' => 'discountPrice'],
            'value' => function ($dataProvider) {
                return $dataProvider['discount_price'] == 0.00 ? ' --' : $dataProvider['discount_price'];
            }
        ],
        [
            'attribute' => 'discount_reason',
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
            'value' => function ($dataProvider) {
                return $dataProvider['discount_reason'] != '' ?$dataProvider['discount_reason'] : '--';
            }

        ],
        [
            'attribute' => 'card_discount_price',
            'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
            'contentOptions' => ['class' => 'cardDiscountPrice'],
            'value' => function ($dataProvider) {
                return $dataProvider['card_discount_price'] == 0.00 ? ' --' : $dataProvider['card_discount_price'];
            },
            'visible' => $action == 'create' ? false : true
        ],
        [
            'attribute' => '折后金额（元）',
            'headerOptions' => ['class' => 'col-sm-2'],
            'contentOptions' => ['class' => 'total_price'],
            'value' => function ($dataProvider) {
                return Common::num(abs($dataProvider['unit_price'] * $dataProvider['num'] - $dataProvider['discount_price'] - $dataProvider['card_discount_price']));
            }
        ],


    ],
]); ?>
<?php Pjax::end(); ?>
<div class='row margin'>
    <div class='col-xs-6 col-md-6'>
        <?php if (isset($chargeType[ChargeInfo::$priceType])): ?>
            <div class='padding'>
                诊疗费用：<span
                    class='left-price five-price'><?= Common::num(abs(array_sum($chargeType[ChargeInfo::$priceType]))) . (array_sum($chargeType['discount'][ChargeInfo::$priceType]) != 0 ? '（已优惠' . Common::num(array_sum($chargeType['discount'][ChargeInfo::$priceType])) . '）' : ''); ?></span>
                <?php
                if ($chargeType['fee_remarks'] != '') {
                    echo '（' . Html::encode($chargeType['fee_remarks']) . '）';
                }
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($chargeType[ChargeInfo::$inspectType])): ?>
            <?php $inspectDiscountInfo = array_sum($chargeType['discount'][ChargeInfo::$inspectType]) != 0 ? '（已优惠' . Common::num(array_sum($chargeType['discount'][ChargeInfo::$inspectType])) . '）' : ''; ?>
            <div class='padding'>
                实验室检查总费用：<span
                    class='left-price inspect-price'><?= Common::num(abs(array_sum($chargeType[ChargeInfo::$inspectType]))) . $inspectDiscountInfo; ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($chargeType[ChargeInfo::$checkType])): ?>
            <?php $checkDiscountInfo = array_sum($chargeType['discount'][ChargeInfo::$checkType]) != 0 ? '（已优惠' . Common::num(array_sum($chargeType['discount'][ChargeInfo::$checkType])) . '）' : ''; ?>
            <div class='padding'>
                影像学检查总费用：<span
                    class='left-price check-price'><?= Common::num(abs(array_sum($chargeType[ChargeInfo::$checkType]))) . $checkDiscountInfo; ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($chargeType[ChargeInfo::$cureType])): ?>
            <?php $cureDiscountInfo = array_sum($chargeType['discount'][ChargeInfo::$cureType]) != 0 ? '（已优惠' . Common::num(array_sum($chargeType['discount'][ChargeInfo::$cureType])) . '）' : ''; ?>
            <div class='padding'>
                治疗总费用：<span
                    class='left-price cure-price'><?= Common::num(abs(array_sum($chargeType[ChargeInfo::$cureType]))) . $cureDiscountInfo; ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($chargeType[ChargeInfo::$recipeType])): ?>
            <?php $recipeDiscountInfo = array_sum($chargeType['discount'][ChargeInfo::$recipeType]) != 0 ? '（已优惠' . Common::num(array_sum($chargeType['discount'][ChargeInfo::$recipeType])) . '）' : ''; ?>

            <div class='padding'>
                处方总费用：<span
                    class='left-price recipe-price'><?php echo Common::num(abs(array_sum($chargeType[ChargeInfo::$recipeType]))) . $recipeDiscountInfo; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (isset($chargeType[ChargeInfo::$materialType])): ?>
            <?php $materialDiscountInfo = array_sum($chargeType['discount'][ChargeInfo::$materialType]) != 0 ? '（已优惠' . Common::num(array_sum($chargeType['discount'][ChargeInfo::$materialType])) . '）' : ''; ?>

            <div class='padding'>
                其他总费用：<span
                    class='left-price material-price'><?php echo Common::num(abs(array_sum($chargeType[ChargeInfo::$materialType]))) . $materialDiscountInfo; ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($chargeType[ChargeInfo::$packgeType])): ?>
            <?php $packageDiscountInfo = array_sum($chargeType['discount'][ChargeInfo::$packgeType]) != 0 ? '（已优惠' . Common::num(array_sum($chargeType['discount'][ChargeInfo::$packgeType])) . '）' : ''; ?>

            <div class='padding'>
                医嘱套餐总费用：<span
                    class='left-price package-price'><?php echo Common::num(abs(array_sum($chargeType[ChargeInfo::$packgeType]))) . $packageDiscountInfo; ?></span>
            </div>
        <?php endif; ?>
        
        <div class="padding">

            <?php
            if ($discountReason) {
                echo '<span class="refund-reason-color">优惠原因：</span>';
                echo Html::encode($discountReason);
            }
            ?>
        </div>
        <div class="padding">
            <?php
                if ($action == 'refund') {
                    echo '<span class="refund-reason-color">退费原因：</span>';
                    $refundInfo = array_values($refundInfo);
                    foreach ($refundInfo as $key => $value) {
                        if($key > 0){
                            echo '<span style="width: 70px;display: inline-block;"></span>' . ( $key + 1 ).'） '.Html::encode($value['reason']) . ( $value['reasonDescription'] ? ' — ' . Html::encode($value['reasonDescription']) : '') . ($value['name'] ? '（' . Html::encode(implode('；', $value['name'])) . '）' : '') . '<br/>';
                        }else{
                            echo ( $key + 1 ).'） '.Html::encode($value['reason']) . ( $value['reasonDescription'] ? ' — ' . Html::encode($value['reasonDescription']) : '') . ($value['name'] ? '（' . Html::encode(implode(',', $value['name'])) . '）' : '') . '<br/>';
                        }
                    }
                    
                }
            ?>
        </div>
    </div>
    <div class='col-xs-6 col-md-6 right'>
        <?php
        if ($action == 'refund') {
            $fee_type = "退费合计";
        } else {
            $fee_type = "应收费用";
        }
        ?>
        <label class='selected-text col-xs-12 col-md-12'>应收费用：
            <div class='total-price old-selected-total-price'><?= $total; ?></div>
        </label>
        <label class='selected-text col-xs-12 col-md-12'>优惠金额：
            <div class='total-price discount-total-price'>
                <?php
                echo $discount['oneDiscount'];
                ?>
            </div>
        </label>

        <label class='selected-text col-xs-12 col-md-12'>实际应付：
            <div
                class='total-price receiptAmount'><?= !empty($discount['receiptAmount']) ? Common::num(abs($discount['receiptAmount'])) : ''; ?></div>
        </label>

        <?php if ($action != 'create' && isset($chargeType['payType']) && !empty($chargeType['payType'])): ?>
            <label class='selected-text col-xs-12 col-md-12'>支付方式：
                <div class='payType'>
                    <?= implode(',',array_unique(explode(',', $chargeType['payType']))); ?>
                </div>
            </label>
        <?php endif; ?>
        <?php if ($action == 'refund'): ?>
            <label class='selected-text col-xs-12 col-md-12 refundAmount'><?php echo $fee_type ?>：
                <div
                    class='total-price'><?= !empty($discount['receiptAmount']) ? '-'. $discount['receiptAmount'] : ''; ?></div>
            </label>
        <?php endif; ?>


    </div>
</div>
<form id="ModalRemoteConfirmForm">
    <input type='hidden' name='pks' id='selectIds' value=''>
</form>
        