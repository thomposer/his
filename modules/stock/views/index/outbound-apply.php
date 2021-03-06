<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
use app\modules\stock\models\Stock;
use app\assets\AppAsset;
use app\modules\stock\models\OutboundInfo;
use app\modules\stock\models\Outbound;
use app\modules\spot\models\RecipeList;
use app\modules\stock\models\ConsumablesStock;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\pharmacy\models\Stock */
/* @var $form yii\widgets\ActiveForm */
$this->title = $model->status != 1 ? '审核出库' : '查看出库';
$this->params['breadcrumbs'][] = ['label' => '医疗耗材管理', 'url' => Url::to(['@stockIndexConsumablesOutboundIndex'])];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$id = Yii::$app->request->get('id');
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php  AppAsset::addCss($this, '@web/public/css/pharmacy/inbound.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>3]) ?>
    <div class="stock-form col-xs-10">
        <div class = "box">
            <div class="box-header with-border">
                <span class = 'left-title'><?= Html::encode($this->title) ?></span>
                <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['@stockIndexConsumablesOutboundIndex'],['class' => 'right-cancel']) ?>
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
                            <div class = 'col-xs-4 col-sm-4 col-md-4 inbound-remarks'>
                                备注：<?= Html::encode($model->remark) ?>
                            </div>
                        </div>
                        <?php if($model->status != 2): ?>
                            <div class = 'row'>
                                <div class = 'col-xs-4 col-sm-4 col-md-4'>
                                    审核时间：<?= date('Y-m-d H:i',$model->update_time) ?>
                                </div>
                                <div class = 'col-xs-4 col-sm-4 col-md-4'>
                                    审核人：<?= Html::encode($applyName) ?>
                                </div>
                                <div class = 'col-xs-4 col-sm-4 col-md-4'>
                                    审核结果：<?= ConsumablesStock::$getStatus[$model->status] ?>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                    <div class = 'box'>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'options' => ['class' => 'grid-view table-responsive'],
                            'tableOptions' => ['class' => 'table table-hover table-border stock-info'],
                            'headerRowOptions' => ['class' => 'header'],
                            'layout'=> '{items}',
                            'columns' => [
                                'product_number',
                                'name',
                                'specification',
                                'manufactor',
                                [
                                    'attribute' => 'default_price',
                                    'value' => function($dataProvider){
                                        return $dataProvider->default_price != null?'¥'.$dataProvider->default_price:'';
                                    }
                                ],
                                'expire_time:date',
                                'inbound_num',
                                'num',
                                'unit',
                            ],
                        ]); ?>
                    </div>
                    <?php if($model->status == 2): ?>
                        <div class="form-group">
                            <?= Html::a('审核通过',['@stockIndexConsumablesOutboundApply','id' => $id,'status' => 1],
                                [
                                    'class' => 'btn btn-default btn-form',
                                    'data-method' => false,
                                    'aria-label' => '审核通过',
                                    'data-confirm' => false,
                                    'data-request-method'=>'post',
                                    'role'=>'modal-remote',
                                    'data-toggle'=>'tooltip',
                                    'data-confirm-title'=>'系统提示',
                                    'data-confirm-message'=>Yii::t('yii', '是否确定审核通过?'),
                                    'data-delete' => true,
                            ]) ?>
                            <?= Html::a('审核不通过',['@stockIndexConsumablesOutboundApply','id' => $id,'status' => 3],
                                [
                                    'class' => 'btn btn-default btn-form',
                                    'data-method' => false,
                                    'aria-label' => '审核不通过',
                                    'data-confirm' => false,
                                    'data-request-method'=>'post',
                                    'role'=>'modal-remote',
                                    'data-toggle'=>'tooltip',
                                    'data-confirm-title'=>'系统提示',
                                    'data-confirm-message'=>Yii::t('yii', '是否确定审核不通过?'),
                                    
                            ]) ?>
                            <?= Html::a('取消',['consumables-outbound-index'],['class' => 'btn btn-cancel btn-form']) ?>
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