<?php

use app\modules\spot_set\models\Material;
use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
use yii\helpers\Url;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot_set\models\search\MaterialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '其他管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="material-index col-xs-10">
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

   <div class = "box">
       <div class = 'row search-margin'>
         <div class = 'col-sm-2 col-md-2'>
           <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/material-create', $this->params['permList'])):?>
           <?= Html::a("<i class='fa fa-plus'></i>新增", Url::to(['@spot_setChargeManageMaterialCreate']), ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
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
            'firstPageLabel'=> Yii::getAlias('@firstPageLabel'),
            'prevPageLabel'=> Yii::getAlias('@prevPageLabel'),
            'nextPageLabel'=> Yii::getAlias('@nextPageLabel'),
            'lastPageLabel'=> Yii::getAlias('@lastPageLabel'),
        ],
        /*'filterModel' => $searchModel,*/
        'columns' => [
            'product_number',
            'name',
            [
                'attribute' => 'type',
                'value' => function($searchModel){
                    return Material::$typeOption[$searchModel->type];
                }
            ],
             'specification',
             'unit',
            'price',
             'remark',
            [
                'attribute' => 'status',
                'value' => function($searchModel){
                    return Material::$getStatus[$searchModel->status];
                }
            ],
            [
                'class' => 'app\common\component\ActionTextColumn',
                'template' => '{material-view}{material-update}{material-delete}',
                'buttons' => [
                    'material-view' => function($url,$model,$key){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/material-view', $this->params['permList'])) {
                            return false;
                        }
                        $options = array_merge([
                            'data-pjax' => '0',
                            'class' => 'op-group-a'
                        ]);
                        return Html::a('查看', $url, $options);
                    },
                    'material-update' => function ($url, $model, $key) {
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/material-update', $this->params['permList'])) {
                            return false;
                        }
                        $options = array_merge([
                            'title' => Yii::t('yii', '修改'),
                            'aria-label' => Yii::t('yii', '修改'),
                            'data-pjax' => '0',
                        ]);
                        return Html::a('修改', $url, $options);
                    },
                    'material-delete' => function ($url, $model, $key) {
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/material-delete', $this->params['permList'])) {
                            return false;
                        }
                        $options = [
                            'role' => 'modal-remote',
                            'data-request-method' => 'post',
                            'data-confirm-title'=> '系统提示',
                            'data-confirm-message'=> Yii::t('yii', 'Are you sure you want to delete this item?')
                        ];
                        return Html::a('删除', $url, $options);

                    }
                ],
            ],
        ],
    ]); ?>
    </div>
    <?php  Pjax::end()?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
