<?php


use yii\helpers\Html;
use app\common\AutoLayout;
use yii\grid\GridView;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\rbac\models\search\PermissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '添加模块';
$baseUrl = Yii::$app->request->baseUrl;
$this->params['breadcrumbs'][] = $this->title;

?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/search.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="menu-index col-xs-12">
	<div class = "box">
        <div class = "box-body">
		<?= $this->render('_search', ['model' => $searchModel,]); ?>
		
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
				['label' => '模块名称', 'value' => 'module_description'],
	            [
					'class' => 'app\common\component\ActionColumn',
					'header' => '操作',
				 	'template' => '{add}',
	                'headerOptions' => ['class' => ''],
	                'contentOptions' => ['class' => ''],
					'buttons' => [
					'add' => function($url, $model, $key) {
                            $manager = \yii::$app->authManager;
                            $hasModule = $manager->getPermission(Yii::$app->session->get('spot').'_permissions_'.$model->module_name);
                            if($hasModule){
                                return Html::tag('button','已添加',['class' => 'btn btn-success disabled']);
                            }
                            if(!$manager->checkAccess(Yii::$app->user->identity->user_id, Yii::$app->session->get('spot').Yii::getAlias('@moduleAdminAdd'))){
                                $url='javascript:void(0)';
                                $auth_class='disabled';
                            }
							$options = array(
								'title' => Yii::t('yii', 'add'),
								'aria-label' => Yii::t('yii', 'add'),
								'data-confirm' => Yii::t('yii', '你确定要添加该模块?'),
								'data-method' => 'post',
							    'class' => 'btn btn-info '.$auth_class,
							);
							return Html::a('添加', $url, $options);
						
						
					},]
				],
	        ],
	    ]); ?>
        </div>
	</div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>

