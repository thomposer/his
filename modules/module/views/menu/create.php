<?php

use yii\helpers\Html;
use app\common\AutoLayout;

/* @var $this yii\web\View */
/* @var $model app\modules\module\models\Menu */

$this->title = '添加菜单';
$this->params['breadcrumbs'][] = ['label' => '模块菜单', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
        'titleList' => $titleList
    ]) ?>
        </div>
    </div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>