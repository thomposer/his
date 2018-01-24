<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CardRechargeCategory */
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/spot/cardCategory.css') ?>
<?php $this->endBlock(); ?>
<?=

$this->render('_form', [
    'model' => $model,
    'cardCategory' => $cardCategory,
    'tagList' => $tagList,
    'cardDiscountList' => $cardDiscountList
])
?>
<?php $this->beginBlock('renderJs') ?>

<?php

$this->endBlock()?>