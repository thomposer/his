<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
use yii\helpers\Url;
use app\modules\module\models\Menu;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\module\models\search\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '模块菜单';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this,'@web/public/css/lib/search.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="menu-index col-xs-12">
   <?php Pjax::begin(['id' => 'crud-datatable-pjax'])?>    
    <div class = "box">
     <div class = 'row search-margin'>
        <div class = 'col-sm-3 col-md-3'>
            <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
            <?php echo  Html::a('<i class="fa fa-plus"></i>新增', ['@moduleMenuCreate'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
            <?php endif;?>
            <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
            <?php echo Html::a('清除缓存',['@moduleMenuFlush'],[
                'class' => 'btn btn-delete font-body2',
                'data' => [
                    'confirm' => '你确定要清除缓存吗?',
                    'method' => 'post',
                ],
                
            ])?>
            <?php endif;?>
        </div>
        <div class = 'col-sm-9 col-md-9'>
         <?php  echo $this->render('_search', ['model' => $searchModel,'titleList' => $titleList]); ?>
        </div>
    </div>    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive add-table-padding'],
        'tableOptions' => ['class' => 'table table-hover table-border header'],
        'sorter' => ['attributes' => ['id','type']],
        'layout'=> '{items}<div class="text-right">{pager}</div>',
        'pager'=>[
            
            'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
            'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
            'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
            'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
        ],
        'columns' => [    
            'id',
            [
                'attribute' => 'description',
                'headerOptions' => ['class' => 'col-sm-2 col-md-2']
            ],
            [
                'attribute' => 'menu_url',
                'headerOptions' => ['class' => 'col-sm-3 col-md-3']
            ],
            [
            'attribute' => 'parent_id',
            'value' => function ($searchModel){
                return $searchModel->module_description;
            }
            ],
            [
                'attribute' => 'type',
                'format' => 'raw',
                'value' => function($searchModel){
                        
                        return "<span class='".Menu::$color[$searchModel->type]."'>".Menu::$left_menu[$searchModel->type]."</span>";
                }
            ],          
           
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($searchModel){
                    
                    return "<span class='".Menu::$color[$searchModel->status]."'>".Menu::$menu_status[$searchModel->status]."</span>";
                }
            ],
            [
                'class' => 'app\common\component\ActionColumn',
            ],
    ]
    ]); ?>
    </div>
    <?php Pjax::end()?>
    
</div>

<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>