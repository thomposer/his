<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\specialModules\recharge\models\UserCard;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
use app\modules\spot\models\CardManage;

use rkit\yii2\plugins\ajaxform\Asset;

Asset::register($this);

$this->title = '会员卡';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/card/index.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="user-card-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <?php echo $this->render('@cardTopTabViewPath'); ?>
    <div class = "box delete_gap">
        <div class = 'row search-margin'>
            <div class="card-list-title">
                已激活卡列表 (共计<span> <?= $dataProvider->totalCount ?> </span>条)
            </div>
            <div class = 'col-sm-2 col-md-2'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/card-create', $this->params['permList'])): ?>
                    <?= Html::a("卡片验证", ['card-check'], ['class' => 'btn btn-default font-body2', 'data-pjax' => 0, 'data-toggle' => 'tooltip', 'role' => 'modal-remote','data-modal-size' => 'normal']) ?>
                <?php endif ?>
            </div>
            <div class = 'col-sm-10 col-md-10'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border header'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /* 'filterModel' => $searchModel, */
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
//                [
//                    'attribute' => 'f_status',
//                    'headerOptions' => ['class' => 'col-sm-1 col-md-1'],
//                    'value' => function ($model)use($cardInfo) {
//                return $cardInfo?UserCard::$getStatus[$cardInfo[$model->card_physical_id]['f_status']]:'';
//            }
//                ],
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
                        'card-check' => function($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/card-create', $this->params['permList'])) {
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', 'View'),
                                'aria-label' => Yii::t('yii', 'View'),
                                'data-pjax' => '0',
                            ]);
                            return Html::a('<span class="icon_button_view fa fa-cog" title="验证"  data-toggle="tooltip"></span>', $url, $options);
                        }
                            ]
                        ],
                    ],
                ]);
                ?>
            </div>
            <?php Pjax::end() ?>
        </div>
        <?php $this->endBlock(); ?>
        <?php $this->beginBlock('renderJs'); ?>
        
        <?php $this->endBlock(); ?>
        <?php AutoLayout::end(); ?>
