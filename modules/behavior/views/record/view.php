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
    <?php $this->registerCssFile('@web/public/css/bootstrap/bootstrap.css') ?>
    <?php $this->registerJsFile('@web/public/js/jquery/jquery.min.js',['position'=>\yii\web\View::POS_HEAD])?>
    <?php $this->registerJsFile('@web/public/js/bootstrap/bootstrap.min.js',['position'=>\yii\web\View::POS_HEAD])?>
    
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="main_bd main_bootstrap">
	<div class="anchor-view">
	    <p class="button-group">
	        <?= Html::a('删除', ['delete', 'id' => $model->id], [
	            'class' => 'btn btn-danger js-del',
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
<?php $this->endBlock();?>
<?php $this->beginBlock('artemplate')?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>

