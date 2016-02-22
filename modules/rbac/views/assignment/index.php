<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\base\Widget;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\rbac\models\search\AssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = \Yii::$app->request->baseUrl;

?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php $this->registerCssFile('@web/public/css/bootstrap/bootstrap.css') ?>
    <?php $this->registerCssFile('@web/public/css/apply/search.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<?= $this->render('_search',['model' => $searchModel,'typeList' => $typeList,'allspotLists' => $allspotLists,'spotList' => $spotList]); ?>

<div class="assignment-index center-block">
    <table class="table-class table table-bordered">

        <thead>
    <tr>
        <th>用户简称</th>
        <th>用户名称</th>
        <th>账号类型</th>
        <th>角色</th>
        <?php if($allspotLists):?>
            <th>站点</th>
        <?php endif;?>
        <th colspan=2>操作</th>
    </tr>
    </thead>
        
        <?php if ($models):?>
        <?php foreach ($models as $key => $v): ?>
        
	     <tr>
	        <td><?= $v['user_id'];?></td>
            <td><?= $v['username'];?></td>
            <td>
            <?php 
                switch ($v['type']){
                    case 1:
                        echo 'OA';
                        break;
                    case 2:
                        echo 'QQ';
                        break;
                }
            ?>
            </td>
            <td>
              <?= implode(',', $v['description']) ?>
            </td>
            
           <?php if($allspotLists):?>
            <td>
            <?php if($spotList):?>
                <?php if(array_key_exists($v['spot'], $spotList)):?>
                    <?= $spotList[$v['spot']];?>                             
                <?php endif;?>
            <?php endif;?>
           </td>
           <?php endif;?>
            <td>
                <?php if($v['item_name'] != Yii::getAlias('@systemRole')):?>
                    <?php echo Html::a('修改',['update','user_id'=>$v['user_id'], 'spot' => $spotList ? $key : null]);?>
                <?php else :?>
                                        修改
                <?php endif;?>  
            </td>
            <td>
                <a href="javascript:void(0)" class = "delete" data-id="<?= $v['user_id']?>" data-type = "<?php foreach ($v['item_name'] as $k){echo $k.'|';} ?>">删除</a>        
                  
            </td>
        </tr>
     
        <?php endforeach;?>
       <?php else: ?>
       	<tr><td class="tc" colspan=<?= $spotList ? '7' : '6' ?>>暂无信息！</td></tr>
       <?php endif;?> 
        
    </table>
 <?php if($models):?>
    <?= LinkPager::widget([
        'pagination' => $pages,
        'firstPageLabel'=>"首页",
        'prevPageLabel'=>'上一页',
        'nextPageLabel'=>'下一页',
        'lastPageLabel'=>'尾页',
    ]); ?>
<?php endif;?>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('artemplate')?>
	
	<script type="text/javascript">
		
		var deleteUrl = "<?php echo Url::to(['@RbacAssignmentDelete']) ?>";
		var updateUrl = "<?php echo Url::to(['@RbacAssignmentUpdate'])?>";
		var indexUrl =  "<?php echo Url::to(['@RbacAssignment']) ?>";
		
		//var permission_data = <?php //echo $permission?$permission:''?>;
    	require(["<?php echo $baseUrl ?>"+"/public/js/rbac/assignment.js"],function(main){
        	main.init();
    	});
	</script>
	
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
