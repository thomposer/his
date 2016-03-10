<?php

/* @var $this \yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\common\AutoLayout;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\base\View;
use app\assets\AppAsset;
$this->title = '站点信息';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
// $layoutUrl = '/modules/apply/views/layouts/layout.php';
// if(isset($this->params['spot'])){
//     $layoutUrl = '/views/layouts/layout.php';
// }
?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss');?>
    <?php AppAsset::addCss($this,'@web/public/css/search.css')?>    
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>                            
<div class="col-xs-12">
   <div class = "box">
    <div class = "box-body">         
        <?php if($render):?>
            <?php echo $this->render('_search',['model' => $searchModel,'status' => $status,'spot'=>$spot]) ?>
			<div class="spot-index">
			    <?= GridView::widget([
			        'dataProvider' => $dataProvider,
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
			            ['attribute'=>'站点名称','value'=>function($data){return $data->spot_name;}],
						['attribute'=>'站点代码','value'=>function($data){return $data->spot;}],
	                    ['attribute'=>'初始模板','value'=>function($data){return $data->template;}],
						[
							//动作列yii\grid\ActionColumn
							//用于显示一些动作按钮，如每一行的更新、删除操作。
							'class' => 'app\common\component\ActionColumn',
							'template' => '{update}</td><td>{template}',//只需要展示更新
							'headerOptions' => ['width' => '120','colspan' => 2],
							'buttons' => [
								'update' => function($url, $model, $key) {
								        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $key]);
								},
								'template' => function($url, $model, $key) {
								     if($model['render'] == 0){
								        return  Html::a('初始化', ['@spotSitesTemplate', 'id' => $model->id], [
								             'class' => 'btn btn-success',
								             'data' => [
								                 'confirm' => '你确定要初始化该站点吗?',
								                 'method' => 'post',
								             ],
								         ]);
								         								         
								     }
								     return '已初始化';
								}
							],
						],
			        ],
			    ]); ?>
			
			</div> 
        <?php else: ?>
			<?= DetailView::widget([
			    'model' => $model,
			    'template' => '<tr><th style="width: 160px">{label}</th><td style = "line-height:30px">{value}</td></tr>',
			    'attributes' => [
			        ['label'=>'站点名称','value'=> $model->spot_name],
					['label'=>'站点英文简称','value'=> $model->spot],
					['label'=>'审核状态','value'=> $model->render === 0 ? '未审核' : '已审核'],
			    ],
			]); ?>   
        <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>

