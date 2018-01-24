<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\patient\models\Patient;

$baseUrl = Yii::$app->request->baseUrl;
$c = count($similarUser);
?>
<?php
$css = <<<CSS
     #ajaxCrudModal .modal-body {
       padding: 15px 30px ;
    }

CSS;
$this->registerCss($css);
?>
<div>
    <?php foreach ($similarUser as $key => $patient): ?>
        <div class="patient-card" patientId="<?= $patient['id'] ?>" actionUrl="<?= $actionUrl ?>">
            <div class="patient-number">病历号：<?= $patient['patient_number'] ?></div>
            <?php
            $text = '患者信息：' . Html::encode($patient['username']) . ' (' . Patient::$getSex[$patient['sex']] . Patient::dateDiffage($patient['birthday']) . ')';
            if (!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@patientIndexView'), $this->params['permList'])) {
                $text = $text;
            } else {
                $text = Html::a($text, ['@patientIndexView', 'id' => $patient['id']], ['data-pjax' => 0, 'target' => '_blank']);
            }
            ?>
            <div class="patient-info"><?= $text ?></div>
            <div>手机号：<?= $patient['iphone'] ?></div>
            <div>就诊信息：<?= isset($recordNum[$patient['id']]) ? $recordNum[$patient['id']]['num'] : 0 ?></div>
            <div class="patient-icon"></div>
        </div>
        <?php if ($key != $c - 1): ?>
            <div class="list-empty"></div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>


