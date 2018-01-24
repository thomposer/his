<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Url;
use rkit\yii2\plugins\ajaxform\Asset;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
/* @var $form yii\widgets\ActiveForm */
Asset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\triage\models\Triage */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/triage/triageModal.css') ?>
<?php $this->endBlock() ?>
    <div class="fit-content add-line">
    <p class="basic-infomation">
        <?= Html::tag('span', '执行人：' . Html::encode($model['executor'])) ?>
        <?= Html::tag('span', '执行时间：' . date('Y-m-d H:i', $model['execute_time']), ['class' => 'add-margin-left']) ?>
    </p>
    <p>
        <?= Html::tag('span', '护理项：' . Html::encode($model['name'])) ?>
    <div class="record-container">
        <?= Html::tag('span', '内容：') ?>
        <div>
            <?= Html::tag('textarea', Html::encode($model['content']), ['class' => 'nursing-content','disabled'=>'true']) ?>
        </div>
    </div>
    <p class="basic-infomation">
        <?= Html::tag('span', '记录人：' . Html::encode($model['username'])) ?>
        <?= Html::tag('span', '记录时间：' . date('Y-m-d H:i:s', $model['create_time']), ['class' => 'add-margin-left']) ?>
    </p>
    <a href="#" class="btn btn-default btn-form close-btn-style" data-toggle="tooltip" role="modal-remote"
       data-modal-size="large" data-url="<?= Url::to(['@triageTriageModal', 'id' => $recordId, 'recordId' => $recordId]) ?>" >关闭</a>
    </div>
<?php
$this->registerJs("") ?>