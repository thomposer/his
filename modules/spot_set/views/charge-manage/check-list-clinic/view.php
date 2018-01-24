<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot_set\models\CheckListClinic;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\CheckListClinic */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '影像学检查', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="check-list-clinic-view col-xs-10">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Url::to(['@spot_setChargeManageCheckListClinicIndex']),['class' => 'right-cancel','data-pjax' => 0]) ?>
     </div>
        <div class = "box-body">  
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'unit',
            'default_price',
            'price',
            'meta',
            'remark:ntext',
            'international_code:ntext',
            'tagName',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return CheckListClinic::$status[$model->status];
                }
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
