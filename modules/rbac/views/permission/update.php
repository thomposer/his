<?php

use yii\helpers\Html;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Permission */

$this->title = '更新权限: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content')?>
<div class="permission-update col-xs-12">
    <div class = "box">
        <div class = "box-body">
    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $category
    ]) ?>
        </div>
    </div>
</div>
<?php $this->endBlock()?>
<?php $this->beginBlock('renderJs')?>
<?php $this->endBlock()?>
<?php AutoLayout::end()?>