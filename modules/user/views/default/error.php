<?php
use yii\helpers\Html;
use app\common\AutoLayout;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */
?>
<!-- Content Wrapper. Contains page content -->
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss'); ?>

<?php $this->endBlock()?>
<?php $this->beginBlock('content')?>
<!-- Content Header (Page header) -->

        <div>
                <?php
                    if($exception->statusCode==406){
                        echo $this->render('permission', ['errorCode' => $exception->statusCode]);
                    }else {
                        echo $this->render('404', ['errorCode' => $exception->statusCode]);
                    }
                ?>
        </div>

<!-- /.content -->
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
<!-- FastClick -->
<!-- <script src="../../plugins/fastclick/fastclick.js"></script> -->
<?php $this->endBlock()?>

<?php AutoLayout::end()?>
