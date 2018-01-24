<?php

use app\modules\spot_set\models\Material;
use app\modules\spot\models\Tag;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot\models\Consumables;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Material */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '医疗耗材', 'url' => ['index']];
$this->params['breadcrumbs'][] = '查看医疗耗材详情';
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="material-view col-xs-10">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Url::to(['@spot_setChargeManageConsumablesClinicIndex']),['class' => 'right-cancel']) ?>
     </div>
        <div class = "box-body">  
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'product_name',
            'en_name',
            [
                'attribute' => 'type',
                'value' => Consumables::$getType[$model->type]
            ],
            'specification',
            'unit',
            'meta',
            'manufactor',
            'remark',
            [
                'attribute' => 'status',
                'value' => Material::$getStatus[$model->status]
            ],
            [
                'attribute' => 'tag_name',
            ],
            'price',
            'default_price',
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
