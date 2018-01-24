<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\common\AutoLayout;
use app\modules\spot\models\CureList;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\Tag;
use app\modules\spot\models\AdviceTagRelation;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\RecipeList */

$this->title = '处方医嘱详情';
$this->params['breadcrumbs'][] = ['label' => '处方医嘱', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>

<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="recipe-list-view col-xs-10">
    <div class = "box">
      <div class="box-header with-border">
      <span class = 'left-title'> <?=  Html::encode($this->title) ?></span>
       <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
     </div>
        <div class = "box-body">  
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
//            'price',
//            'default_price',
            'manufactor',
//            [
//                'attribute' => 'address',
//                'value' => RecipeList::$getAddress[$model->address]
//            ],
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
                'attribute' => 'tag_id',
                'value' => Tag::getTagList(['name'],['id' => $model->tag_id])[0]['name'],
            ],
            [
                'attribute' => 'adviceTagId',
                'value' => AdviceTagRelation::getTagInfoView($model->id, AdviceTagRelation::$recipeType)
            ],
            [
                'attribute' => 'status',
                'value' => RecipeList::$getStatus[$model->status]
            ],
            'remark',
            [
                'attribute' => 'unionSpotId',
                'value' => ConfigureClinicUnion::getClinicNameListString($model->id,ChargeInfo::$recipeType)[$model->id]['spotName']
            ],
            [
                'format' => 'raw',
                'attribute' => 'high_risk',
                'value' => function($model){
                    if(1 == $model->high_risk){
                        return '<span style="color:red;">'.RecipeList::$getHighRiskStatus[$model->high_risk].'</span>';
                    }
                    return RecipeList::$getHighRiskStatus[$model->high_risk];
                }
            ],
//            [
//                'attribute' => 'default_frequency',
//                'value' => RecipeList::$getDefaultConsumption[$model->default_frequency]
//            ],
        ],
    ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>

<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
