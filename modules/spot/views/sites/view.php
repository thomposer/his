<?php

/* @var $this \yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\common\AutoLayout;

$baseUrl = Yii::$app->request->baseUrl;
$this->title = '添加站点成功';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss');?>
    <?php $this->registerCssFile('@web/public/css/form.css', ['depends' => \app\assets\AppAsset::className()]);?>
 <?php $this->endBlock();?>
<?php $this->beginBlock('content')?>                                            
        <!-- 页面主体 -->
        <div  style="margin: 20px 50px">
	 	    <div class="main_hd">
		        <h2>添加新的站点成功<a href="<?php echo $baseUrl . '/wxinfo/sites/create'?>" class="btn btn_primary right_button">继续添加</a></h2>
		    </div>
	        <div class="main_hd" id="main">
	        	<div class="main_wrap">
				<h2><strong>请在微信公众号平台配置以下内容：</strong></h2>
				<div class="field-group">
					<div class="field-item">
						<p>
							<span class="field-label">TOKEN: </span>
							<span class="field-input"><?php echo $model->token ?></span>
						</p>
					</div>	
					<div class="field-item">
						<p>
							<span class="field-label">URL: </span>
							<span class="field-input"><?php echo $model->url ?></span>
						</p>
					</div>	
				</div>
				</div>
	        </div>       
        </div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>

