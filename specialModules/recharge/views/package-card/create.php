<?php

use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\card\models\UserCard */

$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/recharge/membershipCard.css');
AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css');
AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css');
$action = Yii::$app->controller->action->id;
?>

<div class="create-membership-package-card clearfix">

            <?=
            $this->render('_form', [
                'model' => $model,
                'cardList' => $cardList,
                'vidateTime' => $vidateTime
            ])
            ?>
</div>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var cardList = <?= json_encode($cardList,true) ?>;
    var getIphone = '<?= Url::to(['@apiPatientGetIphone']); ?>';
	var error = '<?= $model->errors?1:0 ?>';
	var outTradeNo = '<?= $outTradeNo ?>';
    require([baseUrl + "/public/js/recharge/create-membership-card.js"], function (main) {
        main.init();
    });
</script>