<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\common\AutoLayout;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\rbac\models\search\RoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '角色管理';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>

<div class="role-index col-xs-12">
   <?php if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>            
    <p>
        <?= Html::a('创建角色', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif;?>
    <div class = "box">
    <div class = "box-body">
   <table class="table table-bordered">

   <thead>
	    <tr>
	        <th>角色名称</th>
	        <th>角色描述</th>
	        <th class="op-header">操作</th>
	    </tr>
    </thead>
        <?php foreach ($roles as $v): ?>
        
	     <tr>
            <td><?php echo Html::encode(trim(str_replace($prefix,'',$v->name)));?></td>
            <td><?php echo Html::encode($v->description);?></td>
            <td class="op-group">
                <?php if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/update', $this->params['permList'])):?>            
                    <?php echo Html::a("<span class='glyphicon glyphicon-pencil'></span>",['@rbacRoleUpdate','id'=>$v->name]);?>
                <?php endif;?>
                <?php if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/delete', $this->params['permList'])):?>            
                <?= Html::a('<span class="glyphicon glyphicon-trash"></span>', ['@rbacRoleDelete', 'id' => $v->name], [
                    'data' => [
                        'confirm' => '你确定要删除此项吗?',
                        'method' => 'post',
                        'title' => '删除',
                        'aria-label' => '删除',
                        'data-pjax' => '0',
                    ],
                ]) ?>  
                <?php endif;?>             
            </td>
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