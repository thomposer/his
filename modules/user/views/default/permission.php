<?php
use yii\helpers\Html;
use app\common\AutoLayout;
use yii\helpers\Url;
use app\assets\AppAsset;
$exception = Yii::$app->errorHandler->exception;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
?>
<?php $this->beginBlock('renderCss'); ?>
<?php AppAsset::addCss($this, '@web/public/css/user/permission.css')?> 
<?php $this->endBlock()?>
<div class="col-xs-12">
<div class="row round_bg">
	<div class="content col-sm-8 col-md-8">
		<p class="title">访问受限</p>
		<p class="message">很抱歉, 您没有访问权限！</p>
		<p class="message_2">如有疑问，请联系管理员。</p>
		<!--<a class="link" href=<?php // Url::to(['@manageIndex']) ?>>申请访问</a>--> 
		<!--<a href=<?php // Url::to(['@manageIndex']) ?>>返回主页</a>-->
	</div>
	<div class="col-sm-4 col-md-4">
	   <div class="img_div_style">
		<?= Html::img($public_img_path . '/common/permission.png',['class' => 'img_style'])?>
		</div>
	</div>
</div>
</div>
