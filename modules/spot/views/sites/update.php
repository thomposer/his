<?php

/* @var $this \yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\common\AutoLayout;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
$this->title = '修改站点信息';
$this->params['breadcrumbs'][] = ['label' => 'Wxinfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss');?>
    <?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>                                            
<!-- 页面主体 -->
  <div class = "col-xs-12">
    <div class = "box">
        <div class = "box-body">
	        <?php echo $this->render('_form',['model'=>$model,'templateList' => $templateList])?>       
        </div>
    </div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
    <script type="text/javascript">
    	require(["<?= $baseUrl ?>"+"/public/js/spot/spot.js"],function(main){
    		main.init();
		});
	</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
