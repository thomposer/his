<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\card\models\UserCard */

$this->title = '详细信息';
$this->params['breadcrumbs'][] = ['label' => '会员卡', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/card/index.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class="user-card-create col-xs-12">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['card-index']), ['class' => 'right-cancel second-cancel']) ?>
        </div>
        <div class = "box-body">    

            <?=
            $this->render('_form', [
                'model' => $model,
                'record' => $record,
                'service' => $service,
                'left' => $left,
            ])
            ?>
        </div>
    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var f_status = '<?= $record['f_status'] ?>';
    require(["<?= $baseUrl ?>" + "/public/js/card/create.js?v="+'<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>