<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\Menu */

$this->title = '添加用户';
$this->params['breadcrumbs'][] = ['label' => 'Menus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
      <!-- Select2 -->
    <?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>

<div class="menu-create col-xs-12">
    <div class = "box">
        <div class = "box-body">
            <?= $this->render('_form', [
            'model' => $model,
            'item_name' => $item_name,
            'userData' => $userData
            ]) ?>
        </div>
    </div>
</div>
<?php $this->endBlock()?>
<?php $this->beginBlock('renderJs')?>
    <script type="text/javascript">
    	require(["<?php echo $baseUrl ?>"+"/public/js/rbac/apply.js?v="+'<?= $versionNumber ?>'],function(main){
    		main.init();
		});
		
	</script>        
<?php $this->endBlock()?>
<?php AutoLayout::end()?>