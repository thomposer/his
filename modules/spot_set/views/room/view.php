<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot_set\models\Room;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\Room */

$this->title ='诊室详情';
$this->params['breadcrumbs'][] = ['label' => '诊室管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="room-view col-xs-12">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
     </div>
        <div class = "box-body">  
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=> 'id',
            ],
            'clinic_name',
            'floor',
            [
                'attribute'=>'clinic_type',
                'value'=>Room::$getClinicType[$model->clinic_type],
            ],
            [                   
                'attribute' => 'status',
                'value' => Room::$getStatus[$model->status],
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
