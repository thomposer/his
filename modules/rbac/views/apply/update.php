<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\Menu */

$this->title = '添加用户';
$this->params['breadcrumbs'][] = ['label' => 'Menus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>

<div class="menu-create col-xs-12">
    <div class = "box">
        <div class = "box-body">
            <?= $this->render('_form', [
            'model' => $model,
            'item_name' => $item_name,
            ]) ?>
        </div>
    </div>
</div>
<?php $this->endBlock()?>
<?php $this->beginBlock('renderJs')?>
        
<?php $this->endBlock()?>
<?php AutoLayout::end()?>