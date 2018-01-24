<?php

use yii\helpers\Url;

$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '微信支付', 'url' => Url::to(['@spot_setPaymentConfigUpdate', 'type' => 3]), 'type' => 3, 'icon_img' => $public_img_path . 'tab/tab_weixin.png'],
        ['title' => '支付宝支付', 'url' => Url::to(['@spot_setPaymentConfigPay', 'type' => 4]), 'type' => 4, 'icon_img' => $public_img_path . 'tab/tab_zhifubao.png'],
    ],
    'activeData' => [
        'type' => 3
    ]
];
?>
<?php $this->beginBlock('renderCss') ?>
<?php

$css = <<<CSS
    .box-header.with-border{
            border-bottom: none;
    }
    .btn-default[disabled]{
        background-color:#76a6ef;
        border-color: #76a6ef;
    }
    .btn-default[disabled]:active,.btn-default[disabled]:hover{
        background-color:#76a6ef;
    }
    .hid{
        display:none;
    }
CSS;
$this->registerCss($css);
?>
<?php $this->endBlock(); ?>

<?php

echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]);

