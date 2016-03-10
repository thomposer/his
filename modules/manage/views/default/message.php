<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */
$baseUrl = Yii::$app->request->baseUrl;
use app\common\AutoLayout;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>

<?php $this->beginBlock('renderCss');?>
<?php $this->endBlock();?>

<?php $this->beginBlock('content');?>

<?php $this->endBlock();?>

<?php $this->beginBlock('renderJs');?>
<script type="text/javascript">

	var title = "<?= Yii::$app->request->get('title') ?>";
	var message = "<?= Yii::$app->request->get('message'); ?>";
	var url = "<?= Yii::$app->request->get('url') ?>";
	require(["<?= $baseUrl ?>"+"/public/js/lib/alert.js"],function(main){
 		main.init();
	});	
</script>
<?php $this->endBlock();?>
<?php AutoLayout::end()?>

                
