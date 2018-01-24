<?php

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\Room */
$baseUrl = Yii::$app->request->baseUrl;
?>

<div class="room-view col-xs-12">
    <?= $this->render('@spotIndexForm',['model' => $model]); ?>
</div>



