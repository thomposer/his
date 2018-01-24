<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\awpmanage\models\Service */

$this->title = '修改公众号: ' . ' ' . $model->wxname;
$this->params['breadcrumbs'][] = ['label' => '公众号列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->wxname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="service-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
