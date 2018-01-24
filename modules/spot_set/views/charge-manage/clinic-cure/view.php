<?php

use app\common\AutoLayout;
use app\modules\spot\models\CureList;
use app\modules\spot\models\Tag;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\ClinicCure */

$this -> title = $model -> name;
$this -> params['breadcrumbs'][] = ['label' => '治疗医嘱', 'url' => ['index']];
$this -> params['breadcrumbs'][] = $this -> title;
$baseUrl = Yii ::$app -> request -> baseUrl;
?>
<?php AutoLayout ::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this -> beginBlock('renderCss') ?>

<?php $this -> endBlock(); ?>
<?php $this -> beginBlock('content') ?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="clinic-cure-view col-xs-10">
    <div class="box">
        <div class="box-header with-border">
            <span class='left-title'> <?= Html ::encode($this -> title) ?></span>
            <?= Html ::a(Html ::img($baseUrl . '/public/img/common/icon_back.png') . '返回',Url::to(['@spot_setChargeManageCureClinicIndex']), ['class' => 'right-cancel', 'data-pjax' => 0]) ?>
        </div>
        <div class="box-body">
            <?= DetailView ::widget([
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
                        'value' => Tag::getTagList(['name'], ['id' => $model -> tag_id])[0]['name'],
                    ],
                    'price',
                    'default_price',
                    [
                        'attribute' => 'status',
                        'value' => CureList ::$getStatus[ $model -> status ],
                    ],
                    'create_time:datetime',
                    'update_time:datetime'
                ]
            ]) ?>
        </div>
    </div>
</div>
<?php $this -> endBlock() ?>
<?php $this -> beginBlock('renderJs') ?>

<?php $this -> endBlock() ?>
<?php AutoLayout ::end() ?>
