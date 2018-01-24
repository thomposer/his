<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Url;

$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';


$css = <<<CSS
    #ajaxCrudModal .modal-body {
         border-top:1px solid #ddd;
    }
CSS;
$this->registerCss($css);
?>

<!-- 医生列表 -->
<div class="row filemanager" id="j_doctorList" record_id="<?= $record_id ?>" appointment_doctor="<?= $appointment_doctor ?>">
    <?php foreach ($doctor as $v): ?>
        <div class="col-xs-6 col-sm-3 col-md-2 image-doctor">
            <?php if ($doctor_id == $v['id']): ?>
                <?= Html::img($public_img_path . 'triage/top-icon.png', ['class' => 'doct-top-icon']) ?>
            <?php endif; ?>
            <a data-url="<?= Url::to(['@triageTriageChosedoctor', 'doctorId' => $v['id'], 'recordId' => $record_id]) ?>" role="modal-remote" data-toggle="modal" data-modal-size="large">
                <div class="thumb-doctor">
                    <label for="<?= 'doct_radio' . $v['id'] ?>" class="J-chooseDoct">
                        <input type="radio" name="doctor_id" id="<?= 'doct_radio' . $v['id'] ?>" value="<?= $v['id'] ?>" class="hidden">
                        <div class="thmb-prev">
                            <img src="<?= $v['head_img'] ? Yii::$app->params['cdnHost'] . $v['head_img'] : $public_img_path . 'default.png' ?>" class="img-responsive" alt="" onerror="this.src='<?= $public_img_path ?>default.png'" >
                        </div>
                        <h5 class="fm-title text-nowrap"><?= Html::encode($v['username']) ?></h5>
                        <h5 class="fm-title text-nowrap" data-toggle="tooltip" data-html="false" data-placement="top" data-original-title="<?= Html::encode($v['name']) ?>"><?= Html::encode($v['name']) ?></h5>
                        <h5 class="fm-title text-nowrap" data-toggle="tooltip" data-html="false" data-placement="top" data-original-title="<?= Html::encode($v['appointmentTypeName']) ?>"><?= Html::encode($v['appointmentTypeName']) ?></h5>
                        <span class="font12">待接诊：<small class="red"><?= $v['to_diagnose'] ?></small>人</span>
                        <span class="font12">已接诊：<small class="red"><?= $v['diagnosed'] ?></small>人</span>
                    </label>
                </div>
            </a>
        </div>

    <?php endforeach; ?>
</div>
<!-- 医生列表 -->
<!--</div>-->
<div class="modal-footer modal-btn-col1" style= "margin-bottom: 10px">
    <!-- <button type="submit" class="btn btn-primary" id="j_chooseDoct">确定</button> -->
    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
</div>
