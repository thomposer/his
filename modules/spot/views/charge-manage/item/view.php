<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\InspectItem */

$this->title = '检验项目详情';
$this->params['breadcrumbs'][] = ['label' => '检验项目', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="inspect-item-view col-xs-10">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'> <?= Html::encode($this->title) ?></span>
<?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Yii::$app->request->referrer, ['class' => 'right-cancel']) ?>
        </div>
        <div class = "box-body">  
            <?=
            DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' =>   'spot_id',
                        'value' => app\modules\spot\models\Spot::getSpotName($model->spot_id),
                    ],
                    'item_name',
                    'english_name',
                    'unit',
                    [
                        'attribute' => 'status',
                        'value' => app\modules\spot\models\InspectItem::$getStatus[$model->status],
                    ],
                    'create_time:datetime',
                    'update_time:datetime',
                ],
            ])
            ?>
        </div>
    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php AutoLayout::end() ?>
