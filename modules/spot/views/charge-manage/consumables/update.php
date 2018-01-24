<?php

use yii\helpers\Html;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Material */

$this->title = '修改医疗耗材';
$this->params['breadcrumbs'][] = ['label' => '医疗耗材', 'url' => ['consumables-index']];
/* $this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]]; */
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="material-update col-xs-10">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['consumables-index'],['class' => 'right-cancel']) ?>
    </div>
        <div class = "box-body">
        
            <?= $this->render('_form', [
                'model' => $model,
                'spotList' => $spotList
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
<script type="text/javascript">
    require(["<?= $baseUrl ?>/public/js/spot/material.js"], function(main) {
        main.init();
    })
</script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>