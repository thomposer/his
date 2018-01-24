 <?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use app\common\Common;

$baseUrl = Yii::$app->request->baseUrl;

$gridColumns = [
    [
        'pageSummary'=>'合计',
        'label' => '日期',
        'headerOptions' => ['class' => 'date-count report-text-for-center'],
        'contentOptions'=>['class'=>'report-text-for-center'],
        'value' => function ($dataProvider) {
            if(is_numeric(strtotime($dataProvider['date']))){
                return date('Y-m-d', strtotime($dataProvider['date']));
            }else {
                return $dataProvider['date'];
            }

        },
    ],
    [
        'pageSummary'=>true,
        'label' => '充值总金额(元)',
        'headerOptions' => ['class' => 'recharge-total report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'format' => ['decimal',2],
        'value' => function ($dataProvider) {
            return $dataProvider['vipCardPrice']?Common::num($dataProvider['vipCardPrice']):'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '赠送总金额(元)',
        'headerOptions' => ['class' => 'recharge-total report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'format' => ['decimal',2],
        'value' => function ($dataProvider) {
            return $dataProvider['vipCardPriceGive']?Common::num($dataProvider['vipCardPriceGive']):'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '提现总金额(元)',
        'headerOptions' => ['class' => 'recharge-total report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'format' => ['decimal',2],
        'value' => function ($dataProvider) {
            return $dataProvider['vipCardPriceCash']?Common::num($dataProvider['vipCardPriceCash']):'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '净实收金额(元)',
        'headerOptions' => ['class' => 'recharge-total report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'format' => ['decimal',2],
        'value' => function ($dataProvider) {
            return Common::num($dataProvider['vipCardPrice'] - $dataProvider['vipCardPriceCash']);
        },
    ],
    [
        'pageSummary'=>true,
        'label' => '门诊量',
        'headerOptions' => ['class' => 'membercard-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['reportNum']?$dataProvider['reportNum']:0;
        },
    ],
    [
        'pageSummary'=>true,
        'label' => '非会员门诊量',
        'headerOptions' => ['class' => 'membercard-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'pageSummaryOptions' => ['id' =>'not-vip-num'] ,
        'value' => function ($dataProvider) {
            //FIXME 要修改成发布当天
            if($dataProvider['date'] < "2017-04-30"){
                return "--";
            }
            return $dataProvider['reportNum']-$dataProvider['reportVipNum'];
        },
    ],
    [
        'pageSummary'=>true,
        'label' => '开卡人次',
        'headerOptions' => ['class' => 'membercard-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'pageSummaryOptions' => ['id' =>'vip-card-new'] ,
        'value' => function ($dataProvider) {
            return $dataProvider['getVipCardNew'];
        },
    ],
    [
        'pageSummary'=>true,
        'label' => '开卡转化率(%)',
        'headerOptions' => ['class' => 'membercard-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'pageSummaryOptions' => ['id' =>'recharge-summary-percent'] ,
        'value' => function ($dataProvider) {
            // '非会员门诊量'
            $notVipNum = $dataProvider['reportNum']-$dataProvider['reportVipNum'];
            //发布当日前的历史数据，“非会员门诊量”和“开卡转化率”两个数值置空，用“--”表示
            //FIXME 要修改成发布当天
            if($notVipNum == 0 || $dataProvider['date'] < "2017-04-30") {
                return "--";
            }else{
                return Common::num(($dataProvider['getVipCardNew']/$notVipNum)*100)."%";
            }

        },
    ],



];
?>

<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class="box">
        <div class='row search-margin recharge-content'>
            <div class='col-sm-12 col-md-12'>
                <?php echo $this->render('_search',['model' => $model, 'dateBegin'=>$dateBegin, 'dateEnd'=>$dateEnd,]); ?>
            </div>
        </div>
        <?=
        GridView::widget([
            'showPageSummary'=>true,
            'dataProvider' => $dataProvider,
            'striped' => false,
            'options' => ['class' => 'grid-view table-responsive add-table-padding '],
            'tableOptions' => ['class' => 'table table-hover table-border header','id'=>'data-report-table-width'],
            'layout' => '{items}<div class="text-right">{pager}</div>',
            'pager' => [
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            'columns' => $gridColumns
        ]); ?>
    </div>
<?php
$js = <<<JS
            require([ baseUrl + "/public/js/data/recharge_forms.js"], function (main) {
                    main.init();
                });
JS;
$this->registerJs($js);
?>
<?php Pjax::end() ?>