<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use johnitvn\ajaxcrud\CrudAsset;
use app\assets\AppAsset;
use yii\grid\GridViewAsset;
GridViewAsset::register($this);
CrudAsset::register($this);

$this->title = '标签详情';
$this->params['breadcrumbs'][] = ['label' => '标签管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '标签详情';
$baseUrl = Yii::$app->request->baseUrl;
$attribute = $model->attributeLabels();

?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/spot/tag.css') ?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

    <div class="tag-update col-xs-12">
        <div class="box-header with-border tag-bg">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
            <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['index'],['class' => 'right-cancel']) ?>
        </div>
        <div class="tag-view">
            <?= $this->render('_viewTag', [
                'model' => $model,
            ]) ?>
            <?= $this->render('tagOrdersList', [
                'model' => $model,
                'dataProvider' => $dataProvider
                ]) ?>
        </div>
    </div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
<?php AppAsset::addScript($this, '@web/public/js/lib/common.js') ?>
<?php $this->endBlock(); ?>
<?php  AutoLayout::end()?>
