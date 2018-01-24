<?php 
use yii\bootstrap\Tabs;
use app\assets\AppAsset;
AppAsset::addCss($this, '@web/public/css/lib/tab.css');
AppAsset::addCss($this, '@web/public/css/medicine/form.css')
?>
<?=
    Tabs::widget([
        'renderTabContent' => false,
        'navType' => ' nav-tabs second-tabs',
        'items' => [
               [
                'label' => '美国儿童',
                'options' => ['id' => 'americanChildren']
               ]
        ]
    ]);
?>