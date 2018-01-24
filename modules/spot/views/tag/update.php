<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Tag */
?>
<div class="tag-update">

    <?= $this->render('_form', [
        'model' => $model,
        'haveUnion' => $haveUnion,
    ]) ?>

</div>
