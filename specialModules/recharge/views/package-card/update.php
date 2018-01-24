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
$this->params['breadcrumbs'][] = ['label' => '套餐卡'];
$this->params['breadcrumbs'][] = ['label' => '卡片详情'];
$baseUrl = Yii::$app->request->baseUrl;
$id = Yii::$app->request->get('id');
$tabData = [
    'titleData' => [
        ['title' => '卡片信息', 'url' => Url::to(['@rechargeIndexPackageCardView', 'id' => $id,'type' => 1]), 'type' => 1],
        ['title' => '交易流水', 'url' => Url::to(['@rechargeIndexPackageCardFlow', 'id' => $id,'type' => 2]), 'type' => 2],
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
                <?=
                $this->render('_form', [
                    'model' => $model,
                    'cardList' => $cardList,
                    'vidateTime' => $vidateTime,
                    'canPatientCreate' => $canPatientCreate,//是否显示新增患者提示
                    
                ])
                ?>
            </div>
        </div>
        <?php Pjax::end() ?>
    </div>
<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>
