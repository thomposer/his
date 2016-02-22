<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
use yii\widgets\ActiveForm;
use yii\base\View;
use yii\helpers\Url;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\rbac\models\search\PermissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '权限管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;

?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>    
    <?php AppAsset::addCss($this,'@web/public/css/rbac/rbac.css')?>    
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="permission-index col-xs-12">

    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


<div class = "box">
<div class = "box-body">    
<div class="auth-item-index grid-view">
 <p class = "create">
        <?= Html::a('创建权限', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('权限分类', ['create_category'], ['class' => 'btn btn-success']) ?>
 </p>
  <!-- Single button -->
        <div class="btn-group">
            
             <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                   <?php echo $currentCategory?$currentCategory:'请选择权限类别'?>
                   <span class="caret"></span>          
              </button>
                       <ul class="dropdown-menu" role="menu">
                          <li><?= Html::a('全部权限', ['index']) ?></li>
                        <?php if($categories):?>
        				<?php foreach ($categories as $category):?>
                          <li><?= Html::a($category->description, ['index','currentCategory'=>$category->name]) ?></li>
              
                         <?php endforeach;?>
        		         <?php endif;?>
                       </ul>
    </div>
 <div class="btn-group">
            
             <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                   <?php echo $currentSpotInfo?$currentSpotInfo[1]:'切换站点权限'?>
                   <span class="caret"></span>          
              </button>
                       <ul class="dropdown-menu" role="menu">
                          <li><?= Html::a('当前站点', ['index']) ?></li>
                        <?php if($allspot):?>
        				<?php foreach ($allspot as $v):?>
                          <li><?= Html::a($v->spot_name, ['index','currentSpot'=>$v->spot]) ?></li>
              
                         <?php endforeach;?>
        		         <?php endif;?>
                       </ul>
    </div>                 

 <table class="table table-hover table-bordered">
	<tr class="tb_header">
		<th>标题</th>
		<th>描述</th>
		<th class="op-header">操作</th>
	</tr>
	    <?php if($items):?>
		<?php foreach ($items as $row ): ?>
    		<?php foreach ($row as $v):?>
	    		<tr>
	    			<td><?php echo $v->description?></td>
	    			<td><?php echo trim(str_replace($currentSpotInfo[0].'/', '',$v->name))?></td>
	    			<td class="op-group">
	    				<?= Html::a("<span class='glyphicon glyphicon-pencil'></span>", ['update','id'=>$v->name,'currentCategory'=>$v->data]) ?>
	    				<?= Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $v->name], [
                            'data' => [
                                'confirm' => '你确定要删除此项吗?',
                                'method' => 'post',
                            ],
                        ]) ?>
	    			</td>
	    		</tr>
    		<?php endforeach;?>
		<?php endforeach;?>
		<?php endif;?>
			
		
	
</table>
</div>
    
</div>
</div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
		
<?php $this->endBlock();?>
<?php AutoLayout::end();?>