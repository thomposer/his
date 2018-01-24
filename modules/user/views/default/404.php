<?php
use app\assets\AppAsset;
use app\common\AutoLayout;
use yii\helpers\Html;

$this->title = '404';
$baseUrl = Yii::$app->request->baseUrl;

?>

<?php $this->beginBlock('renderCss'); ?>
<?php AppAsset::addCss($this, '@web/public/css/user/404.css')?>
<?php $this->endBlock()?>

<div class="round_bg">
	<div class="col-sm-7 col-md-7">

		<span class="text_style_404"> <?= $errorCode ?> </span> <br /> <span
			class="text_style_no_wifi"> 你所请求页面没找到 </span> <br />

		<div class="left_bottom">

			<span class="text_style_tip_1"> 您可以尝试按以下步骤诊断此问题: </span> <br /> <span
				class="text_style_tip_2"> 转到 </span> <span
				class="text_style_tip_2_1">应用程序 > 系统偏好设置 > 网络 > 向导，</span><span
				class="text_style_tip_2_2">以测试连接。</span><br>
			<div class="text_style_tip_3"></div>
			<span class="text_style_tip_3"> 请试试以下办法：</span><br>

			<ul class="text_style_tip_4">
				<li>检查网线或路由器</li>
				<li>重置调制解调器或路由器</li>
				<li>重新连接到Wi-Fi网络</li>
			</ul>

		</div>

	</div>


	<div class="col-sm-5 col-md-5 ">

		<div class="img_div_style">
       <?= Html::img($baseUrl.'/public/img/user/img_404.png',['class' => 'img_style'])?>
       </div>
	</div>
</div>











