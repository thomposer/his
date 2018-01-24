<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\Charge */
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
use app\modules\charge\models\ChargeInfo;
use app\common\Common;
use rkit\yii2\plugins\ajaxform\Asset;

$this->title = '交易流水明细';
$this->params['breadcrumbs'][] = ['label' => '收费', 'url' => ["index", "type" => 6]];
$this->params['breadcrumbs'][] = $chargeRecordLogList['type'] == 1 ? '收费明细' : '退费明细';
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
CrudAsset::register($this);
Asset::register($this);
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/charge/form.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/check/print.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
    <div class="charge-create col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <span class='left-title'><?= Html::encode($chargeRecordLogList['type'] == 1 ? '收费明细' : '退费明细') ?></span>
                <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(["index", "type" => 6]), ['class' => 'right-cancel']) ?>
            </div>
            <div class="box-body charge-body">
                <?=
                $this->render('_logForm', [
                    'dataProvider' => $dataProvider,
                    'chargeRecordLogList' => $chargeRecordLogList
                ])
                ?>
            </div>
        </div>
        <div class = 'save-charge'>
            <?php
            $button = $chargeRecordLogList['type'] == 1?'打印收费单':'打印退费单';
            $options = [
                        'style' => 'display: inline-block;',
                        'class' => 'btn btn-default',
                        'data-pjax' => '0',
                        'role' => 'modal-remote',
                        'data-modal-size' => 'middle',
                    ];
            echo Html::a($button, ['@apiChargePrintList', 'id' => Yii::$app->request->get('id')], $options);
//            echo Html::button($button, ['class' => 'btn btn-default print_label' ,'id'=>Yii::$app->request->get('id'),'name'=>Yii::$app->request->get('id').'charge_show' ])
            ?>
        </div>

        <div id='print-show-none'>
            <div id='print-view'>

            </div>
        </div>
    </div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
    <script type="text/javascript">
        var baseUrl = "<?= $baseUrl ?>";
        var chargeId = '<?= $chargeId ?>';
        var discountType = '<?= $discountType ?>';
        var payType = '<?= $payType ?>';
        var income = '<?= $income ?>';
        var flowCount = '<?= $flowCount ?>';
        var entrance = '2';
        var chargeRecordLogUrl ='<?= Url::to(['@apiChargeChargeRecordLog'])?>';
        var cdnHost = '<?= Yii::$app->params['cdnHost'] ?>';
        var chargeLogPrintData = '<?= Url::to(['@apiChargeLogPrintData'])?>';
        require(["<?= $baseUrl ?>"+"/public/js/lib/jquery-migrate-1.1.0.js"],function(){
        });

        require(["<?= $baseUrl ?>"+"/public/js/charge/update.js?v="+ '<?= $versionNumber ?>'],function(main){
            main.init();
        });

        require(["<?= $baseUrl ?>"+"/public/js/charge/chargeRecordLog.js"],function(main){
            main.init();
        });
        require([baseUrl + "/public/js/charge/print.js"], function (main) {
            main.init();
        });
    </script>
<?php $this->endBlock() ?>
<?php
AutoLayout::end() ?>