<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\outpatient\models\CureRecord;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\pharmacy\models\PharmacyRecord;
$attribute = $model->attributeLabels();

$report['record_id'] = Yii::$app->request->get('id');
$report['type'] = 2;
$report['id'] = $val['id'];
if(isset($val['report_time'])){
    $report['report_time'] = $val['report_time'];
    $report['username'] = $val['username'];
}
$repiceInfo=PharmacyRecord::getRepiceInfo($report['record_id'],2,$report['id']);

if(isset($val['report_time'])){
    $report_time = $val['report_time'];
}

/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="my-show check-print">
    <div class="rebate-foot-bottom">
        <?php
        if ($spotConfig['logo_img']) {
            $logoImg = $spotConfig['logo_shape'] == 1 ?"clinic-img":"clinic-img-long";
            echo Html::img(Yii::$app->params['cdnHost'] . $spotConfig['logo_img'], ['class' => $logoImg, 'onerror' => 'javascript:this.src=\'' . $baseUrl . '/public/img/charge/img_click_moren.png\'']);
        }
        ?>
        <p class="rebate-date"><?= $spotConfig['pub_tel']?'Tel:'.Html::encode($spotConfig['pub_tel']):''; ?></p>
        <div class="children-sign">儿科</div>
    </div>
    <span class="clearfix"></span>
    <p class = 'title rebate-title' style = "font-size:16px; margin-top:-50px"><?= Html::encode($spotConfig['spot_name']) ?></p>
    <p class = 'title rebate-title'>影像报告</p>
    <p class = 'tc title font-3rem'>（<?= Html::encode($val['name']) ?>）</p>
    <div style="min-height: 750px;" class="print-main-contnet">
        <div class="fill-info">
            <p class = 'title small-title'>病历号：<?= $triageInfo['patient_number'] ?></p>
            <div class = 'patient-user'>
                <div>
                    <div class="total-column-three-part"><span class="column-name">姓名</span><span class="column-value"><?= Html::encode( $triageInfo['username']) ?></span></div>
                    <div class="tc total-column-three-part"><span class="column-name">性别</span><span class="column-value"><?= Patient::$getSex[$triageInfo['sex']]?></span></div>
                    <div class="tr total-column-three-part"><span class="column-name">年龄</span><span style="width:70%" class="column-value" ><?=  Patient::dateDiffage($triageInfo['birthtime']) ?></span></div>
                </div>
                <div class="line-margin-top">
                    <div class="total-column-three-part"><span class="column-name">出生日期</span><span style="width:58%;" class="column-value"><?= Html::encode($triageInfo['birth']) ?></span></div>
                    <div class="tc total-column-three-part"><span class="column-name">TEL</span><span style="width: 70%;"class="column-value"><?= Html::encode($triageInfo['iphone']) ?></span></div>
                    <div class="tr total-column-three-part"><span class="column-name">就诊科室</span><span class="column-value" style="width:58%;"><?= Html::encode($repiceInfo['second_department']) ?></span></div>
                </div>
                <div class="line-margin-top">
                    <div class="total-column-three-part"><span class="column-name">接诊医生</span><span style="width:60%" class="column-value"><?= Html::encode($repiceInfo['doctor']) ?></span></div>
                    <div class="tc total-column-three-part"><span class="column-name">开单时间</span><span style="width: 58%;"  class="column-value"><?= $repiceInfo['time'] ?></span></div>
                    <div class="tr total-column-three-part"><span class="column-name">报告时间</span><span style="width: 58%;" class="column-value"><?= $report_time?date('Y-m-d H:i:s',$report_time):'' ?></span></div>
                </div>
            </div>
        </div>

        <?php echo  $this->render(Yii::getAlias('@orderFillerInfoPrint')) ?>

        <div class="fill-info patient-user font-3rem">
            <div class = 'title'>检查所见</div>
            <div class="patient_info_content_top"> <?= Html::encode($val['description'])?></div>
            <div class = 'patient-info-block-top title'>诊断意见 </div>
            <div class="patient_info_content_top"><?= Html::encode($val['result'])?></div>
        </div>


    </div>


    <div class="fill-info-buttom font-0px">
        <p class="font-3rem">注：此检验报告仅对本次标本负责，如有疑问，请立即与 <b>影像科</b> 联系,谢谢合作</p>
        <div class="double-underline"></div>
        <p  class="rebate-write font-3rem">检查员：  </p>
        <p  class="rebate-write font-3rem">审核员：  </p>
    </div>

</div>


