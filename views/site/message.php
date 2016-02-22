<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $title;
?>
<div id="da-error-wrapper">
                	                   
	<h1 class="da-error-heading"><?= $message ?></h1>
	<p><a href="<?php echo \Yii::$app->request->getReferrer()?>">返回上一页</a></p>
	
</div>
                
