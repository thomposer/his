<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;

/* @var $this yii\web\View */
/* @var $model app\modules\wyf\models\FansAdmin */
$this->title = '行为记录详情';
$baseUrl = Yii::$app->request->baseUrl;
$this->params['breadcrumbs'][] = ['label' => 'Fans Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="col-xs-12">
	<div class = "box">
       <div class = "box-body">
	    <p class="button-group">
	        <?= Html::a('删除', ['delete', 'id' => $model->id], [
	            'class' => 'btn btn-danger',
	            'data' => [
	                'confirm' => '你确定要删除此项吗?',
	                'method' => 'post'
	           ]
	        ]) ?>
	        <?= Html::a('返回列表', ['index'], ['class' => 'btn btn-primary']) ?>
	    </p>
	
	    <?= DetailView::widget([
	        'model' => $model,
	        'attributes' => [
	            'user_id',
	            'ip',
				['attribute' => 'spot', 'value' => $spotList[$model->spot]],
				['attribute' => 'module', 'value' => $moduleList[$model->module]],
				['attribute' => 'action', 'value' => $actionList[$model->action]],
	            'data',
	            'operation_time',
	        ],
			'template' => '<tr><th style="width: 200px;">{label}</th><td>{value}</td></tr>',
	    ]) ?>
	   </div>
	</div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>

