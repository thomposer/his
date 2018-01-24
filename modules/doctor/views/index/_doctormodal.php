<?php

use yii\helpers\Html;
use app\modules\patient\models\Patient;

$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
/* @var $this yii\web\View */
/* @var $model app\modules\triage\models\Triage */
/* @var $form yii\widgets\ActiveForm */
//$attribute = $model->attributeLabels();
?>


<!--<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            ...
        </div>
    </div>
</div>-->
<?php //  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('contents') ?>

<?php $this->endBlock(); ?>
<!-- 医生列表 -->
<div class="row filemanager" id="j_doctorList">
    <?php foreach ($patient as $v): ?>
        <div class="col-xs-6 col-sm-3 col-md-2 image-doctor">
            <div class="thumb-doctor" data-url="<?= yii\helpers\Url::to(['@patientIndexView', 'id' => $v['id']]) ?>">

                <label for="<?= 'doct_radio' . $v['id'] ?>" class="J-chooseDoct">
                    <input type="radio" name="doctor_id" id="<?= 'doct_radio' . $v['id'] ?>" value="<?= $v['id'] ?>" class="hidden">
                    <div class="thmb-prev">
                        <a>
                            <img src="<?= $v['head_img'] ? Yii::$app->params['cdnHost'].$v['head_img'] : $public_img_path . 'default.png' ?>" width="95" height="95" class="img-responsive" alt="" onerror="this.src='<?= $public_img_path?>default.png'" >
                        </a>
                    </div>
                    <h5 class="fm-title text-nowrap"><?= Html::encode($v['username']) ?> </h5>
                    <span class="font12 text-nowrap"><?= Patient::$getSex[$v['sex']] ?> <?= Patient::dateDiffage($v['birthday'])?> </span>
                    <span class="font12"><?= $v['iphone']?$v['iphone']:'' ?> </span>
                </label>

            </div>
        </div>
    <?php endforeach; ?>
</div>
<!-- 医生列表 -->


