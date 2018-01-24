<?php
use yii\helpers\Url;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
         ['title' => '库存管理', 'url' => Url::to(['@materialIndexStockInfo'])],
         ['title' => '入库管理', 'url' => Url::to(['@materialIndexInboundIndex'])],
        ['title' => '出库管理', 'url' => Url::to(['@materialIndexOutboundIndex'])],
    ],
];

echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]);

