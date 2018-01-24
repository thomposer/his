<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\wechat\models\ChargeRecord */
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>

<div class="charge-record-view">
    <!--<div class = "box">-->
    <div class = "box-body">  
        <div align="center" id="qrcode">
        </div>
    </div>
    <div style=" text-align: center; padding-bottom: 20px;">
        <?php // Html::button('关闭', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) ?>
    </div>
    <!--</div>-->
</div>
<?php
$js = <<<JS
   var codeUrl = "$code_url";
   require(["$baseUrl/public/js/paymentconfig/code.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
