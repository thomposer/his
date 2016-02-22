<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\ItemChild */

$this->title = $model->parent;
$this->params['breadcrumbs'][] = ['label' => 'Item Children', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-child-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'parent' => $model->parent, 'child' => $model->child], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'parent' => $model->parent, 'child' => $model->child], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'parent',
            'child',
            'values',
        ],
    ]) ?>

</div>
