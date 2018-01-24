<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Spot */

$this->title = '编辑诊所';
$this->params['breadcrumbs'][] = ['label' => '诊所管理', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css') ?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/upload.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/city-picker.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/spot/create.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="spot-update col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['index'],['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">
        
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
<?php AppAsset::addScript($this, '@web/public/js//lib/city-picker.data.js')?>
<?php AppAsset::addScript($this, '@web/public/js/lib/city-picker.js')?>
  <script type="text/javascript">
  		var uploadUrl = '<?= Url::to(['@manageSitesUpload']); ?>';
		require(["<?= $baseUrl ?>"+"/public/js/spot/spot.js?v="+ '<?= $versionNumber ?>'],function(main){
			main.init();
		});
	</script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>