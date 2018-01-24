<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot\models\CureList;
use app\modules\spot\models\Tag;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CureList */

$this->title = '治疗医嘱详情';
$this->params['breadcrumbs'][] = ['label' => '治疗医嘱', 'url' => ['cure-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="cure-list-view col-xs-10">
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
            'unit',
            'meta',
            'remark:ntext',
            'international_code',
            [
                'attribute' => 'tag_id',
                'value' => Tag::getTagList(['name'],['id' =>$model->tag_id])[0]['name'],
            ],
            [
                'attribute' => 'unionSpotId',
                'value' => ConfigureClinicUnion::getClinicNameListString($model->id,ChargeInfo::$cureType)[$model->id]['spotName']
            ],
            [
                'attribute' => 'status',
                'value' => CureList::$getStatus[$model->status],
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
