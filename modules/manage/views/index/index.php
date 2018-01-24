<?php

/* @var $this \yii\web\View */
/* @var $content string */
use app\common\AutoLayout;

?>
<?php AutoLayout::begin(['viewFile'=>'@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss');?>
   
 <?php $this->endBlock();?>
<?php $this->beginBlock('content')?>

<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>