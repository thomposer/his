<?php

use app\modules\spot\models\Spot;
use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use app\common\Common;
use yii\helpers\Json;
use app\specialModules\recharge\models\CardRecharge;
use app\specialModules\recharge\models\CardFlow;
use yii\helpers\Url;
use yii\widgets\ActiveFormAsset;
use yii\validators\ValidationAsset;
use app\modules\user\models\User;

ValidationAsset::register($this);
ActiveFormAsset::register($this);

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\specialModules\recharge\models\search\CardRechargeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '卡片详情';
$this->params['breadcrumbs'][] = ['label' => '会员卡', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '充值卡'];
$this->params['breadcrumbs'][] = ['label' => '卡片详情'];
$baseUrl = Yii::$app->request->baseUrl;

$tabData = [
    'titleData' => [
        ['title' => '卡片信息', 'url' => Url::to(['@cardRechargePreview', 'id' => $record_id, 'type' => 1]), 'type' => 1],
        ['title' => '交易流水', 'url' => Url::to(['@cardRechargeFlow', 'id' => $record_id, 'type' => 2]), 'type' => 2],
        ['title' => '卡种及折扣', 'url' => Url::to(['@cardRechargeHistoryIndex', 'id' => $record_id, 'type' => 3]), 'type' => 3],
    ],
    'tabLevel' => 2
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/recharge/history.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<?php
$css = <<<CSS
#card-container,#card-desc-container{margin: 0 auto;width: 100%;}
#card-wrap,#card-desc-wrap{position: relative;overflow: hidden;margin-top: 5px }
#read-more,#card-read-more{padding: 0px;}
#read-more a,#card-read-more a{text-decoration: underline;color: #76A6EF}
.card-updown{
    background-color: #fff!important;
    border: 0 solid #CACFD8;
    box-shadow: 0px 6px 14px -4px #8190a7;/*opera或ie9*/
    border-radius: 6px!important;
}
.single-line {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        word-break: break-all;
        overflow: hidden;
        float: left;
        width: 92px;
}
CSS;
$this->registerCss($css);
?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<div class="card-recharge-index col-xs-12">
    <div class="box-header with-border recharge-bg">
        <span class = 'left-title'><?= Html::encode($this->title) ?></span>
        <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', ['index'], ['class' => 'right-cancel']) ?>
    </div>
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <div class = "box delete_gap">
        <div class = 'row search-margin-flow'>
            <div class="col-sm-12 flow-operation">
                <?php
                $sumAmount = $cardBasic['f_donation_fee'] + $cardBasic['f_card_fee'];
                $feeEnd = Common::num($sumAmount);
                $feeArr = explode('.', $feeEnd);
                ?>
                <div>
                    卡内余额：<span class="fee_int"><?= $feeArr[0] ?></span>
                    <span>.</span>
                    <span class="fee_point"><?= $feeArr[1] ?>元</span>
                </div>
                <div class="amount-color">基本账户余额:<?= Common::num($cardBasic['f_card_fee']) ?>元</div>
                <div class="amount-color">赠送账户余额:<?= Common::num($cardBasic['f_donation_fee']) ?>元</div>
                <div class="amount-color">已累计充值并赠送总金额:<?= Common::num($total) ?>元
                     <span  style='margin-bottom: 8px;word-break:break-all ;cursor: pointer;' >
                        <?php
                        if (!empty($cardUpgradeCategroy)) {
                            $showValue = '';
                            $num = 0;
                            foreach ($cardUpgradeCategroy as $key=>$val) {
                                if (5 == $num) {  // 超过限定卡数打点
                                    $showValue .= '<div style=\'margin-top: -5px;margin-bottom:12px\'>......</div>';
                                    break;
                                } else {
                                    if(($val['f_upgrade_amount']-$total) <=0){
                                        $showValue .= '<div style=\'margin-bottom: 8px;word-break:break-all\'>已充值【' .Common::num($total).'元】，可升级为【'.$val["categoryName"].'】</div>';
                                    }else{
                                        $showValue .= '<div style=\'margin-bottom: 8px;word-break:break-all\'>再充值【' .Common::num(($val["f_upgrade_amount"]-$total)) .'元】，可升级为【'.$val["categoryName"].'】</div>';
                                    }

                                }
                                $num++;
                            }
                            $i = '<span class="recharge-span"><i style="color: #99a3b1" class="fa fa-bullhorn  ml-9" data-toggle="tooltip" data-html="true" data-placement="right" data-original-title="' . $showValue . '"></i></span>';
                        } else {
                            $i = '';
                        }
                        echo $i;
                        ?>
                    </span>
                </div>
                <div class="btn-recharge-content">
                    <?php
                    if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/recharge', $this->params['permList'])) {
                        if (0 == $cardBasic['f_is_logout']) {
                            echo Html::a('充值', ['recharge', 'id' => $record_id], ['class' => 'btn  btn-register ', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'middle', 'data-pjax' => 1]);
                        }
                    }
                    if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/record', $this->params['permList'])) {
                        if (0 == $cardBasic['f_is_logout']) {
                            echo Html::a('手动登记', ['record', 'id' => $record_id], ['class' => 'btn btn-hollow ', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'middle', 'data-pjax' => 1]);
                        }
                    }
                    if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/subscribe', $this->params['permList'])) {
                        if (0 == $cardBasic['f_is_logout']) {
                            if ($cardBasic['f_message_subscribe'] == 2) {
                                $message = Html::tag('div', '确定要为用户订阅该卡片的“交易流水”短信吗?');
                                $btn = '订阅';
                            } else {
                                $message = Html::tag('div', '确定要为用户取消订阅该卡片的“交易流水”短信吗?');
                                $btn = '取消订阅';
                                $cancelClass = 'subscribe-btn';
                            }
                            echo Html::a($btn, ['subscribe', 'id' => $record_id], [
                                'class' => 'pull-right btn btn-hollow ' . $cancelClass,
                                'data-modal-size' => 'middle',
                                'data-pjax' => 1,
                                'data-confirm' => false,
                                'data-method' => false,
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-toggle' => 'tooltip',
                                'data-confirm-title' => '系统提示',
                                'data-delete' => true,
                                'data-confirm-message' => $message,
                            ]);
                            echo Html::tag('div', '交易流水短信：', ['class' => 'notice-msg pull-right']);
                        }
                    }
                    ?>

                </div>
            </div>
        </div>
        <div class=" card-total-discount-price">
            会员卡优惠金额：<?= $totalDiscountPrice; ?>元
        </div>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive add-table-padding', 'id' => 'card-flow-table'],
                'tableOptions' => ['class' => 'table table-hover table-border header'],
                'layout' => '{items}<div class="text-right">{pager}</div>',
                'pager' => [
                    //'options'=>['class'=>'hidden']//关闭自带分页

                    'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                    'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                    'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                    'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
                ],
                /* 'filterModel' => $searchModel, */
                'columns' => [
                    'f_create_time',
                    'attribute' => 'f_flow_item',
                    [
                        'attribute' => 'f_record_fee',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions' => ['class' => 'text-right'],
                        'value' => function ($dataProvider) {
                            $type = ($dataProvider->f_record_type == 2 || $dataProvider->f_record_type == 3 || $dataProvider->f_record_type == 6) ? '-' : '+';
                            $text = ($dataProvider->f_record_type == 2 || $dataProvider->f_record_type == 4 ) ? ($dataProvider->f_record_fee + $dataProvider->f_consum_donation) : $dataProvider->f_record_fee;
                            return $type . Common::num($text);
                    },
                    ],
                    [
                        'attribute' => 'f_card_fee_end',
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'f_record_type',
                        'value' => function ($dataProvider) {
                            return CardFlow::$getRecordType[$dataProvider->f_record_type];
                        },
                    ],
                    [
                        'attribute' => 'f_pay_type',
                        'value' => function ($dataProvider) {
                            return $dataProvider->f_pay_type ? CardFlow::$getPayType[$dataProvider->f_pay_type] : '一';
                        },
                    ],
                    [
                        'attribute' => 'f_operate_origin',
                        'value' => function ($dataProvider) {
                            return $dataProvider->f_operate_origin ? CardFlow::$getOperateOrigin[$dataProvider->f_operate_origin] : '一';
                        },
                    ],
                    [
                        'attribute' => 'f_channel_source',
                        'value' => function ($dataProvider) {
                            if ($dataProvider->f_channel_source == 1) {
                                return Spot::getSpotName($dataProvider->f_spot_id);
                            } else if ($dataProvider->f_channel_source == 2) {
                                return "妈咪知道APP";
                            } else {
                                return "";
                            }
                        }
                    ],
                    [
                        'attribute' => 'f_sale_id',
                        'value' => function($dataProvider) {
                            return User::getUserInfo($dataProvider->f_sale_id, 'username')["username"];
                        }
                    ],
                    [
                        'attribute' => 'f_remark',
                        'format' => 'raw',
                        'value' => function ($dataProvider)use($spotId) {
                            //操作是门诊收费而且是有与收费关联
                            if (in_array($dataProvider->f_operate_origin, [1, 4]) && $dataProvider->f_charge_record_log_id != 0) {
                                if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@chargeIndexTradeLog'), $this->params['permList'])) {
                                    return false;
                                } else {
                                    $remark = !empty($dataProvider->f_remark) ? $dataProvider->f_remark . ',' : '';
                                    $jump=($dataProvider->f_spot_id==$spotId)?Html::a('查看详情', Url::to(['@chargeIndexTradeLog', 'id' => $dataProvider->f_charge_record_log_id]), ['class' => 'view-trade-log', 'target' => '_blank', 'data-pjax' => 0]):'';
                                    return $remark . $jump;
                                }
                            } else {
                                return Html::encode($dataProvider->f_remark);
                            }
                        },
                    ],
                    'f_user_name',
                ],
            ]
                    );
                    ?>
            </div>
            <?php Pjax::end() ?>
        </div>
        <?php $this->endBlock(); ?>
        <?php $this->beginBlock('renderJs') ?>
        <script type="text/javascript">
            <?php $this->endBlock() ?>
            <?php AutoLayout::end(); ?>
