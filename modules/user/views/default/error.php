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
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss'); ?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content')?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?= Html::encode($name); ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="error-page">
        <h3 class="headline text-red" style = "margin-left:-105px;margin-right:20px;font-size:44px">
            <?= Html::encode($name); ?>
        </h3>

        <div class="error-content" style = "width:100%">
          <h3><i class="fa fa-warning text-red"></i><?= Html::encode($message); ?></h3>

          <p>
            We will work on fixing that right away.
            Meanwhile, you may <a href="<?= Url::to(['@manageDefaultIndex']); ?>">return to dashboard</a>
          </p>

<!--           <form class="search-form"> -->
<!--             <div class="input-group"> -->
<!--               <input type="text" name="search" class="form-control" placeholder="Search"> -->

<!--               <div class="input-group-btn"> -->
<!--                 <button type="submit" name="submit" class="btn btn-danger btn-flat"><i class="fa fa-search"></i> -->
<!--                 </button> -->
<!--               </div> -->
<!--             </div> -->
            <!-- /.input-group -->
<!--           </form> -->
        </div>
      </div>
      <!-- /.error-page -->

    </section>
    <!-- /.content -->
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
<!-- FastClick -->
<!-- <script src="../../plugins/fastclick/fastclick.js"></script> -->
<?php $this->endBlock()?>

<?php AutoLayout::end()?>
