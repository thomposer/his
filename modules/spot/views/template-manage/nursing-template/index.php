<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\NursingRecordTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '护理模板';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>1]) ?>
<div class="nursing-record-template-index col-xs-10">
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

   <div class = "box">
       <div class = 'row search-margin'>
         <div class = 'col-sm-2 col-md-2'>
           <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/nursing-create', $this->params['permList'])):?>
           <?= Html::a("<i class='fa fa-plus'></i>新增", ['nursing-create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
           <?php endif?>
        </div>
        <div class = 'col-sm-10 col-md-10'>
                        <?php /*echo $this->render('_search', ['model' => $searchModel]); */?>
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
            // 'id',
            // 'spot_id',
            'nursing_item',
            'username',
            // 'content_template:ntext',
            'create_time:datetime',
            // 'update_time',

            [
                'class' => 'app\common\component\ActionTextColumn',
                'template' => '{nursing-update}{nursing-delete}',
                'buttons' => [
                    'nursing-update' => function ($url, $model, $key) {
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/nursing-update', $this->params['permList'])) {
                            return false;
                        }
                        return Html::a('修改', Url::to(['nursing-update', 'id' => $model->id]), ['title' => '修改','data-pjax' => 0]);
                    },
                    'nursing-delete' => function ($url, $model, $key) {
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/nursing-delete', $this->params['permList'])) {
                            return false;
                        }
                        $options = array_merge([
                            'data-confirm' => false,
                            'data-method' => false,
                            'data-request-method' => 'post',
                            'role' => 'modal-remote',
                            'data-confirm-title' => '系统提示',
                            'data-delete' => false,
                            'data-confirm-message' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-pjax' => '1',
                            'title' => '删除',
                        ]);
                        return Html::a('删除', Url::to(['nursing-delete', 'id' => $model->id]), $options);
                    },
                ]
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
