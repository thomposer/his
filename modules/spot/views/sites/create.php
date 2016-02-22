<?php

/* @var $this \yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\common\AutoLayout;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
$baseUrl = Yii::$app->request->baseUrl;
$this->title = '申请站点';
$layoutUrl = '/modules/apply/views/layouts/layout.php';
if(Yii::$app->session->get('spot')){
    $layoutUrl = '/views/layouts/layout.php';
}
?>
<?php AutoLayout::begin(['viewFile'=>'@app'.$layoutUrl])?>
<?php $this->beginBlock('renderCss');?>
   <?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>    
   <?php AppAsset::addCss($this,'@web/public/css/rbac/rbac.css')?>
 <?php $this->endBlock();?>
<?php $this->beginBlock('content')?>     
<!-- 页面主体 -->
  <div class = "col-xs-12">
    <div class = "box">
        <div class = "box-body">
 	           <?php echo $this->render('_form',['model'=>$model,'templateList' => $templateList]);?>
       </div>
    </div>
 </div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
    <script type="text/javascript">
    	require(["<?php echo $baseUrl ?>"+"/public/js/spot/spot.js"],function(main){
    		main.init();
		});
	</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>

