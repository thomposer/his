<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\module\models\Menu */

$this->title = '添加子模块';
$this->params['breadcrumbs'][] = ['label' => '模块列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-create">
    <?= $this->render('_childrenForm', [
        'model' => $model,
        'title' => $title
    ]) ?>

</div>
