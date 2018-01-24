 <?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DatePickerAsset;
use dosamigos\datepicker\DatePickerLanguageAsset;
 use app\modules\patient\models\Patient;
 use johnitvn\ajaxcrud\CrudAsset;
DatePickerAsset::register($this);
DatePickerLanguageAsset::register($this)->js[] = 'bootstrap-datepicker.zh-CN.min.js';
/* @var $this yii\web\View */
/* @var $searchModel app\modules\charge\models\search\SearchCharge */
/* @var $dataProvider yii\data\ActiveDataProvider */
CrudAsset::register($this);
$this->title = '收费';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$tabData = [
    'titleData' => [
        ['title' => '待收费单', 'url' => Url::to(['@chargeIndexIndex', 'type' => 3]), 'type' => 3],
        ['title' => '已收费单', 'url' => Url::to(['@chargeIndexIndex', 'type' => 4]), 'type' => 4],
        ['title' => '已退费单', 'url' => Url::to(['@chargeIndexIndex', 'type' => 5]), 'type' => 5],
        ['title' => '交易流水', 'url' => Url::to(['@chargeIndexIndex', 'type' => 6]), 'type' => 6]
    ],
    'activeData' => [
        'type' => 3
    ]
];

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
 <?php AppAsset::addCss($this, '@web/public/css/charge/form.css') ?>
 <?php AppAsset::addCss($this, '@web/public/css/check/print.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<?php $params = Yii::$app->request->queryParams; ?>

<div class="charge-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
	<?php $type = isset($params['type']) ? $params['type'] : 3; ?>

    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
    <div class="box  delete_gap">
        <div class = 'row search-margin'>
          <div class = 'col-sm-12 col-md-12'>
           <?php  if((isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create-material', $this->params['permList'])) && $type == 3):?>
               <?= Html::a("<i class='fa fa-plus'></i>新增收费", ['create-material'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
           <?php endif?>
        </div>
       </div>
        <div class='row no-gap'>
            <!--            <div class='col-sm-2 col-md-2'>-->
            <!--            </div>-->
            <div class='col-sm-12 col-md-12'>
                <?php

                if ($params['type'] == 6) {
                    echo $this->render('_chargeLogSearch', ['model' => $searchRecordLogModel]);
                } else {
                    echo $this->render('_search', ['model' => $searchModel]);
                }
                ?>
            </div>
        </div>
        <?php
        
        if ($type == 3) { //待收费
            echo $this->render('_index_be_charge', ['dataProvider' => $dataProvider, 'baseUrl' => $baseUrl, 'searchModel' => $searchModel, 'cardInfo' => $cardInfo]);
        } else if ($type == 6) {
            echo $this->render('_chargeRecordAccount', ['dataProvider' => $dataProvider, 'baseUrl' => $baseUrl, 'searchModel' => $searchModel, 'cardInfo' => $cardInfo, 'type' => $type]);
        } else {//
            echo $this->render('_index_charge_record', ['dataProvider' => $dataProvider, 'baseUrl' => $baseUrl, 'searchModel' => $searchModel, 'cardInfo' => $cardInfo, 'type' => $type]);
        }
        ?>
    </div>

    <div id='print-show-none'>
        <div id='print-view'>

        </div>
    </div>

    <?php Pjax::end(); ?>
</div>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>
 <script type="text/javascript">
     var baseUrl = '<?= $baseUrl ?>';
     var chargeRecordLogUrl ='<?= Url::to(['@apiChargeChargeRecordLog'])?>';
     var cdnHost = '<?= Yii::$app->params['cdnHost'] ?>';
     var chargeLogPrintData = '<?= Url::to(['@apiChargeLogPrintData'])?>';
     var entrance = '1';
     require([baseUrl + '/public/js/charge/chargeRecordLog.js'], function (main) {
         main.init();
     })
    require([baseUrl + "/public/js/charge/print.js"], function (main) {
        main.init();
    });
 </script>
<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
