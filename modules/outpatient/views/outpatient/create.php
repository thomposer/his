<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */

$this->title = '';
$this->params['breadcrumbs'][] = ['label' => 'Outpatients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/form.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class = 'col-sm-12 col-md-2'>

</div>
<div class="outpatient-create col-xs-12 col-sm-12 col-md-10">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">    

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>