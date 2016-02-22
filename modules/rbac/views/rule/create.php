<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Rule */

$this->title = 'Create Rule';
$this->params['breadcrumbs'][] = ['label' => 'Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-create">

   

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
