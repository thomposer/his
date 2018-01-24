<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\spot\models\Spot;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\SpotSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '诊所管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="spot-index col-xs-12">
    <?php Pjax::begin(['id' => 'crud-datatable-pjax'])?>
   <div class = "box">
   <div class = 'row search-margin'>
      <div class = 'col-sm-2 col-md-2'>
       <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
       <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
       <?php endif?>
    </div>
    <div class = 'col-sm-10 col-md-10'>
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
   </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border header'],
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
        /*'filterModel' => $searchModel,*/
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
            ],
            [
                'attribute' => 'spot_name',
            ],
            [
                'attribute' => 'telephone',
            ],
            [
                'attribute' => 'fax_number',
            ],
            [
                'attribute' => 'contact_name',
            ],
            [
                'attribute' => 'contact_iphone',
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($searchModel){
                    if($searchModel->status == 3){
                        return Html::tag('span','已删除',['class' => 'red']);
                    }
                   return Spot::$getStatus[$searchModel->status];
                }
            ],
            [
                'class' => 'app\common\component\ActionTextColumn',
                'template' => '{view}{update}{delete}',
                'headerOptions' => ['class' => 'col-sm-2 col-md-2'],
                
                'buttons' => [
                    'view' => function ($url,$model,$key){
                        if($model->status == 3 || (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/view', $this->params['permList']))){
                            return false;
                        }
                        $options = array_merge([
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ]);
                        /*fa-eye是查看*/
                        return Html::a('查看', $url, $options);
                    },
                    'update' => function ($url,$model,$key){
                        if($model->status == 3 || (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/update', $this->params['permList']))){
                            return false;
                        }
                        $options = array_merge([
                            'title' => '修改',
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                        ]);
                        return Html::a('修改', $url, $options);
                    },
                    'delete' => function ($url,$model,$key){
                        if($model->status == 3 || (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/delete', $this->params['permList']))){
                            return false;
                        }
                        $options = array_merge([
                           'title' => '删除',
                           'data-confirm'=>false, 
                           'data-method'=>false,
                           'data-request-method'=>'post',
                           'role'=>'modal-remote',
                           'data-confirm-title'=>'系统提示',
                           'data-delete' => false,
                           'data-confirm-message'=>Yii::t('yii', 'Are you sure you want to delete this item?'),
                           'data-pjax' => '1',
                        ]);
                        return Html::a('删除', $url, $options);
                    },

                ]
                
            ],
        ],
    ]); ?>
    </div>
    <?php Pjax::end();?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
