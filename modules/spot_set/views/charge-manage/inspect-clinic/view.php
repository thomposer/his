<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\InspectClinic */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inspect Clinics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>

<div class="inspect-clinic-view col-xs-10">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['index'],['class' => 'right-cancel','data-pjax' => 0]) ?>
     </div>
        <div class = "box-body">  
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'spot_id',
            'inspect_id',
            'inspect_price',
            'cost_price',
            'deliver',
            'specimen_type',
            'cuvette',
            'inspect_type',
            'remark',
            'description',
            'status',
            'create_time',
            'update_time',
        ],
    ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
