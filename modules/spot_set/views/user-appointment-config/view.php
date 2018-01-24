<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\UserAppointmentConfig */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Appointment Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="user-appointment-config-view col-xs-12">
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
            'spot_id',
            'user_id',
            'spot_type_id',
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
