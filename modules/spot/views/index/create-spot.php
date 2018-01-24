<?php

use app\assets\AppAsset;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\Room */
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
AppAsset::addCss($this, '@web/public/css/spot/createSpot.css');
AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css');
AppAsset::addCss($this, '@web/public/css/lib/upload.css');
AppAsset::addCss($this, '@web/public/css/lib/city-picker.css');
?>
    <div class = 'row'>
        <div class = 'title'>
        你的机构下，目前还没有诊所，请创建一个新诊所
        </div>
    </div>
    <div class="room-view col-xs-12">
        <?= $this->render('_form',['model' => $model]); ?>
    </div>



    <script type="text/javascript">
  		var uploadUrl = '<?= Url::to(['@manageSitesUpload']); ?>';
		require(["<?= $baseUrl ?>"+"/public/js/spot/spot.js?v="+'<?= $versionNumber ?>'],function(main){
			main.init();
		});
	</script>