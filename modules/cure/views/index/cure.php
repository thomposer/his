<?php

use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\charge\models\ChargeRecord;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\CheckRecord */
/* @var $form yii\widgets\ActiveForm */
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/check/common.css');

?>

<div class="charge-record-form">
    <div class = 'cost-bg'>
        <h5 class = 'title'>选择你对 “<?= Html::encode($userInfo['username']);?>” 治疗项目</h5>
    </div>
    <?php
    $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal common']
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'cure')->checkboxList(ArrayHelper::map($cureList, 'id', 'name'))->label(false); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>

</div>
