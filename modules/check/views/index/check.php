<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/check/common.css');
?>

<div class="charge-record-form">
    <div class = 'cost-bg'>
        <h5 class = 'title'>选择你对 “<?= Html::encode($userInfo['username']);?>” 的检查项目</h5>
    </div>
    <?php
    $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal common']
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-12'>
    <?= $form->field($model, 'check')->checkboxList(ArrayHelper::map($checkList, 'id', 'name'))->label(false); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>

</div>
