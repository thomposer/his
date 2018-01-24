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
$this->params['breadcrumbs'][] = ['label' => '模块列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="menu-index col-xs-12">
	<div class = "box">
		<div class = 'row search-margin'>
          <div class = 'col-sm-12 col-md-12'>
            <?= $this->render('_search', ['model' => $searchModel,]); ?>
          </div>
        </div>
	    <?= GridView::widget([
	        'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive add-table-padding'],
                'tableOptions' => ['class' => 'table table-hover table-border header'],
	            'layout'=> '{items}<div class="text-right tooltip-demo">{pager}</div>',
	    		'pager'=>[
	    			//'options'=>['class'=>'hidden']//关闭自带分页

                    'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
                    'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
                    'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
                    'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
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
                            $hasModule = $manager->getPermission(Yii::$app->cache->get(Yii::getAlias('@parentSpotCode').$_COOKIE['spotId'].Yii::$app->user->identity->id).'_permissions_'.$model->module_name);
                            if($hasModule){
                                return Html::tag('button','已添加',['class' => 'btn btn-disabled disabled']);
                            }
                            if(!isset($this->params['permList']['role']) && !in_array($this->params['requestModuleController'].'/add', $this->params['permList'])){
                                return false;
                            }
							$options = array(
								'title' => Yii::t('yii', 'add'),
								'aria-label' => Yii::t('yii', 'add'),
								'data-confirm' => Yii::t('yii', '你确定要添加该模块?'),
								'data-method' => 'post',
							    'class' => 'btn btn-default ',
							);
							return Html::a('添加', $url, $options);
						
						
					},]
				],
	        ],
	    ]); ?>
	</div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>

