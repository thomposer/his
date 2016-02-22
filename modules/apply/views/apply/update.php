<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\apply\models\ApplyPermissionList */

$this->title = 'Update Apply Permission List: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Apply Permission Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="apply-permission-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
