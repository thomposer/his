<?php

use yii\helpers\Html; 
use app\common\AutoLayout; 
use app\assets\AppAsset; 
use yii\widgets\Pjax;
use kartik\grid\GridView;
use dosamigos\datepicker\DatePickerAsset;
use dosamigos\datepicker\DatePickerLanguageAsset;
use app\modules\spot\models\RecipeList;
use kartik\grid\GridViewAsset;
use yii\helpers\Url;
GridViewAsset::register($this);
/* @var $this yii\web\View */ 
/* @var $searchModel app\modules\pharmacy\models\search\StockInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */ 
DatePickerAsset::register($this);
DatePickerLanguageAsset::register($this)->js[] = 'bootstrap-datepicker.'.Yii::$app->language.'.min.js';
$this->title = '处方管理'; 
$this->params['breadcrumbs'][] = $this->title; 
$baseUrl = Yii::$app->request->baseUrl;

$params = [
    'searchName' => 'ValidSearch',
    'statusName' => 'status',
    'buttons' => [
        [
            'title' => '全部',
            'statusCode' => 0,
        ],
        [
            'title' => '效期预警',
            'statusCode' => 3,
        ],
        [
            'title' => '库存预警',
            'statusCode' => 1,
        ],

    ]
];
?> 
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?> 
<?php $this->beginBlock('renderCss')?> 
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/pharmacy/pharmacy.css') ?>
<?php $this->endBlock()?> 
<?php $this->beginBlock('content');?> 
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>3]) ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?> 
<div class="stock-info-index col-xs-10">
   <?php echo $this->render('_top_tab.php'); ?> 
   <div class = "box delete_gap">
     <div class = 'row search-margin'>
         <div class = 'col-sm-6 col-md-4'>
             <?= $this->render(Yii::getAlias('@searchStatus'),$params) ?>
         </div>
          <div class = 'col-sm-6 col-md-8 '>
                <?php echo $this->render('_stockInfoSearch', ['model' => $searchModel]); ?>
          </div>
   </div>
    <?= GridView::widget([ 
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border'],
        'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
        'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
        'pager'=>[
            'hideOnSinglePage' => false,
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
        'pjax' => false,
        'striped' => false,
        'columns' => [
            [
                'attribute'=>'shelves',
                'headerOptions' => ['class' => 'col-sm-1'],
                'group' => true,
                'format'=>'raw',
                'value' => function($searchModel){
                    return $searchModel['shelves']?$searchModel['shelves']:'<span style="display:none">'.$searchModel->recipe_id.'</span>';
                    
                }
            ],
           [
               'attribute' => 'name',
               'headerOptions' => ['class' => 'col-sm-1'],
               'group' => true,
               'subGroupOf' => 0,
               'value' => function($searchModel) {
                    $text= Html::encode($searchModel['name']);
                    $text.='<span style="display:none">'.$searchModel->recipe_id.'</span>';
                    return $text;
                },
                'format'=>'raw',
           ],

            [
                'attribute' => 'specification',
                'group' => true,
                'subGroupOf' => 0,
                'value' => function($searchModel) {
                    $text= Html::encode($searchModel['specification']);
                    $text.='<span style="display:none">'.$searchModel->recipe_id.'</span>';
                    return $text;
                },
                'format'=>'raw',
            ],
            [
                'attribute' => 'manufactor',
                'value' => function($searchModel) {
                    $text= $searchModel['manufactor'] ? Html::encode($searchModel['manufactor']) : '--';
                    $text.='<span style="display:none">'.$searchModel->recipe_id.'</span>';
                    return $text;
                },
                'format'=>'raw',
                'group' => true,
                'subGroupOf' => 0
            ],
            [
                'attribute' => 'count',
                'value' => function($searchModel) use($numArr) {
                    $key = $searchModel['recipe_id'];
                    return $numArr[$key]['total'].RecipeList::$getUnit[$searchModel->unit];
                },
                'group' => true,
                'subGroupOf' => 0
            ],
            [
                'attribute' => 'num',
                'headerOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {

                    if($model->num <= 10){
                        $html ='<span class = "red">'.$model->num.RecipeList::$getUnit[$model->unit].'</span>';
                        return $html;
                    }else{
                        $html = '<span style="color:#445064 ;">'.$model->num.RecipeList::$getUnit[$model->unit].'</span>';
                        return $html;
                    }
                }
            ],
//             'price',
            'default_price',
            'batch_number',
                [
                'attribute' => 'expire_time',
                'format' => 'raw',
                'value' => function ($model) {

                    if($model->expire_time <= strtotime(date('Y-m-d'))+86400*180){
                        $html ='<span class = "red">'.date(('Y-m-d'),$model->expire_time).'</span>';
                        return $html;
                    }else{
                        $html = '<span style="color:#445064 ;">'.date(('Y-m-d'),$model->expire_time).'</span>';
                        return $html;
                    }
                }
            ],
            [
                'attribute' => 'inbound_time',
                'value' => function($model){
                    return date(('Y-m-d'),$model->inbound_time);
                }
            ]
        ],
    ]); ?> 
    </div> 
</div> 
<?php  Pjax::end()?>

<?php $this->endBlock();?> 
<?php $this->beginBlock('renderJs');?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var pharmacyIndexStockExportData = '<?= Url::to(['@pharmacyIndexStockExportData']) ?>';
    require(["<?= $baseUrl ?>" + "/public/js/stock/stockManage.js"], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock();?> 
<?php AutoLayout::end();?> 
