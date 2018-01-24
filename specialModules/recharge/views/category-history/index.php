<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveFormAsset;
use app\modules\spot\models\Spot;

ActiveFormAsset::register($this);
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\specialModules\recharge\models\search\CategoryHistorySearch */
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
#card-container,#card-desc-container{margin: 0 auto;width: 100%;padding: 10px}
#card-wrap,#card-desc-wrap{position: relative;overflow: hidden;margin-top: 5px }
#read-more,#card-read-more{padding: 0px;position:relative;z-index:666}
#read-more a,#card-read-more a{text-decoration: underline;color: #76A6EF}
.card-updown{
    background-color: #fff!important;
    border: 0 solid #CACFD8;
    box-shadow: 0px 6px 14px -4px #8190a7;/*opera或ie9*/
    border-radius: 6px!important;
}
CSS;
$this->registerCss($css);
?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="category-history-index col-xs-12">
    <div class="box-header with-border recharge-bg">
        <span class = 'left-title'><?= Html::encode($this->title) ?></span>
        <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', ['index'], ['class' => 'right-cancel']) ?>
    </div>
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <div class = "box delete_gap">
        <div class = 'row search-margin-flow'>
            <div class = 'col-sm-12 col-md-12 category-history-top-bar'>
                <div class="history-top-left">
                    <div class="category-history-top-card">
                        <div class="easyhin-card">医信科技</div>
                        <div class="easyhin-card-category text-overflow"><?= Html::encode($cardInfo['f_category_name']) ?></div>
                        <div class="easyhin-card-id">ID:<?= Html::encode($cardInfo['f_card_id']) ?></div>
                    </div>
                </div>

                <div class="history-top-right">
                    <div class="category-history-service-title" style="position: relative">

                        <div id="card-container" style="position: absolute;">
                            <span style="font-weight: bold;font-size: 12px;">服务折扣:</span>
                            <div id="card-wrap" >

                                <div style="line-height: 22px">
                                    <?php foreach ($categoryService as $val): ?>
                                        <?= Html::encode($val['spotName']) . '：' . Html::encode($val['discount']) ?>;<br>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div id="read-more"></div>
                        </div>
                    </div>
                    <div class="category-history-service-title" style="position: relative;bottom: -66px" id="category-history-service">
                        <div id="card-desc-container" style=" position: absolute;">
                            <span style="font-weight: bold;font-size: 12px;">卡片描述:</span>
                            <div id="card-desc-wrap" >
                                <div id="category-desc" style="line-height: 22px">
                                    <?= Html::encode($cardInfo['f_category_desc']) ?>
                                </div>
                            </div>
                            <div id="card-read-more"></div>
                        </div>

                    </div>
                    <?php if (0 == $cardInfo['f_is_logout']) echo Html::a('变更卡种', Url::to(['@cardRechargeHistoryCreate', 'id' => $record_id]), ['id' => 'change-card-category', 'class' => 'btn btn-default change-card-category', 'role' => 'modal-remote', 'data-modal-size' => 'large']) ?>
                </div>
            </div>

        </div>
        <div class="row card-history-title">
            <div class="card-history-title-hr">
                <div class="text">卡种变更历史</div>
                <div class="title-line"></div>
            </div>
        </div>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
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
                'f_create_time:datetime',
                'end_category_name',
                'beg_category_name',
                [
                    'attribute' => 'f_spot_id',
                    'value' => function ($dataProvider) {
                        if ($dataProvider->f_spot_id) {
                            return Spot::getSpotName($dataProvider->f_spot_id);
                        }
                    }
                ],
                'f_user_name',
                'f_change_reason',
            // 'f_user_name',
            // 'f_state',
            // 'f_create_time',
            // 'f_update_time',
            ],
        ]);
        ?>
    </div>
    <?php
    $js = <<<JS
    require(["$baseUrl" + '/public/js/recharge/category.js?v=' + '<?= $versionNumber ?>'], function (main) {
        main.init();
    })
JS;
    $this->registerJs($js);
    ?>
    <?php Pjax::end() ?>
</div>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>

<?php AutoLayout::end(); ?>

