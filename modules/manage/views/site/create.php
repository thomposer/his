<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\awpmanage\models\Service */

$this->title = '添加公众号';
$this->params['breadcrumbs'][] = ['label' => '公众号列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
