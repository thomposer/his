<?php

use app\assets\AppAsset;
use app\common\AutoLayout;
use app\modules\spot\models\Consumables;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePickerAsset;
use dosamigos\datepicker\DatePickerLanguageAsset;
CrudAsset ::register($this);
DatePickerAsset::register($this);
DatePickerLanguageAsset::register($this)->js[] = 'bootstrap-datepicker.'.Yii::$app->language.'.min.js';
$this -> title = '医疗耗材管理';
$this -> params['breadcrumbs'][] = $this -> title;
$params = [
    'searchName' => 'ConsumablesStockInfoSearch',
    'statusName' => 'status',
    'buttons' => [
        [
            'title' => '全部',
            'statusCode' => 0,
        ],
        [
            'title' => '有效预警',
            'statusCode' => 3,
        ],
        [
            'title' => '库存预警',
            'statusCode' => 1,
        ],

    ]
];
?>
<?php AutoLayout ::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this -> beginBlock('renderCss') ?>
<?php AppAsset ::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset ::addCss($this, '@web/public/css/material/stock-info.css') ?>
<?php $this -> endBlock() ?>
<?php $this -> beginBlock('content'); ?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>3]) ?>
<?php Pjax ::begin(['id' => 'crud-datatable-pjax']) ?>
<div class="consumables-stock-info-index col-xs-10">

    <div class="box delete_gap">
        <?= $this -> render('_topTab.php'); ?>
        <div class='row search-margin'>
            <div class='col-sm-6 col-md-6'>
                <?= $this -> render(Yii ::getAlias('@searchStatus'), $params) ?>
            </div>
            <div class='col-sm-6 col-md-6'>
                <?php echo $this -> render('_stockInfoSearch', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?= GridView ::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border header'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii ::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii ::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii ::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii ::getAlias('@lastPageLabel'),
            ],
            'pjax' => false,
            'striped' => false,
            'columns' => [
                [
                    'attribute' => 'product_number',
                    'group' => true
                ],
                [
                    'attribute' => 'consumablesName',
                    'group' => true,
                    'subGroupOf'=>0
                ],
                [
                    'attribute' => 'specification',
                    'group' => true,
                    'subGroupOf'=>0
                ],
                [
                    'attribute' => 'type',
                    'value' => function ($searchModel) {
                        return Consumables::$typeOption[ $searchModel["type"] ];
                    },
                    'group' => true,
                    'subGroupOf'=>0
                ],
                [
                    'attribute' => 'manufactor',
                    'value' => function($searchModel) {
                        return $searchModel["manufactor"] ?  $searchModel["manufactor"] : "--";
                    },
                    'group' => true,
                    'subGroupOf'=>0
                ],
                [
                    'attribute' => 'count',
                    'value' => function($searchModel) use($numArr) {
                        return $numArr[$searchModel['consumables_id']]['total'];
                    },
                    'group' => true,
                    'subGroupOf'=>0
                ],
                [
                    'attribute' => 'num',
                    'headerOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        if($model->num <= 10){
                            $html ='<span class = "red">'.$model->num.'</span>';
                            return $html;
                        }
                        return $model->num;
                    }
                ],
                'default_price',
                [
                    'attribute' => 'expire_time',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if($model->expire_time <= strtotime(date('Y-m-d'))+86400*180){
                            $html ='<span class = "red">'.date('Y-m-d',$model->expire_time).'</span>';
                            return $html;
                        }
                        return date('Y-m-d',$model->expire_time);
                    }
                ],
                'inboundTime:date'
            ],
        ]); ?>
    </div>
</div>
<?php Pjax ::end() ?>
<?php $this -> endBlock(); ?>
<?php $this -> beginBlock('renderJs'); ?>

<?php $this -> endBlock(); ?>
<?php AutoLayout ::end(); ?>
