<?php
use yii\helpers\Url;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '今日病人', 'url' => Url::to(['@pharmacyIndexIndex'])],
        ['title' => '库存管理', 'url' => Url::to(['@pharmacyIndexStockInfo'])],
        ['title' => '入库管理', 'url' => Url::to(['@pharmacyIndexInboundIndex'])],
       ['title' => '出库管理', 'url' => Url::to(['@pharmacyIndexOutboundIndex'])],
    ],
];

echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]);

