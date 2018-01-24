<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
use app\modules\stock\models\Stock;
use app\assets\AppAsset;
use app\modules\stock\models\StockInfo;
use app\modules\spot\models\RecipeList;
/* @var $this yii\web\View */
/* @var $model app\modules\pharmacy\models\Stock */
/* @var $form yii\widgets\ActiveForm */
$this->title = $model->status != 1 ? '审核入库' : '查看入库';
$this->params['breadcrumbs'][] = ['label' => '处方管理', 'url' => Yii::$app->request->referrer];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/pharmacy/inbound.css')?>
<?php AppAsset::addCss($this, '@web/public/css/pharmacy/pharmacy.css')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>3]) ?>
<div class="stock-form col-xs-10">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>      
    </div>
    <div class = "box-body"> 
    <div class="stock-apply col-md-12">
    <div class = 'margin-bottom margin-top'>
        <div class = 'row'>
            <div class = 'col-xs-4 col-md-4'>
                入库单号：<?= $model->id; ?>
            </div>
            <div class = 'col-xs-4 col-sm-4 col-md-4'>
                入库日期：<?= date('Y-m-d',$model->inbound_time) ?>
            </div>
            <div class = 'col-xs-4 col-sm-4 col-md-4'>
                入库方式：<?= Stock::$getInboundType[$model->inbound_type] ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-xs-4 col-sm-4 col-md-4'>
                供应商：<?= Html::encode($model->name) ?>
            </div>
            <div class = 'col-xs-4 col-sm-4 col-md-4'>
                制单人：<?= Html::encode($model->username) ?>
            </div>
            <div class = 'col-xs-4 col-sm-4 col-md-4 inbound-remarks'>
                备注：<?= Html::encode($model->remark) ?>
            </div>
        </div>
    </div>
    <div class = 'box'>
        <?= GridView::widget([ 
            'dataProvider' => $dataProvider, 
            'options' => ['class' => 'grid-view table-responsive'], 
            'tableOptions' => ['class' => 'table table-hover table-border stock-info'], 
            'headerRowOptions' => ['class' => 'header'],
            'layout'=> '{items}', 
            'columns' => [
                'name',
                'specification',
                'manufactor',
                [
                  'attribute'=>'total_num',
                    'contentOptions' => ['class' => 'total_num-focusout']
                ],
                [
                    'attribute' => 'unit',
                    'value' => function($dataProvider){
                        return RecipeList::$getUnit[$dataProvider->unit];
                    }
                ],
                'invoice_number',
//                 [
//                     'attribute' => 'price',
//                     'value' => function($dataProvider){
//                         return $dataProvider->price?'¥'.$dataProvider->price:'';
//                     }
//                 ],
                [
                    'attribute' => 'default_price',
                    'contentOptions' => ['class' => 'default_price-focusout'],
                    'value' => function($dataProvider){
                        return $dataProvider->default_price?'¥'.$dataProvider->default_price:'';
                    }
                ],
               'batch_number',
               'expire_time:date',
            ], 
        ]); ?>
        <div class="stock-total">
            <span class="stock-total-pre">成本合计： <span class="stock-total-pre-num"></span></span>
        </div>
    </div>
    <?php if($model->status != 1): ?>
    <div class="form-group">
        <?= Html::a('审核通过','',['class' => 'btn btn-default btn-form', 'data-method' => 'post','aria-label' => '审核','data-confirm' => '是否确定审核?','data-delete' => true]) ?>
        <?= Html::a('取消',Yii::$app->request->referrer,['class' => 'btn btn-cancel btn-form']) ?>
    </div>
    <?php endif;?>
</div>
</div>
</div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var  view=1;//查看入口
    require(["<?= $baseUrl ?>"+"/public/js/pharmacy/total-price.js?v="+'<?= $versionNumber ?>'],function(main){
    main.init();
    });
    </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>