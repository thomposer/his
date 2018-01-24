<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Organization */

$this->title = '新增机构';
$this->params['breadcrumbs'][] = ['label' => '机构管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/city-picker.css')?>
    <?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="organization-create col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['index'],['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">    

            <?= $this->render('_form', [
                'model' => $model,
                'templateList' => $templateList
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
  <?php AppAsset::addScript($this, '@web/public/js/lib/city-picker.data.js')?>
  <?php AppAsset::addScript($this, '@web/public/js/lib/city-picker.js')?>
  <script type="text/javascript">
		require(["<?= $baseUrl ?>"+"/public/js/spot/organization.js?v="+ '<?= $versionNumber ?>'],function(main){
			main.init();
		});
	</script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>