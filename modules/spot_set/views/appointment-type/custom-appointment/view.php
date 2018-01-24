<?php

use yii\widgets\DetailView;
use app\common\AutoLayout;
use yii\helpers\Html;
$baseUrl = Yii::$app->request->baseUrl;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\spotType */
?>
<?php   AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php   $this->endBlock();?>
<?php   $this->beginBlock('content')?>
<div class="spot-type-view col-xs-12">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=   Html::encode($this->title) ?></span>
       <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
     </div> 
     <div class = "box-body">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            'time',
            'status',
            'create_time',
            'update_time',
        ],
    ]) ?>
        </div>
    </div> 
</div>
<?php   $this->endBlock()?>
<?php   $this->beginBlock('renderJs')?>

<?php   $this->endBlock()?>
<?php   AutoLayout::end()?>