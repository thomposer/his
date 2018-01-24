<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\module\models\Menu */

$this->title = '初始化模块';
$this->params['breadcrumbs'][] = ['label' => '模块列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
