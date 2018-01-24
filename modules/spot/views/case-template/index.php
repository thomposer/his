<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\modules\spot\models\CaseTemplate;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\spot\models\search\CaseTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '病例模板';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="case-template-index col-xs-12">
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
    
   <div class = "box">
   <div class = 'row search-margin'>
       <div class='col-sm-2 col-md-2'>
           <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/create', $this->params['permList'])): ?>
               <?= Html::a("<i class='fa fa-plus'></i>新增", ['create'], ['class' => 'btn btn-default font-body2', 'data-pjax' => 0]) ?>
           <?php endif ?>
       </div>
   </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border header'],
        'layout'=> '{items}<div class="text-right">{pager}</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
        /*'filterModel' => $searchModel,*/
        'columns' => [
            [
                'attribute' => 'name',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2']
            ],
            [
                'attribute' => 'type',
                'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                'value' => function ($searchModel){
                    return CaseTemplate::$getType[$searchModel->type];
                }
            ],

            'create_time:datetime',
            [
                'attribute' => 'user_name',
                'value'=>function($model){
                    return $model->user_name;
                },
            ],
            [
                'class' => 'app\common\component\ActionColumn',
                'template' => '{update}{delete}',
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
