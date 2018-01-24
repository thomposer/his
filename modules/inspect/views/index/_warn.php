<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php
$css = <<<CSS
     .report-modal-title{
         font-size: 20px;
         color:#445064;
     }
    .warn-text{
        margin-top:20px;
   }

CSS;
$this->registerCss($css);
?>
<div>
    <div class="row">
        <div class='col-md-12 warn-text'>
            <span class="red">注意</span>：<?= Html::encode($patientInfo['username']) ?>的<span class="red"><?= Html::encode($warning[$model->id]) ?></span>出现危机值，请立即告知<span class="red"><?= Html::encode($triageInfo['doctorName'])?></span>医生做出相应处理
        </div>

    </div>
    <?php
    $form = ActiveForm::begin([
                'id' => 'report-patient',
    ]);
    ?>
    <?php ActiveForm::end(); ?>
</div>