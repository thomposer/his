<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\web\View;
use app\modules\user\models\User;
use app\modules\stock\models\ConsumablesOutbound;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\stock\models\ConsumablesStock;
use kartik\grid\GridView;
use kartik\grid\GridGroupAsset;
CrudAsset::register($this);
GridGroupAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\pharmacy\models\search\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '医疗耗材管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$searchParams = [
    'searchName' => 'ValidSearch',
    'statusName' => 'status',
    'buttons' => [
        [
            'title' => '按出库单号',
            'statusCode' => 0,
        ],
        [
            'title' => '按出库耗材',
            'statusCode' => 2,
        ],

    ]
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php AppAsset ::addCss($this, '@web/public/css/material/stock-info.css') ?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>3]) ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<div class="stock-index col-xs-10">
    <?= $this->render('_topTab.php'); ?>
    <div class = "box delete_gap">
        <div class = 'row search-margin'>
            <div class = 'col-sm-4 col-md-4'>
                <?php  if(isset($this->params['permList']['role'])||in_array(Yii::getAlias('@stockIndexConsumablesOutboundCreate'), $this->params['permList'])):?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", ['consumables-outbound-create'], ['class' => 'btn btn-default font-body2 margin-r-15','data-pjax' => 0]) ?>
                <?php endif?>
                <?= $this->render(Yii::getAlias('@searchStatusCommon'), $searchParams) ?>
            </div>
            <div class = 'col-sm-8 col-md-8'> 
                <?= $this->render('_outboundSearch', ['model' => $searchModel, 'status' => 1]); ?> 
            </div> 
        </div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'pager'=>[
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            'pjax' => false,
            'striped' => false,
            'bordered' => false,
            'columns' => [
                'id',
                'outbound_time:date',
                [
                    'attribute' => 'outbound_type',
                    'value' => function($searchModel){
                        return ConsumablesOutbound::$getOutboundType[$searchModel->outbound_type];
                    }
                ],
                'department_name',
                [
                    'attribute' => 'leading_user_id',
                    'value' => function ($searchModel){
                        if(!$searchModel->leading_user_id){
                            return '';
                        }
                        return User::find()->select(['username'])->where(['id' => $searchModel->leading_user_id])->asArray()->one()['username'];
                    }
                ],
                'username',
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function ($searchModel){
                            return Html::tag('span',ConsumablesStock::$getStatus[$searchModel->status]);
                    }
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'template' => '{consumables-outbound-update}{consumables-outbound-delete}{consumables-outbound-apply}{consumables-outbound-view}',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'buttons' => [
                        'consumables-outbound-update' => function($url,$model,$key){
                            if((!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@materialIndexOutboundUpdate'), $this->params['permList'])) || $model->status !=2){
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', 'Update'),
                                'aria-label' => Yii::t('yii', 'Update'),
                                'data-pjax' => '0',
                                'class'=>'op-group-a'
                            ]);
                            return Html::a('编辑', $url, $options);
                        },
                        'consumables-outbound-delete' => function($url,$model,$key){
                            if((!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@materialIndexOutboundDelete'), $this->params['permList'])) || $model->status != 2){
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', 'Delete'),
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-confirm'=>false,
                                'data-method'=>false,
                                'data-request-method'=>'post',
                                'role'=>'modal-remote',
                                'data-confirm-title'=>'系统提示',
//                            'data-delete' => true,
                                'data-confirm-message'=>Yii::t('yii', '你确定删除吗?'),
                                'class'=>'op-group-a'
                            ]);
                            return Html::a('删除', $url, $options);
                        },
                        'consumables-outbound-apply' => function($url,$model,$key){
                            if((!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@materialIndexOutboundApply'), $this->params['permList'])) || $model->status != 2){
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', '审核'),
                                'aria-label' => Yii::t('yii', '审核'),
                                'data-pjax' => '0',
                                'class'=>'op-group-a'
                            ]);
                            return Html::a('审核', $url, $options);
                        },
                        'consumables-outbound-view' => function($url,$model,$key){
                            if((!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@materialIndexOutboundView'), $this->params['permList'])) || $model->status == 2){
                                return false;
                            }
                            $options = array_merge([
                                'title' => Yii::t('yii', 'View'),
                                'aria-label' => Yii::t('yii', 'View'),
                                'data-pjax' => '0',
                                'title'=>'查看'  ,
                                'class'=>'op-group-a'
                            ]);
                            /*查看按钮*/
                            return Html::a('查看', $url, $options);
                        }

                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
<?php  Pjax::end()?>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
