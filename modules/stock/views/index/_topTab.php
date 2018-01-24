<?php
use yii\helpers\Url;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
         ['title' => '库存管理', 'url' => Url::to(['@stockIndexConsumablesStockInfo'])],
         ['title' => '入库管理', 'url' => Url::to(['@stockIndexConsumablesInboundIndex'])],
         ['title' => '出库管理', 'url' => Url::to(['@stockIndexConsumablesOutboundIndex'])],
    ],
];

echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]);

