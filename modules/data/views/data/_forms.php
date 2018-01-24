<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;

$baseUrl = Yii::$app->request->baseUrl;

$gridColumns = [
//    ['class'=>'kartik\grid\SerialColumn'],
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
        'label' => '预约人数(人)',
        'headerOptions' => ['class' => 'people-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['reservationNumber']?$dataProvider['reservationNumber']:0;
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '接诊人数(人)',
        'headerOptions' => ['class' => 'people-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['actualNumber']?$dataProvider['actualNumber']:0;
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '付款人数(人)',
        'headerOptions' => ['class' => 'people-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['paymentNumber']?$dataProvider['paymentNumber']:0;
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '诊金(元)',
        'headerOptions' => ['class' => 'charge-type-detail report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['fee']?$dataProvider['fee']:'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '实验室检查费用(元)',
        'headerOptions' => ['class' => 'charge-type-detail report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['labFee']?$dataProvider['labFee']:'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '影像学检查费用(元)',
        'headerOptions' => ['class' => 'charge-type-detail report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['iconographyFee']?$dataProvider['iconographyFee']:'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '治疗费用(元)',
        'headerOptions' => ['class' => 'charge-type-detail report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['cureFee']?$dataProvider['cureFee']:'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '处方费用(元)',
        'headerOptions' => ['class' => 'charge-type-detail report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['recipeFee']?$dataProvider['recipeFee']:'0.00';
        },
    ],

//    [
//        'pageSummary'=>true,
//        'label' => '就诊应收金额',
//        'value' => function ($dataProvider) {
//            return $dataProvider['receivablePrice']?$dataProvider['receivablePrice']:'0.00';
//        },
//    ],
//
//    [
//        'pageSummary'=>true,
//        'label' => '就诊优惠金额',
//        'value' => function ($dataProvider) {
//            return $dataProvider['favourablePrice']?$dataProvider['favourablePrice']:'0.00';
//        },
//    ],
//
//    [
//        'pageSummary'=>true,
//        'label' => '就诊退费金额',
//        'value' => function ($dataProvider) {
//            return $dataProvider['returnPrice']?$dataProvider['returnPrice']:'0.00';
//        },
//    ],

    [
        'pageSummary'=>true,
        'label' => '就诊实收金额(元)',
        'headerOptions' => ['class' => 'charge-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['actualPrice']?$dataProvider['actualPrice']:'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '客单价(元)',
        'headerOptions' => ['class' => 'charge-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['perPrice']?$dataProvider['perPrice']:'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '会员卡销量(张)',
        'headerOptions' => ['class' => 'membercard-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['vipCardSum']?$dataProvider['vipCardSum']:'0';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '会员卡充值金额(元)',
        'headerOptions' => ['class' => 'membercard-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['vipCardPrice']?$dataProvider['vipCardPrice']:'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '会员卡赠送金额(元)',
        'headerOptions' => ['class' => 'membercard-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['vipCardPriceGive']?$dataProvider['vipCardPriceGive']:'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '消费占比(%)',
        'headerOptions' => ['class' => 'kpi-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['consumptionProportion']?$dataProvider['consumptionProportion']:'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '市场效率(%)',
        'headerOptions' => ['class' => 'kpi-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['marketEfficiency']?$dataProvider['marketEfficiency']:'0.00';
        },
    ],

    [
        'pageSummary'=>true,
        'label' => '销售效率(%)',
        'headerOptions' => ['class' => 'kpi-count report-text-for-right'],
        'contentOptions'=>['class'=>'report-text-for-right'],
        'value' => function ($dataProvider) {
            return $dataProvider['marketingEfficiency']?$dataProvider['marketingEfficiency']:'0.00';
        },
    ],


];
?>

<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div class="box">
        <div class='row search-margin'>
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
            require([ baseUrl + "/public/js/data/report_forms.js"], function (main) {
                    main.init();
                });
JS;
$this->registerJs($js);
?>
<?php Pjax::end() ?>