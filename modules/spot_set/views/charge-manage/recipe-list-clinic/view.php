<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot\models\CureList;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\Tag;
use app\modules\spot\models\AdviceTagRelation;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\RecipeList */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '处方医嘱', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<?php
//var_dump($model);
//exit();
//?>
<div class="recipe-list-view col-xs-10">
    <div class="box">
        <div class="box-header with-border">
            <span class='left-title'> <?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['@spot_setChargeManageRecipeClinicIndex']), ['class' => 'right-cancel']) ?>
        </div>
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'name',
                    'product_name',
                    'en_name',
                    [
                        'attribute' => 'drug_type',
                        'value' => RecipeList::$getDrugType[$model->drug_type]
                    ],
                    'specification',
                    [
                        'attribute' => 'type',
                        'value' => RecipeList::$getType[$model->type]
                    ],
                    'dose_unit',
                    [
                        'attribute' => 'unit',
                        'value' => RecipeList::$getUnit[$model->unit]
                    ],
                    [
                        'attribute' => 'insurance',
                        'value' => RecipeList::$getInsurance[$model->insurance]
                    ],
                    'price',
                    'default_price',
                    'manufactor',
                    [
                        'attribute' => 'address',
                        'value' => RecipeList::$getAddress[$model->address]
                    ],
                    'shelves',
                    'app_number',
                    'import_regist_no',
                    'international_code',
                    'meta',
                    [
                        'attribute' => 'default_used',
                        'value' => RecipeList::$getDefaultUsed[$model->default_used]
                    ],
                    [
                        'attribute' => 'default_consumption',
                        'value'=> RecipeList::$getDefaultConsumption[$model->default_consumption]
                    ],
                    [
                        'attribute' => 'skin_test_status',
                        'value' => RecipeList::$getSkinTestStatus[$model->skin_test_status]
                    ],
                    'skin_test',
                    [
                        'attribute' => 'tag_name',
                    ],
                    [
                        'attribute' => 'general_name',
                        'value' =>  function ($model) {
                            return AdviceTagRelation::getTagInfoView($model->recipelist_id, AdviceTagRelation::$recipeType);
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => RecipeList::$getStatus[$model->status]
                    ],
                    'remark',
                    [
                        'format' => 'raw',
                        'attribute' => 'high_risk',
                        'value' => function ($model) {
                            if (1 == $model->high_risk) {
                                return '<span style="color:red;">' . RecipeList::$getHighRiskStatus[$model->high_risk] . '</span>';
                            }
                            return RecipeList::$getHighRiskStatus[$model->high_risk];
                        }
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php AutoLayout::end() ?>
