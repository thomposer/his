<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot_set\models\SecondDepartment;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\SecondDepartment */

$this->title = '二级科室详情';
$this->params['breadcrumbs'][] = ['label' => '二级科室', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="second-department-view col-xs-12">
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
            'name',
            [
                'attribute' => 'status',
                'value' => SecondDepartment::$getStatus[$model->status]
                
            ],
            [
                'attribute' => 'appointment_status',
                'value' => SecondDepartment::$getAppointmentStatus[$model->appointment_status]
                
            ],
            [
                'attribute' => 'room_type',
                'value' => SecondDepartment::$getRoomType[$model->room_type]
            ],
            'create_time:datetime',
            'update_time:datetime',
        ],
    ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
