<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\TagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '标签管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
CrudAsset::register($this);

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/spot/tag.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
   <div class="tag-index col-xs-12">
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    <div id="ajaxCrudDatatable" class = 'box'>
        <div class = 'row search-margin'>
            <div class = 'col-sm-2 col-md-2'>
                <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
                    <?= Html::a("<i class='fa fa-plus'></i>新建标签", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0,'role'=>'modal-remote','data-toggle'=>'tooltip']) ?>
                <?php endif?>
            </div>
            <div class = 'col-sm-10 col-md-10'>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <div class = 'row search-margin'>
            <div class = 'col-sm-12 col-md-12'>
                <div class="description-div">
                    <span>标签可用于：</span><br>
                    <span class="description-span"> ▪ 【会员卡中心】为一类标签配置一种折扣</span>
                </div>
            </div>
        </div>
            <?=GridView::widget([
                'id'=>'crud-datatable',
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive add-table-padding'],
                'tableOptions' => ['class' => 'table-border header'],
                'layout' => '{items}<div class="text-right">{summary}{pager}</div>',
                'summary' =>'<div class="table-summary">( {totalCount} 结果，共 {pageCount} 页 )</div>',
                'pager'=>[
                    'hideOnSinglePage' => false,//在只有一页时也显示分页
                    'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                    'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                    'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                    'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
                ],
                'columns' => [
                [
                    'attribute' => 'id',
                ],
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'type',
                    'value' => function ($model) {
                        return $model::$getType[$model->type];
                    },
                ],
                [
                    'attribute' => 'description',
                ],
                [
                    'class' => 'app\common\component\ActionTextColumn',
                    'template' => '{view}{delete}',
                    'buttons' => [
                        'delete' => function ($url, $dataProvider, $key) {
                            if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/delete', $this->params['permList'])) {
                                return false;
                            }
                            $options = [
                                'role' => "modal-remote",
                                'data-confirm-title' => "系统提示",
                                'data-original-title' => "删除",
                                'data-confirm-message' => '确定要删除该标签吗？同时将删除与标签相关的其他配置',
                                'data-pjax' => '1',
                                'data-request-method' => 'post',
                            ];
                            return Html::a('删除', Url::to(['@spotTagDelete', 'id' => $dataProvider['id']]), $options);
                        },
                            ],
                            'ajaxList' => [
                                'update' => true, //默认开启ajax的update,delete,关闭view
                                'delete' => true
                            ],
                        ],
                    ],
                'striped' => false,
                'condensed' => false, 
                'hover' => true,
                'bordered' => false,
                    
            ])?>
    </div>
<?php  Pjax::end()?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
