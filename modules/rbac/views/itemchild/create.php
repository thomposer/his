<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\ItemChild */

$this->title = 'Create Item Child';
$this->params['breadcrumbs'][] = ['label' => 'Item Children', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-child-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
