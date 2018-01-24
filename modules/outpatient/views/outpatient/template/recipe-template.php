<?php

use yii\helpers\Html; 
use app\common\AutoLayout; 
use app\assets\AppAsset; 
use yii\widgets\Pjax; 
use johnitvn\ajaxcrud\CrudAsset;
use yii\grid\GridView; 
use app\modules\outpatient\models\RecipeTemplate;
use yii\helpers\Url;
CrudAsset::register($this); 
/* @var $this yii\web\View */ 
/* @var $searchModel app\modules\outpatient\models\search\RecipeTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */ 

$this->title = '处方模板'; 
$this->params['breadcrumbs'][] = ['label' => '医生门诊', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; 
$baseUrl = Yii::$app->request->baseUrl; 
?> 
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?> 
<?php $this->beginBlock('renderCss')?> 
<?php $this->endBlock()?> 
<?php $this->beginBlock('content');?> 
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>2]) ?>

<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?> 

<div class="recipe-template-index col-xs-10"> 
   <div class = "box"> 
       <div class = 'row search-margin'> 
         <div class = 'col-sm-2 col-md-2'> 
           <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/recipetemplate-create', $this->params['permList'])):?> 
           <?= Html::a("<i class='fa fa-plus'></i>新增", ['recipetemplate-create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?> 
           <?php endif?> 
        </div> 
        <div class = 'col-sm-10 col-md-10'> 
                        <?php //echo $this->render('_search', ['model' => $searchModel]); ?> 
                </div> 
      </div> 
    <?= GridView::widget([ 
        'dataProvider' => $dataProvider, 
        'options' => ['class' => 'grid-view table-responsive add-table-padding'], 
        'tableOptions' => ['class' => 'table table-hover table-border header'], 
        'layout'=> '{items}<div class="text-right">{pager}</div>', 
        'pager'=>[ 
            //'options'=>['class'=>'hidden']//关闭自带分页 
            'firstPageLabel'=> Yii::getAlias('@firstPageLabel'), 
            'prevPageLabel'=> Yii::getAlias('@prevPageLabel'), 
            'nextPageLabel'=> Yii::getAlias('@nextPageLabel'), 
            'lastPageLabel'=> Yii::getAlias('@lastPageLabel'), 
        ], 
        /*'filterModel' => $searchModel,*/
        'columns' => [
            'name',
            'typeTemplateName',
            [
                'attribute' => 'type',
                'value' => function($searchModel){
                    return RecipeTemplate::$getType[$searchModel->type];
                }
            ],
            'username',
            'create_time:datetime',

            [ 
                'class' => 'app\common\component\ActionColumn',
                'template' => '{recipetemplate-update}{recipetemplate-delete}',
                'buttons' => [
                    'recipetemplate-update' => function($url, $model, $key){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/recipetemplate-update', $this->params['permList'])) {
                            return false;
                        }
                        return Html::a('',$url, [ 'class' => 'icon_button_view fa fa-pencil-square-o', 'title' => '修改', 'data-toggle' => 'tooltip','data-pjax' => 0]);
                    },
                    'recipetemplate-delete' => function($url, $model, $key){
                        if (!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'] . '/recipetemplate-delete', $this->params['permList'])) {
                            return false;
                        }
                        $options = array_merge([
                            'data-confirm' => false,
                            'data-method' => false,
                            'data-request-method' => 'post',
                            'role' => 'modal-remote',
                            'data-toggle' => 'tooltip',
                            'data-confirm-title' => '系统提示',
                            'data-delete' => false,
                            'data-confirm-message' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-pjax' => '1',
                            'class' => 'icon_button_view fa fa-trash-o',
                            'title' => '删除',
                            'data-toggle' => 'tooltip'
                        ]);
                        return Html::a('',$url, $options);
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
A Product of Yii Software LLC