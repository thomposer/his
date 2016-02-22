<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\grid\GridView;
use yii\grid\DataColumn;
use yii\helpers\Url;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\rbac\models\search\PermissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '模块列表';
$baseUrl = Yii::$app->request->baseUrl;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this,'@web/public/css/search.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
	<div class="menu-index col-xs-12">
	<div class = "box">
    <div class = "box-body">
	
		<?= $this->render('_search', ['model' => $searchModel]); ?>
		<?php $form = ActiveForm::begin ([
			'options' => [ 
					'class' => 'form-horizontal' 
			],
			'fieldConfig' => [ 
					'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>" 
			]
	    ]);
		?>
	    <?= GridView::widget([
	        'dataProvider' => $dataProvider,
	    		'tableOptions' => ['class' => 'table table-bordered thread'],
	    		'layout'=> '{items}<div class="text-left tooltip-demo">{pager}</div>',
	    		'pager'=>[
	    			//'options'=>['class'=>'hidden']//关闭自带分页
	    			'firstPageLabel'=>"首页",
	    			'prevPageLabel'=>'上一页',
	    			'nextPageLabel'=>'下一页',
	    			'lastPageLabel'=>'尾页',
	    		],
	       
	        'columns' => [
	           
	            [               
	                'attribute' => '排序',
	                'format' => 'raw',      
		            'contentOptions' => ['class' => 'sort'],
		            'value' => function ($searchModel){
		                echo Html::input('hidden','title_id[]',$searchModel->id);
		                return Html::input('text','sort[]',$searchModel->sort,['style' => 'width:100px','class'=>'form-control','data-id' => $searchModel->id]);
		           	}
            	],
           
				['label' => '模块名称', 'value' => 'module_description'],
				[
					'attribute' => '操作',
					'format' => 'raw',
					'value' => function ($searchModel){
						return Html::a('更新', ['update', 'id' => $searchModel->id]);
					}
				],				
	            
			],
	       
	    ]);
	    
	    echo Html::submitButton('排序',['class' =>  'btn btn_primary submit-btn', 'name' => 'submit-button']);
	    ActiveForm::end();
	    ?>
        </div>
	</div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
<script type="text/javascript">
	var indexUrl = "<?php Url::to(['@moduleAdminIndex'])?>";    
	require(["<?php echo $baseUrl ?>"+"/public/js/module/index.js"],function(main){
		main.init();
	});
</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>

