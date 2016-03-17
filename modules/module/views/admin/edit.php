<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\module\models\Menu */

$this->title = '更新模块: ' . ' ' . $model->module_name;
$this->params['breadcrumbs'][] = ['label' => 'Menus', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="module-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
