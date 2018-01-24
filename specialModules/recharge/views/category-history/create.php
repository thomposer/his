<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CategoryHistory */

$this->title = '新增\'Category Histories\'';
$this->params['breadcrumbs'][] = ['label' => 'Category Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock(); ?>
<?=
$this->render('_form', [
    'model' => $model,
    'cardCategory' => $cardCategory,
    'id' => $record_id,
    'cardModel' => $cardModel,
    'cardService' => $cardService
])
?>