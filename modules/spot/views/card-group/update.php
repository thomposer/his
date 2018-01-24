<?php

use yii\helpers\Html;
use app\common\AutoLayout;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CardRechargeCategory */

$this->title = 'Update Card Recharge Category: ' . ' ' . $model->f_physical_id;
$this->params['breadcrumbs'][] = ['label' => 'Card Recharge Categories', 'url' => ['index']];
/* $this->params['breadcrumbs'][] = ['label' => $model->f_physical_id, 'url' => ['view', 'id' => $model->f_physical_id]]; */
$this->params['breadcrumbs'][] = 'Update';
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock(); ?>
<?=

$this->render('_form', [
    'model' => $model,
])
?>
<?php $this->beginBlock('renderJs') ?>

<?php

$this->endBlock()?>