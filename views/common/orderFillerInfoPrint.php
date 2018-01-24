<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/3
 * Time: 14:42
 */
use yii\helpers\Html;
use app\modules\outpatient\models\FirstCheck;
use app\modules\outpatient\models\AllergyOutpatient;
$recordId = Yii::$app->request->get('id');
$firstCheck = FirstCheck::getFirstCheckInfo($recordId);
$allergy = AllergyOutpatient::getAllergyByRecord($recordId);
$allergy = isset($allergy) ? $allergy[$recordId] : [];
?>
<div class="check-new border font-5rem">
    <p style="word-break: break-all">诊断信息：<?= Html::encode($firstCheck) ?></p>
    <p style="float: left;width: 10%;">　过敏史：</p>
    <p style="float: left;width: 90%;">
        <?php if(empty($allergy)): ?>
            <?php echo '无' ?>
        <?php else: ?>
            <?php
            echo $allergy[1]?'药物过敏：<span>' .  Html::encode($allergy[1]).'</span><br>':'';
            echo $allergy[2]?'食物过敏：<span>'.  Html::encode($allergy[2]).'</span><br>':'';
            echo $allergy[3]?'其它过敏：<span>'.  Html::encode($allergy[3]).'</span>':'';
            ?>
        <?php endif;?>
    </p>
</div>
