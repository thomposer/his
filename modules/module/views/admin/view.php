<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\wyf\models\FansAdmin */

$this->title = '成功添加模块';
$baseUrl = Yii::$app->request->baseUrl;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
 <style>
    p{margin:10px}   
</style>    
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
	<div class="menu-view col-xs-12">
	   <div class = "box">
        <div class = "box-body"> 
	    <p>
	        <?= Html::a('继续添加', ['create'], ['class' => 'btn btn-default']) ?>
	    </p>
		
		<p style="padding-top:20px;">模块</p>
	    <?= DetailView::widget([
	        'model' => $model,
	        'attributes' => [
	            ['label' => '模块名称', 'value' => $model->module_description],
				['label' => '模块英文简称', 'value' => $model->module_name],
	        ],
	    ]) ?>
		
		<p>菜单列表</p>
		<table class="table-class  table table-hover table-bordered">
		<tr class="tb_header">
			<th>菜单名称</th>
			<th>菜单URL</th>
			<th>是否显示在菜单栏</th>
			<th>是否超级管理员模块</th>
			<th>状态</th>
		</tr>
		<?php foreach ($model->getAllMenus()->all() as $row): ?>
    				<tr>
    				    <td><?php echo $row->description ?></td>
    				    <td><?php echo $row->menu_url ?></td>
    				    <td><?php echo $row->type === 0 ? '否' : '是'  ?></td>
    				    <td><?php echo $row->role_type === 0 ? '否' : '是'  ?></td>
    				    <td><?php echo $row->status === 0 ? '不显示' : '显示'  ?></td>
    				</tr>
		<?php endforeach;?>
		</table>	
		</div>	
	</div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>

