<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot_set\models\Schedule;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\Schedule */

$this->title = '排班详情';
$this->params['breadcrumbs'][] = ['label' => '班次配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="schedule-view col-xs-12">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
     </div>
        <div class = "box-body">  
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'shift_name',
            'shift_time',
           [                   
                'attribute' => 'status',
                'value' => Schedule::$getStatus[$model->status],
            ],
        ],
    ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
