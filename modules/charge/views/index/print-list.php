<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$baseUrl = Yii::$app->request->baseUrl;
?>

<div class="charge-print">
    <div class = 'application-bg'>
        <h5 class = 'title'>请选择您要打印的清单</h5>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12 charge-print-list' data-id="<?= $logId ?>">
            <?= Html::checkboxList('printList',[1],ArrayHelper::map($printList, 'id', 'name')); ?>
        </div>
    </div>
</div>
<?php
    $this->registerCss('
        .charge-print .title{
            font-size: 16px;
            font-weight: normal;
        }
        .charge-print-list label{
            display: block;
            padding-left: 20px;
        }
        .modal-footer .form-group{
            margin-bottom: 15px;
        }
            ');
?>
<?php $this->beginBlock('renderJs'); ?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var cdnHost = '<?= Yii::$app->params['cdnHost'] ?>';
    var chargeLogPrintData = '<?= Url::to(['@apiChargeLogPrintData'])?>';
    require(["<?= $baseUrl ?>" + "/public/js/charge/print.js"], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock(); ?>