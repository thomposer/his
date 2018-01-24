<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
use rkit\yii2\plugins\ajaxform\Asset;

CrudAsset::register($this);
Asset::register($this);

$this->title = '报表';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';

$tabArray = array();
$tabArray[] = ['title' => '费用统计', 'url' => Url::to(['/data/data/index', 'type' => 3]), 'type' => 3,'icon_img' => $public_img_path . 'charge/tab_cost.png'];
//$tabArray[] = ['title' => '医务统计', 'url' => Url::to(['/data/data/index', 'type' => 4]), 'type' => 4];
//$tabArray[] = ['title' => '进销存统计', 'url' => Url::to(['/data/data/index', 'type' => 5]), 'type' => 5];
$tabData = [
    'titleData' => $tabArray,
    'activeData' => [
        'type' => 3
    ]
];

$buttonArray = array();
$buttonArray[] = ['title' => '核心指标', 'statusCode' => 0, 'url' => Url::to(['/data/data/index'])];
$buttonArray[] = ['title' => '充值日报', 'statusCode' => 2, 'url' => Url::to(['/data/data/recharge'])];
//$buttonArray[] = ['title' => '收费明细', 'statusCode' => 1, 'url' => Url::to(['/data/data/index'])];
$params = [
    'searchName' => 'data',
    'statusName' => 'type',
    'buttons' => $buttonArray,
];

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/data/index.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

    <div class="report-forms-index col-xs-12">

        <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>

            <div class="report-forms-top recharge-content">

                <div class='row'>
                    <div class='col-sm-6 col-md-6 tf'>
                        <?= $this->render(Yii::getAlias('@searchStatusSkip'), $params) ?>
                    </div>
                    <div class='col-sm-6 col-md-6 tr'>
                        <?php /*echo
                            Html::tag('input', '', ['name' => "forms_type", 'value' => '1', 'type' => 'radio', 'checked' => 'true']) . '日报' .
                            Html::tag('input', '', ['name' => "forms_type", 'value' => '2', 'type' => 'radio']) . '月报'
                        */?>
                    </div>
                </div>

            </div>

            <?= $this->render('_chart') ?>
            <?php echo $this->render('_forms', ['dataProvider' => $dataProvider, 'model'=> $searchModel,'dateBegin'=>$dateBegin,'dateEnd'=>$dateEnd,]); ?>
        </div>
<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
