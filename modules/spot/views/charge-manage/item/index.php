<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\spot\models\InspectItem;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\InspectItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '检验项目';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="inspect-item-index col-xs-10">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax'])?>
    
    <div class = "box">
        <div class = 'row search-margin'>
            <div class = 'col-sm-2 col-md-2'>
                <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/item-create', $this->params['permList'])): ?>
                    <?= Html::a("<i class='fa fa-plus'></i>新增", Url::to(['@spotChargeManageItemCreate']), ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
                <?php endif ?>
            </div>
            <div class = 'col-sm-10 col-md-10'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <?=GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive add-table-padding'],
            'tableOptions' => ['class' => 'table table-hover table-border header'],
            'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
            'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
            'pager' => [
                //'options'=>['class'=>'hidden']//关闭自带分页
                'hideOnSinglePage' => false,//在只有一页时也显示分页
                'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
            ],
            /* 'filterModel' => $searchModel, */
            'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

                'id',
//            'spot_id',
                [
                    'attribute' => 'item_name',
                ],
                [
                    'attribute' => 'english_name',
                ],
                [
                    'attribute' => 'unit',
                ],
                [
                    'attribute' => 'status',
                    'value' => function($searchModel) {
                        return InspectItem::$getStatus[$searchModel->status];
                    },
                ],
                // 'status',
               [
                    'class' => 'app\common\component\ActionTextColumn',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2 '],
                    'deleteOptions'=>['title'=>'停用','data-confirm-message'=>'您确定要停用此项吗？'],
                    'template' => '{item-view}{item-update}{item-delete}',
                    'buttons' => [
                        'item-view'=>  function ($url,$model){
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/item-view', $this->params['permList'])) {
                                return false;
                            }
                            return Html::a('查看', $url,['class'=>'op-group-a']);
                        },
                        'item-update'=>  function ($url,$model){
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/item-update', $this->params['permList'])) {
                                return false;
                            }
                            return Html::a('修改', $url,['class'=>'op-group-a']);
                        },
                        'item-delete' => function ($url, $model, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/item-delete', $this->params['permList'])) {
                                return false;
                            }
                            $options = [
                                'data-method' => false,
                                'data-request-method' => 'post',
                                'role' => 'modal-remote',
                                'data-confirm-title' => '系统提示',
                                'data-delete' => $model->status==1?false:true,
                                'data-confirm-message' => '您确定要'.($model->status==1?'停用':'启用').'此项吗？',
                                'class'=>'op-group-a'
                            ];
                            return Html::a($model->status==1?'停用':'启用', Url::to(['item-delete', 'id' => $model->id,'status'=>$model->status]), $options);
                        }
                    ]
                ],
            ],
        ]);
        ?>
    </div>
    <?php Pjax::end();?>
</div>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>

<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
