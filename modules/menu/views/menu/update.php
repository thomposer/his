<?php

use yii\helpers\Html;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\Menu */

$this->title = 'Update Menu: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Menus', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="menu-update col-xs-12">
    <div class = "box">
        <div class = "box-body">
            <h2><?= Html::encode($this->title) ?></h2>
        
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