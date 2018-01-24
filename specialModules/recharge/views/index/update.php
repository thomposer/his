<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\assets\AppAsset;
use yii\grid\GridViewAsset;

GridViewAsset::register($this);
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CardRecharge */

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
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
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
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>

<div class="card-recharge-update col-xs-12">
    <div class="box-header with-border recharge-bg">
        <span class = 'left-title'><?= Html::encode($this->title) ?></span>
        <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', ['index'], ['class' => 'right-cancel']) ?>
    </div>
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <div class = "box delete_gap">
        <div class = "box-body">
            <?=
            $this->render('_form', [
                'model' => $model,
            ])
            ?>
        </div>
    </div>
    <?php Pjax::end() ?>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>