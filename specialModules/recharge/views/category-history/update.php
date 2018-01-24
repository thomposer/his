<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CategoryHistory */

$this->title = 'Update Category History: ' . ' ' . $model->f_physical_id;
$this->params['breadcrumbs'][] = ['label' => 'Category Histories', 'url' => ['index']];
/* $this->params['breadcrumbs'][] = ['label' => $model->f_physical_id, 'url' => ['view', 'id' => $model->f_physical_id]]; */
$this->params['breadcrumbs'][] = 'Update';
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/recharge/history.css') ?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="category-history-update col-xs-12">
    <div class="box-header with-border recharge-bg">
        <span class = 'left-title'><?= Html::encode($this->title) ?></span>
        <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['index'],['class' => 'right-cancel']) ?>
    </div>
    <div class = "box">
        <div class = "box-body">
        
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>