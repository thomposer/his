<?php

use app\modules\pharmacy\models\PharmacyRecord;
use yii\helpers\Html;

$recordType = 1;
if (isset($type)) {
    $recordType = $type;
}
$listId = 0;
if (isset($id)) {
    $listId = $id;
}
$repiceInfo = PharmacyRecord::getRepiceInfo($record_id, $recordType, $listId);
$allergy = isset($repiceInfo['allergy'][$record_id]) ? $repiceInfo['allergy'][$record_id] : [];
?>
<?php $this->beginBlock('renderCss') ?>
<?php
$css = <<<CSS
     .dispense-form-doc{
        margin: 6px 0;
    }
    .dispense-form-doc .center{
        text-align: center
    }
    .dispense-form-doc .right{
        text-align: right
    }
    .dispense-allergy{
        background: #F4F5F7;
        border-radius: 4px;
        border: 1px solid lightgray;
        margin: 6px 0px;
        padding: 12px 12px 12px 0px;
    }
    .doctor-info{
        padding-left:0px
   }
    .allergy{
        margin-top:22px;
        margin-bottom:0px;
    }
}
CSS;
$this->registerCss($css);
?>
<?php $this->endBlock() ?>

<div class="row font12 dispense-form-doc">
    <div class="col-xs-4 doctor-info">
        <span>接诊医生：<?= Html::encode($repiceInfo['doctor']) ?></span>
    </div>
    <div class="col-xs-4">
        <span>开单时间：<?= $repiceInfo['time'] ?></span>
    </div>
    <div class="col-xs-4">
        <span>所属诊室：<?= Html::encode($repiceInfo['room']) ?></span>
    </div>
</div>
<?php if (isset($report_time)): ?>
    <div class="row font12 dispense-form-doc">
        <div class="col-xs-4 doctor-info">
            <span>报告人：<?= Html::encode($username) ?></span>
        </div>
        <div class="col-xs-5">
            <span>报告时间：<?= $report_time ? date('Y-m-d H:i:s', $report_time) : '' ?></span>
        </div>
    </div>
<?php endif; ?>
<div class="row font12 dispense-allergy">
    <div class="col-xs-12">
        <p style="word-break: break-all">诊断信息：<?= Html::encode($repiceInfo['first_check']) ?></p>
        <div style="float: left">　过敏史：</div>
            <div style="float: left; width: 90%; word-break: break-all;">
                <?php if(empty($allergy)): ?>
                <?php echo '无' ?>
                <?php else: ?>
                <?php 
                    echo $allergy[1]?'药物过敏：<span class="allergy-red">' .  Html::encode($allergy[1]).'</span><br>':'';
                    echo $allergy[2]?'食物过敏：<span class="allergy-red">'.  Html::encode($allergy[2]).'</span><br>':'';
                     echo $allergy[3]?'其它过敏：<span class="allergy-red">'.  Html::encode($allergy[3]).'</span>':'';
                ?>
                <?php endif;?>
            </div>
    </div>
</div>