<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\patient\models\Patient */

$this->title = '新增患者';
$this->params['breadcrumbs'][] = ['label' => '病历库', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/city-picker.css')?>
<?php AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/upload.css')?>
<?php AppAsset::addCss($this, '@web/public/css/spot/create.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="patient-create col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Url::to(['@patientIndexIndex']),['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">    

            <?= $this->render('_create_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
<?php AppAsset::addScript($this, '@web/public/js//lib/city-picker.data.js')?>
<?php AppAsset::addScript($this, '@web/public/js/lib/city-picker.js')?>
<script type = "text/javascript">
   	var baseUrl = '<?= $baseUrl ?>';
   	var uploadUrl = '<?= Url::to(['@manageSitesUpload']); ?>';
	require(['<?= $baseUrl?>'+'/public/js/patient/create.js?v='+'<?= $versionNumber ?>'],function(main){
		main.init();
	})
</script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>