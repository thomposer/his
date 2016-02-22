<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\starroom\models\search\AdimageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$baseUrl = Yii::$app->request->baseUrl;
$this->title = '行为记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php $this->registerCssFile('@web/public/css/bootstrap/bootstrap.css') ?>
    <?php $this->registerCssFile('@web/public/css/apply/search.css')?>
    <?php $this->registerCssFile('@web/public/css/rbac/rbac.css')?>
    <?php $this->registerCssFile('@web/public/css/starroom/adimage/image_preview.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="main_bd main_bootstrap">
    <?php  echo $this->render('_search', ['model' => $searchModel, 'spotList' => $spotList, 'moduleList' => $moduleList, 'actionList' => $actionList ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'layout'=> '{items}<div class="text-left tooltip-demo">{pager}</div>',
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭自带分页
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'尾页',
        ],
        'columns' => [
            'user_id',
			['label'=>'IP地址',  'attribute' => 'ip'],
			[
				'attribute' => 'spot',
				'value' => function ($data) use($spotList) {
					return $spotList[$data->spot];
				},
			],
            [
            	'attribute' => 'module',
            	'value' => function ($data) use($moduleList) {
            		return $moduleList[$data->module] . '(' . $data->module . ')';
            	},
            ],
			[
				'label'=>'动作',
				'attribute' => 'action',
				'value' => function ($data) use($actionList) {
					return $actionList[$data->action] . '(' . $data->action . ')';
				},
			],
			'operation_time',
			[
				'header' => '操作',
				'class' => 'yii\grid\ActionColumn',
				'contentOptions' => ['class' => 'op-group'],
				'headerOptions'=>['class'=>'op-header'],
				'template' => '{view}'
			],
        ],
    ]); 

   ?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('artemplate')?>
<script type="text/javascript">
    var behaviorActionList = "<?= Url::to([Yii::getAlias('@BehaviorActionList')]); ?>";
    var behaviorActionDelete = "<?= Url::to([Yii::getAlias('@BehaviorActionDelete')]); ?>";
   	require(["<?php echo $baseUrl ?>"+"/public/js/behavior/index.js"],function(main){
        main.init();
    });
</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
