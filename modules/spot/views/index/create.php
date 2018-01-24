<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Spot */

$this->title = '新增诊所';
$this->params['breadcrumbs'][] = ['label' => '诊所管理', 'url' => ['index']];
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
<?php if($_COOKIE['createSpot'] == 1):?>

<div class="col-xs-12">
    <div class="box-top">
        <?=  Html::img(($baseUrl.'/public/img/spot/tips.png'),['class' => 'img-size']) ?>
        <span class="wording-alert">您的机构下，目前还没有诊所，请创建一个新的诊所。</span>
    </div>

</div>

<?php endif;?>
<div class="spot-create col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
        <?php if($_COOKIE['createSpot'] != 1):?>
            <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['index'],['class' => 'right-cancel']) ?>
        <?php endif;?>
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
        $('#spot-addselected').find('label').append('<span class="alert-wording-color">（必须加入才能进入该诊所）</span>');

		require(["<?= $baseUrl ?>"+"/public/js/spot/spot.js?v="+'<?= $versionNumber ?>'],function(main){
			main.init();
		});


  </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>