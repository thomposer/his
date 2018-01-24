<?php

use app\modules\spot_set\models\Material;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\Tag;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\common\AutoLayout;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\Material */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '其他管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '查看其他详情';
$baseUrl = Yii::$app->request->baseUrl;
$columnF = [
    'id',
    'name',
    'product_name',
    'en_name',
    [
        'attribute' => 'type',
        'value' => Material::$typeOption[$model->type]
    ],
    [
        'attribute' => 'attribute',
        'value' => Material::$attributeOption[$model->attribute]
    ],
    'specification',
    'unit',
    'price',
    'default_price',
    'meta',
    'manufactor'
];
$columnS = $model->attribute == 1 ? [] : [
    'warning_num',
    'warning_day',
];
$columnT = ['remark',
    [
        'attribute' => 'status',
        'value' => Material::$getStatus[$model->status]
    ],
    [
        'attribute' => 'tag_id',
        'value' => Tag::getTagList(['name'], ['id' => $model->tag_id])[0]['name'],
    ],
    'create_time:datetime',
    'update_time:datetime',];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="material-view col-xs-10">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'> <?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回',Url::to(['@spot_setChargeManageMaterialIndex']), ['class' => 'right-cancel']) ?>
        </div>
        <div class = "box-body">  
            <?=
            DetailView::widget([
            'model' => $model,
            'attributes' => array_merge($columnF,$columnS,$columnT)
            ])
            ?>
        </div>
    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php AutoLayout::end() ?>
