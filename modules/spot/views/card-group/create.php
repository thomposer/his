<?php

use yii\helpers\Html;
use app\common\AutoLayout;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CardRechargeCategory */

$this->title = '新增';
$this->params['breadcrumbs'][] = ['label' => 'Card Recharge Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>