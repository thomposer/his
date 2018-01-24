<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\common\Common;
use app\specialModules\recharge\models\CardRecharge;
use yii\widgets\Pjax;
use app\assets\AppAsset;
use app\modules\patient\models\Patient;
use app\specialModules\recharge\models\MembershipPackageCard;
use app\modules\spot\models\CardManage;
?>
<div class="cols-xs-12">
	<div class="row add-table-padding">
            <div class="col-sm-6 title-item">
                <span class="item-num"></span><span class="item-text">充值卡</span>
            </div>
    </div>
    <?php Pjax ::begin(['id' => 'recharge-crud-datatable-pjax', 'timeout' => 5000,'enablePushState' => false]) ?>
	
	<?=
        GridView::widget([
            'dataProvider' => $rechargeCardDataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border appointment-table'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            'columns' => [
                'f_user_name',
                [
                    'attribute' => 'f_baby_name',
                    'headerOptions' => ['class' => 'col-md-3'],
                ],
                [
                    'attribute' => 'f_sale_id',
                    'value' => function($dataProvider)use($doctorListInfo) {
                        return $doctorListInfo[$dataProvider->f_sale_id]['username'];
                    }
                ],
                'category_name',
                [
                    'attribute' => 'f_card_fee',
                    'contentOptions' => ['class' => 'text-right'],
                    'headerOptions' => ['class' => 'text-right'],
                    'value' => function ($dataProvider) {
                           return Common::num(($dataProvider->f_card_fee + $dataProvider->f_donation_fee)) ;
                    },
                ],
                [
                    'attribute' => 'f_buy_time',
                    'value' => function ($dataProvider) {
                        if ($dataProvider->f_buy_time) {
                            return date("Y-m-d H:i", $dataProvider->f_buy_time);
                        } else {
                            return '';
                        }
                    },
                ],
                [
                    'attribute' => 'f_is_logout',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-md-1'],
                    'value' => function ($dataProvider) {
                        return 0 == $dataProvider->f_is_logout ? CardRecharge::$getIsLogout[$dataProvider->f_is_logout] : "<span class='red'>" . CardRecharge::$getIsLogout[$dataProvider->f_is_logout] . "</span>";
                    },
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{preview}',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                    'buttons' => [
                        'preview' => function ($url, $model, $key) {
                            if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@cardRechargePreview'), $this->params['permList'])) {
                                return Html::a('查看', ['@cardRechargePreview', 'id' => $model->f_physical_id], ['class' => 'a-card', 'data-toggle' => 'tooltip', 'data-pjax' => 0,'target' => '_blank']);
                            } else {
                                return false;
                            }
                        },
                            ]
                        ],
                    ],
                ]);
     ?>
    <?php Pjax::end();?>
    <div class="row add-table-padding">
            <div class="col-sm-6 title-item">
                <span class="item-num"></span><span class="item-text">套餐卡</span>
            </div>
    </div>
	<?=
        GridView::widget([
            'dataProvider' => $membershipPackageCarddataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border appointment-table'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            'columns' => [
                [
                    'attribute' => 'patientInfo',
                    'value' => function ($searchModel) {
                        $birth = Patient::dateDiffage($searchModel->birthday, time());
                        $text = Html::encode($searchModel->username) . '( ' . Patient::$getSex[$searchModel->sex] . ' ' . $birth . ' )';
                        return $text;
                    },
                    'format' => 'raw',
                ],
                'name',
                [
                    'attribute' => 'active_time',
                    'format' => 'datetime',
                ],
                [
                    'attribute' => 'validity_period',
                    'value' => function($searchModel){
                        $year = date('Y',$searchModel->create_time);
                        $time = date('m-d H:i:s',$searchModel->create_time);
                        $vidateTime = ($year + $searchModel->validity_period) . '-'.$time;
                        return $vidateTime;
                    },
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'status',
                    'value' => function($searchModel){
                        $year = date('Y',$searchModel->create_time);
                        $time = date('m-d H:i:s',$searchModel->create_time);
                        $vidateTime = ($year + $searchModel->validity_period) . '-'.$time;
                        if(time() > strtotime($vidateTime) ){
                            return MembershipPackageCard::$cardStatus[2];
                        }
                        return MembershipPackageCard::$cardStatus[$searchModel->status];
                    },
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'template' => '{view}',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                    'buttons' => [
                        'view' => function ($url, $model, $key)use($spotId) {
                            if ((isset($this->params['permList']['role']) || in_array(Yii::getAlias('@rechargeIndexPackageCardView'), $this->params['permList'])) && $spotId == $model->spot_id) {
                                return Html::a('查看', ['@rechargeIndexPackageCardView', 'id' => $model->id], ['data-pjax' => 0,'style' => 'margin-right:10px','target' => '_blank']);
                            } else {
                                return false;
                            }
                        },
                
                    ]
                ],
            ],
        ]);
     ?>
    <div class="row add-table-padding">
            <div class="col-sm-6 title-item">
                <span class="item-num"></span><span class="item-text">服务卡</span>
            </div>
    </div>
	<?=
        GridView::widget([
            'dataProvider' => $userCardDataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border appointment-table'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            'columns' => [
                [
                    'attribute' => 'card_id',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                ],
                [
                    'attribute' => 'card_type_code',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'value' => function ($model)use($cardInfo) {
                        return $cardInfo?$cardInfo[$model->card_id]['f_card_type_code']:'';
                    }
                ],
                [
                    'attribute' => 'cardName',
                    'value' => function ($model)use($cardInfo){
                        $card_type_code=$cardInfo?$cardInfo[$model->card_id]['f_card_type_code']:'';
                        return isset(CardManage::$cardTypeCode[$card_type_code])?CardManage::$cardTypeCode[$card_type_code]:'';
                    }
                ],
                [
                    'attribute' => 'f_card_desc',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'value' => function ($model)use($cardInfo) {
                        return $cardInfo?$cardInfo[$model->card_id]['f_card_desc']:'';
                    }
                ],
                [
                    'attribute' => 'user_name',
                    'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                    'value' => function ($model) {
                        return $model->user_name . ' ' . $model->phone;
                    }
                ],
                [
                    'attribute' => 'f_activate_time',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'value' => function ($model)use($cardInfo) {
                        $f_activate_time = $model->f_activate_time;
                        return $f_activate_time ? date('Y-m-d H:i:s', $f_activate_time) : '';
                    }
                ],
                [
                    'attribute' => 'f_invalid_time',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'value' => function ($model)use($cardInfo) {
                        $f_invalid_time = $model->f_invalid_time;
                        return $f_invalid_time ? date('Y-m-d H:i:s', $f_invalid_time) : '';
                    }
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
                    'template' => '{card-check}',
                    'buttons' => [
                        'card-check' => function($url, $model, $key)use($spotId) {
                            if ((!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@rechargeIndexCardCreate'), $this->params['permList'])) || $spotId != $model->spot_id) {
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', 'View'),
                                'aria-label' => Yii::t('yii', 'View'),
                                'data-pjax' => '0',
                                'target' => '_blank'
                            ]);
                            return Html::a('<span class="icon_button_view fa fa-cog" title="验证"  data-toggle="tooltip"></span>', ['@rechargeIndexCardCheck','id' => $model->id], $options);
                        }
                    ]
                ],
            ],
        ]);
     ?>
</div>
