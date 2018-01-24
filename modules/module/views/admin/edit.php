<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\module\models\Menu */

$this->title = '编辑模块';
$this->params['breadcrumbs'][] = ['label' => '模块列表', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
