<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
use app\modules\stock\models\Stock;
use app\assets\AppAsset;
use app\modules\stock\models\OutboundInfo;
use app\modules\stock\models\Outbound;
use app\modules\spot\models\RecipeList;
/* @var $this yii\web\View */
/* @var $model app\modules\pharmacy\models\Stock */
/* @var $form yii\widgets\ActiveForm */
$this->title = $model->status != 1 ? '审核出库' : '查看出库';
$this->params['breadcrumbs'][] = ['label' => '处方管理', 'url' => Yii::$app->request->referrer];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/pharmacy/inbound.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>3]) ?>
<div class="stock-form col-xs-10">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['@pharmacyIndexOutboundIndex'],['class' => 'right-cancel']) ?>      
    </div>
    <div class = "box-body">
    <div class="stock-apply col-md-12">
    <div class = 'margin-bottom margin-top'>
        <div class = 'row'>
            <div class = 'col-xs-4 col-md-4'>
                出库单号：<?= $model->id; ?>
            </div>
            <div class = 'col-xs-4 col-sm-4 col-md-4'>
                出库日期：<?= date('Y-m-d',$model->outbound_time) ?>
            </div>
            <div class = 'col-xs-4 col-sm-4 col-md-4'>
                出库方式：<?= Outbound::$getOutboundType[$model->outbound_type] ?>
            </div>
        </div>
        <div class = 'row'>    
            <div class = 'col-xs-4 col-sm-4 col-md-4'>
                领用科室：<?= Html::encode($model->department_name) ?>
            </div>
            <div class = 'col-xs-4 col-sm-4 col-md-4'>
                领用人员：<?= Html::encode($model->username) ?>
            </div>
            <div class = 'col-xs-4 col-sm-4 col-md-4'>
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
//                 [
//                     'attribute' => 'price',
//                     'value' => function($dataProvider){
//                         return '¥'.$dataProvider->price;
//                     }
//                 ],
                [
                    'attribute' => 'default_price',
                    'value' => function($dataProvider){
                        return $dataProvider->default_price != null?'¥'.$dataProvider->default_price:'';
                    }
                ],
               'batch_number',
               'expire_time:date',
               'inbound_num',
               'num',
               [
                   'attribute' => 'unit',
                   'value' => function($dataProvider){
                       return RecipeList::$getUnit[$dataProvider->unit];
                   }
               ],
            ], 
        ]); ?> 
    </div>
    <?php if($model->status != 1): ?>
    <div class="form-group">
        <?= Html::a('审核通过','',['class' => 'btn btn-default btn-form', 'data-method' => 'post','aria-label' => '审核','data-confirm' => '是否确定审核?','data-delete' => true]) ?>
        <?= Html::a('取消',['@pharmacyIndexOutboundIndex'],['class' => 'btn btn-cancel btn-form']) ?>
    </div>
    <?php endif;?>
</div>
</div>
</div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>