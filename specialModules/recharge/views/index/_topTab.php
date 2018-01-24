<?php
use yii\helpers\Url;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '充值卡', 'url' => Url::to(['@rechargeIndexIndex']), 'icon_img' => $public_img_path . '/common/tab_charge.png'],
        ['title' => '套餐卡', 'url' => Url::to(['@rechargeIndexPackageCard']), 'icon_img' => $public_img_path . '/common/tab_package_card.png'],
        ['title' => '服务卡', 'url' => Url::to(['@cardIndexIndex']), 'icon_img' => $public_img_path . '/common/tab_service.png']
    ],
];

echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]);