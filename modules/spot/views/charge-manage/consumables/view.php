<?php

use app\modules\spot_set\models\Material;
use app\modules\spot\models\Tag;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot\models\Consumables;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Material */

$this->title = '医疗耗材详情';
$this->params['breadcrumbs'][] = ['label' => '医疗耗材', 'url' => ['consumables-index']];
$this->params['breadcrumbs'][] = $this->title;
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
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['consumables-index'],['class' => 'right-cancel']) ?>
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
                'attribute' => 'unionSpotId',
                'value' => ConfigureClinicUnion::getClinicNameListString($model->id,ChargeInfo::$consumablesType)[$model->id]['spotName']
            ],
            [
                'attribute' => 'status',
                'value' => Material::$getStatus[$model->status]
            ],
            [
                'attribute' => 'tag_id',
                'value' => Tag::getTagList(['name'],['id' => $model->tag_id])[0]['name'],
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
