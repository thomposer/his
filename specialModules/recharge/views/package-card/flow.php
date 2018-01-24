<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\assets\AppAsset;
use yii\grid\GridViewAsset;
use yii\grid\GridView;
use app\specialModules\recharge\models\MembershipPackageCardFlow;
use app\modules\spot\models\Spot;
use app\modules\patient\models\Patient;

GridViewAsset::register($this);
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CardRecharge */

$this->title = '卡片详情';
$this->params['breadcrumbs'][] = ['label' => '会员卡', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '套餐卡'];
$this->params['breadcrumbs'][] = ['label' => '交易流水'];
$baseUrl = Yii::$app->request->baseUrl;
$tabData = [
    'titleData' => [
        ['title' => '卡片信息', 'url' => Url::to(['@rechargeIndexPackageCardView', 'id' => $id, 'type' => 1]), 'type' => 1],
        ['title' => '交易流水', 'url' => Url::to(['@rechargeIndexPackageCardFlow', 'id' => $id, 'type' => 2]), 'type' => 2],
    ],
    'tabLevel' => 2
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/recharge/history.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/recharge/membershipCard.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>

<div class="card-recharge-update col-xs-12">
    <div class="box-header with-border recharge-bg">
        <span class = 'left-title'><?= Html::encode($this->title) ?></span>
        <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', ['package-card'], ['class' => 'right-cancel']) ?>
    </div>
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <div class = "box delete_gap">
        <div class = "box-body">
            <div class="recipe-list-form col-md-12">
                <div id="membershipCardForm">
                    <div class="module-title">
                        <div class='row' style='margin-top:5px;margin-bottom:5px'>
                            <div class="col-sm-6">
                                <span class="module-title-adorn"></span><span class="module-title-content">服务信息</span>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-md-4'>
                                服务类型
                            </div>
                            <div class = 'col-md-4'>
                                总次数
                            </div>
                            <div class = 'col-md-4'>
                                剩余次数
                            </div>
                        </div>
                        <?php
                        if (!empty($data)) {
                            foreach ($data as $key => $value) {
                                echo "<div class='row'>";

                                echo "<div class='col-md-4'>";
                                echo "<div class='package-card-flow-list'>";
                                echo Html::encode($value['name']);
                                echo "</div>";
                                echo "</div>";

                                echo "<div class='col-md-4'>";
                                echo "<div class='package-card-flow-list'>";
                                echo $value['total_time'];
                                echo "</div>";
                                echo "</div>";

                                echo "<div class='col-md-4'>";
                                echo "<div class='package-card-flow-list'>";
                                echo $value['remain_time'];
                                echo "</div>";
                                echo "</div>";

                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class = 'row'>
                <div class="col-md-12">
                    <div class="other-operation">
                        <?php
                            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/package-record', $this->params['permList'])) {
                                echo Html::a('手动登记', ['package-record', 'id' => $id], ['class' => 'btn btn-registration ', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'middle', 'data-pjax' => 1]);
                            }
                        ?>
                    </div>
            	</div>
            	</div>
                <div class="flow-record">
                    <div class='row' style='margin-top:5px;margin-bottom:5px'>
                        <div class="col-sm-6">
                            <span class="module-title-adorn"></span><span class="module-title-content">交易记录</span>
                        </div>
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
                            'create_time:datetime',
                            'attribute' => 'flow_item',
                            [
                                'attribute' => 'patientName',
                                'value'=>  function ($dataProvider){
                                    $user_sex = Patient::$getSex[$dataProvider->sex];
                                    $dateDiffage = Patient::dateDiffage($dataProvider->birthday, time());
                                    $userName = Html::encode($dataProvider->patientName);
//                return $userName . '(' . $user_sex . ' ' . $dateDiffage . ')' . $firstRecord;
                                    $text = $userName . '(' . $user_sex . ' ' . $dateDiffage . ')';
                                     return $text;
                                    },
                                    'format' => 'raw'
                            ],
                            [
                                'attribute' => 'trans_detail',
                                'value' => function ($dataProvider)use($transDetail) {
                                    return isset($transDetail[$dataProvider->id])?$transDetail[$dataProvider->id]:'';
                                },
                            ],
                            [
                                'attribute' => 'transaction_type',
                                'value' => function ($dataProvider) {
                                    return MembershipPackageCardFlow::$getTransactionType[$dataProvider->transaction_type];
                                },
                            ],
                            [
                                'attribute' => 'pay_type',
                                'value' => function ($dataProvider) {
                                    return $dataProvider->pay_type?MembershipPackageCardFlow::$getPayType[$dataProvider->pay_type]:'--';
                                },
                            ],
                            [
                                'attribute' => 'operate_origin',
                                'value' => function ($dataProvider) {
                                    return MembershipPackageCardFlow::$getOperateOrigin[$dataProvider->operate_origin];
                                }
                            ],
                            [
                                'attribute' => 'channelSource',//来源渠道
                                'value' => function($dataProvider) {
                                    return $dataProvider->spot_id?Spot::getSpotName($dataProvider->spot_id):'';
                                }
                            ],
                            [
                                'attribute' => 'remark',
                                'format' => 'raw',
                                'value' => function ($dataProvider)use($spotId) {
                                    //操作是门诊收费而且是有与收费关联
                                    if (in_array($dataProvider->operate_origin, [1, 4]) && $dataProvider->charge_record_log_id != 0) {
                                        if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@chargeIndexTradeLog'), $this->params['permList'])) {
                                            return false;
                                        } else {
                                            $remark = !empty($dataProvider->remark) ? $dataProvider->remark . ',' : '';
                                            $jump = ($dataProvider->spot_id == $spotId) ? Html::a('查看详情', Url::to(['@chargeIndexTradeLog', 'id' => $dataProvider->charge_record_log_id]), ['class' => 'view-trade-log', 'target' => '_blank', 'data-pjax' => 0]) : '';
                                            return $remark . $jump;
                                        }
                                    } else {
                                        return Html::encode($dataProvider->remark);
                                    }
                                },
                            ],
                            'username',
                        ],
                    ]
                    );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <?php Pjax::end() ?>
        </div>

        <?php $this->endBlock() ?>
        <?php AutoLayout::end() ?>
