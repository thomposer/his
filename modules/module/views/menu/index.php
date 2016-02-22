<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
use yii\helpers\Url;
use app\modules\module\models\Menu;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\module\models\search\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '模块菜单';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this,'@web/public/css/search.css')?>
    
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="menu-index col-xs-12">
    <p class = "applySearch-button">
        <?php echo  Html::a('添加菜单', ['@moduleMenuCreate'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class = "box">
    <div class = "box-body">
    <?php  echo $this->render('_search', ['model' => $searchModel,'titleList' => $titleList]); ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => ' table table-hover table-bordered'],
        'layout'=> '{items}<div class="text-right tooltip-demo">{pager}</div>',
        'pager'=>[
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'尾页',
        ],
        'columns' => [            
            'description',
            'menu_url',
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
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'contentOptions' => ['class' => 'op-group'],
                'headerOptions'=>['class'=>'op-header'],
        ],
    ]
    ]); ?>
        </div>
    </div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
	<script type="text/javascript">
		var deleteUrl = "<?php echo Url::to(['@moduleMenuDelete']) ?>";
		var updateUrl = "<?php echo Url::to(['@moduleMenuUpdate']) ?>";
		var indexUrl =  "<?php echo Url::to(['@moduleMenuIndex']) ?>";
    	require(["<?php echo $baseUrl ?>"+"/public/js/module/menu.js"],function(main){
        	main.init();
    	});
	</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>