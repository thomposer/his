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
     .report-modal-content{
         font-size: 14px;
         margin-top: 10px;
     }
     #ajaxCrudModal .modal-header{
         border-bottom: none;
     }
     #ajaxCrudModal .modal-body {
       padding: 15px 30px ;
    }

CSS;
$this->registerCss($css);
?>
<div>
    <div class="text-center report-modal-title">确认保存?</div>
    <div class="report-modal-content">系统根据您的输入内容判断该用户可能已经存在，确认要继续保存吗?继续保存将为该用户生成新的病历记录，您也可以先取消进行用户信息的修改。</div>
    <?php
    $form = ActiveForm::begin([
                'id' => 'report-patient',
                'action' => $actionUrl
    ]);
    ?>
    <?= Html::hiddenInput('postParam', $postParam) ?>
    <?php ActiveForm::end(); ?>
</div>


