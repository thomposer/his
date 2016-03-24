<?php

/* @var $this \yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\common\AutoLayout;
use yii\helpers\Url;
use yii\grid\GridView;
use app\assets\AppAsset;
$this->title = '申请站点列表';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$layoutUrl = '/modules/apply/views/layouts/layout.php';
?>
<?php AutoLayout::begin(['viewFile'=>'@app'.$layoutUrl])?>
<?php $this->beginBlock('renderCss');?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>                                            
        
		<div class="spot-index col-xs-12">
		<div class = "box">
        <div class = "box-body">		
		  <?php echo $this->render('_listSearch',['model' => $searchModel, 'renderStatusList' => $renderStatus]) ?>
		    <?= GridView::widget([
		        'dataProvider' => $dataProvider,
                'showOnEmpty' => true,
                'tableOptions' => ['class' => 'table table-bordered table-hover'],
                'layout'=> '{items}<div class="text-right tooltip-demo">{pager}</div>',
                'pager'=>[
                    //'options'=>['class'=>'hidden']//关闭自带分页
                    'firstPageLabel'=>"首页",
                    'prevPageLabel'=>'上一页',
                    'nextPageLabel'=>'下一页',
                    'lastPageLabel'=>'尾页',
                ],
		        'columns' => [
					['attribute'=>'站点名称','value'=>function($data) {
						return $data->spot_name . ' [' . $data->spot . ']';
					}],
					['attribute'=>'是否已审核','value'=>function($data) {
						$renderStatus = array('审核中', '已审核');
						return $renderStatus[$data->render];
					}],
		        ],
		    ]); ?>
		
		</div>    
    </div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
	<script type="text/javascript">
		
		var templateUrl = "<?php echo Url::to(['@spotSitesTemplate']) ?>";
		var updateUrl = "<?php echo Url::to(['@spotSitesUpdate']) ?>";
		var indexUrl =  "<?php echo Url::to(['@spotSitesIndex']) ?>";
		
		//var permission_data = <?php //echo $permission?$permission:''?>;
    	require(["<?php echo $baseUrl ?>"+"/public/js/spot/spot.js"],function(main){
        	main.init();
    	});
	</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>

