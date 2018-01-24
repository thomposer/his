<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\MedicalFee */

$this->title = '诊金配置详情';
$this->params['breadcrumbs'][] = ['label' => '诊金配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="medical-fee-view col-xs-10">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Url::to(['@spot_setChargeManageMedicalFeeClinicIndex']),['class' => 'right-cancel']) ?>
     </div>
        <div class = "box-body">  
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'price',
            [
                'attribute' => 'status',
                'value' => $model::$getStatus[$model->status],
            ],
            'remarks',
            'note',
            [
                'attribute' => 'create_time',
                'value' => date('Y-m-d H:i', $model->create_time),
            ],
            [
                'attribute' => 'update_time',
                'value' => date('Y-m-d H:i', $model->update_time),
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