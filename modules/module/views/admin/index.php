<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\grid\GridView;
use yii\grid\DataColumn;
use yii\helpers\Url;
use app\assets\AppAsset;
use leandrogehlen\treegrid\TreeGrid;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\rbac\models\search\PermissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '模块列表';
$baseUrl = Yii::$app->request->baseUrl;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this,'@web/public/css/lib/search.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="menu-index col-xs-12">
	<div class = "box">
        <div class = 'row search-margin'>
            <div class = 'col-sm-6 col-md-6'>
              <?php if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>            
        
                <?= Html::a('初始化模块', ['@moduleAdminCreate'], ['class' => 'btn btn-default font-body2']) ?>
                <?= Html::a('添加模块',['@moduleAdminList'],['class' => 'btn btn-default font-body2']) ?>
            	<?= Html::a('添加子模块',['@moduleAdminCreateChildren'],['class' => 'btn btn-default font-body2']) ?>
            <?php endif;?>
            </div>
            <div class = 'col-sm-6 col-md-6'>
                <?= $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
        <div class = 'grid-view table-responsive add-table-padding'>
		<?php $form = ActiveForm::begin ([
			'options' => [ 
					'class' => 'form-horizontal',
			        'method' => 'post',
			],
			'fieldConfig' => [ 
					'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>" 
			]
	    ]);
		?>
	    <?= TreeGrid::widget([
	        'dataProvider' => $dataProvider,
	        'keyColumnName' => 'id',
	        'parentColumnName' => 'parent_id',
	        'parentRootValue' => '0', //first parentId value
	        'pluginOptions' => [
// 	            'initialState' => 'collapsed',
	        ],
            'options' => ['class' => 'table table-hover table-border header'],
	   
	        'columns' => [
	            [
				    'label' => '模块名称', 
				    'value' => 'module_description'
            	],  
	            [               
	                'attribute' => '排序',
	                'format' => 'raw',      
		            'contentOptions' => ['class' => 'sort'],
		            'value' => function ($searchModel){
		                echo Html::input('hidden','title_id[]',$searchModel->id);
		                return Html::input('text','sort[]',$searchModel->sort,['style' => 'width:100px','class'=>'form-control','data-id' => $searchModel->id]);
		           	}
            	],
           
				[
				    'class' => 'app\common\component\ActionColumn',
				    'template' => '{update}&nbsp;&nbsp;{edit}&nbsp;&nbsp;{delete}',
				    'headerOptions' => ['class' => ''],
				    'contentOptions' => ['class' => ''],
				    'buttons' => [
				        'update' => function ($url,$model,$key){
				            if(!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/update', $this->params['permList'])){
				                return false;
				            }
							/*更新*/
				            return Html::a('', ['update', 'id' => $model->id],['class' => 'icon_button_css icon_new','title'=>'更新','data-toggle'=>'tooltip']);
				        	
            	        },
            	        'edit' => function ($url,$model,$key){
            	           if(!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/update', $this->params['permList'])){
            	               return false;
            	           }
                           if($model->parent_id == 0){
                               return Html::a('', ['edit', 'id' => $model->id],['class' => 'icon_button_view fa fa-pencil-square-o','title'=>'修改','data-toggle'=>'tooltip']);
                                
                           }     
                           return Html::a('', ['update-children', 'id' => $model->id],['class' => 'icon_button_view fa fa-pencil-square-o','title'=>'修改','data-toggle'=>'tooltip']);
                            
            	        },
            	        'delete' => function ($url,$model,$key){
            	        if(!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/delete', $this->params['permList'])){
            	            return false;
            	        }
            	        $options = [
            	            'data-confirm'=>false,
            	            'data-method'=>false,
            	            'data-request-method'=>'post',
            	            'role'=>'modal-remote',
            	            'data-toggle'=>'tooltip',
            	            'data-confirm-title'=>'系统提示',
            	            'data-delete' => false,
            	            'data-confirm-message'=>Yii::t('yii', 'Are you sure you want to delete this item?'),
            	        ];
            	        return Html::a('<span class="icon_button_view fa fa-trash-o" title="删除", data-toggle="tooltip"></span>',['delete','id' => $model->id],$options);
            	        
            	        }
            	        
            	   ]
            	],
			],
	       
	    ]);
	    
	    echo Html::submitButton('排序',['class' =>  'btn btn-default', 'name' => 'submit-button']);
	    ActiveForm::end();
	    ?>
	    </div>
        </div>
	</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>

