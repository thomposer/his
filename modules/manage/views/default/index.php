<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\common\AutoLayout;
$baseUrl = Yii::$app->request->baseUrl;
$logout = Yii::getAlias('@userIndexLogout');

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/main.php'])?>
<?php $this->beginBlock('renderCss')?>

<?php $this->endBlock()?>
<?php $this->beginBlock('content')?>
    <div class="container page_media_list page_appmsg_list">
        <!-- 页面主体 -->
        <div class="col_main" id="main">
        	<div style="margin: 200px auto; background: #EEE; padding: 50px; text-align: center;">
	        	<div class="main_hd">
			        <h2>选择操作站点</h2>
			    </div>
			   	<form action="<?= Url::to(['@manageSites']) ?>" method="post">
					<select name="id" class="db" style="margin: 10px auto; height: 30px;width:100px">
					   <?php if($list):?>
						<?php foreach($list as $wxInfo):?>
						  <option value="<?= $wxInfo['id'] ?>"><?= $wxInfo['spot_name'] ?></option>
						<?php endforeach; ?>
						<?php endif;?>
					</select>
					<input type="hidden" value="<?= Yii::$app->request->csrfToken ?>" name="_csrf" />
					<input class="btn btn_primary" type="submit" value="进入">
			   	</form>
     	
        	</div>

        </div>
    </div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>

<?php $this->endBlock()?>
<?php AutoLayout::end()?>