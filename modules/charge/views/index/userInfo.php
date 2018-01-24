<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use yii\helpers\Url;
$action = Yii::$app->controller->action->id;
$recordId = Yii::$app->request->get('id');
?>

<div class = 'patient-info'>
    <p class = 'title'>患者信息</p>
    <div class = 'row patient-margin'>
        <div class = 'patient-img-header'>
            <div class = 'patient-img'>
            <?= Html::img($userInfo['head_img']?Yii::$app->params['cdnHost'].$userInfo['head_img']:'@web/public/img/common/default_patient.png',['class' => 'charge-img','onerror' => 'this.src = "'.$baseUrl . '/public/img/common/default_patient.png"']) ?>
            </div>
        </div>
        <div class = 'patient-user'>

            <p>姓名：<?= Html::encode($userInfo['username']) ?><?php echo $userInfo['first_record']==1?'<span class="patient-first-record">新</span>':'' ?></p>
            <p>性别：<?= Patient::$getSex[$userInfo['sex']] ?></p>
            <p>年龄：<?= Patient::dateDiffage($userInfo['birthday'],time()) ?></p>
            <p>手机号：<?= Html::encode($userInfo['iphone']) ?> <span class="charge-doctor">接诊医生：<?= $doctorName?Html::encode($doctorName):'--' ?></span>
                <?php  if(($action == 'update' || $action =='refund') && ( isset($this->params['permList']['role']) || in_array(Yii::getAlias('@chargeIndexViewChargeRecordLog'), $this->params['permList'])) ) :?>
                        <span class="charge-doctor">
                            <?= Html::a("查看交易流水", '#', [
                                'data-toggle' => 'modal',
                                'role' => 'modal-remote',
                                'data-modal-size' => 'large',
                                'class' => 'btn btn-default',
                                'data-url' => Url::to(['@chargeIndexViewChargeRecordLog', 'recordId' => $recordId])
                            ]) ?>
                        </span>
                <?php endif?>
            </p>
        </div>
    </div>
</div>
