<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\modules\outpatient\models\CureRecord;
use app\modules\spot\models\RecipeList;
use app\common\Common;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\RecipeRecord */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="cure-record-index">
    <?php
    $form = ActiveForm::begin([
//                'action' => Url::to(['@pharmacyIndexDispense', 'id' => Yii::$app->request->get('id')]),
                'id' => 'cure-record ModalRemoteDispense',
                'options' => ['data' => ['pjax' => true]],
            ])
    ?>
    <?= $this->render(Yii::getAlias('@orderFillerInfo'), ['record_id' => $model->record_id, 'type' => 4]) ?>
    <div class = 'box shadow'>
        <?php
        $nameArr = [];
        if ($status == 4) {
            $nameArr = [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
                'headerOptions' => ['class' => 'col-sm-1'],
            ];
        } else {
            $nameArr = [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'col-sm-1'],
                'header' => '序号'
            ];
        }
        ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive', 'id' => 'grid'],
            'tableOptions' => ['class' => 'table table-hover cure-form'],
            'headerRowOptions' => ['class' => 'header'],
            'layout' => '{items}',
            'rowOptions' => function ($model) {
                return ['class' => 'recipe-top skin_test_' . $model->id];
            },
            'columns' => [
                $nameArr,
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function($model) {
                        $recipeName =  Html::encode($model->name);
                        $specification =  Html::encode($model->specification);
                        if($model->high_risk == 1){
                            $recipeName = '<span class="high-risk">高危</span>'.$recipeName;
                        }
                        if ($specification != '') {
                            return $recipeName . '（' . $specification . '）';
                        }
                        return $recipeName;
                    },
                    'headerOptions' => ['class' => 'col-sm-1'],
                ],
                [
                    'attribute' => 'dosage_form',
                    'headerOptions' => ['class' => 'col-sm-1'],
                    'value' => function ($model) {
                        return RecipeList::$getType[$model->dosage_form];
                    }
                ],
                [
                    'attribute'=>'usage',
                    'headerOptions'=>['class'=>'col-sm-2'],
                    'value'=>function($model){
                        $usage='';
                        $usage.=$model->dose;
                        $usage.=RecipeList::$getDoseUnit[$model->dose_unit].';';
                        $usage.=RecipeList::$getDefaultUsed[$model->used].';';
                        $usage.=RecipeList::$getDefaultConsumption[$model->frequency];
                        return $usage;
                    }
                ],
                [
                    'attribute' => 'day',
                    'headerOptions' => ['class' => 'col-sm-1'],
                ],
                [
                    'attribute' => 'num',
                    'headerOptions' => ['class' => 'col-sm-1'],
                    'value' => function ($model) {
                        return $model->num . RecipeList::$getUnit[$model->unit];
                    }
                ],
                [
                    'attribute' => 'price',
                    'headerOptions' => ['class' => 'col-sm-1'],
                    'value' => function($model){
                        return Common::num($model->price * $model->num);
                    }
                ],
                [
                    'attribute' => 'description',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        $html = Html::encode($model->description);
                        return $html;
                    }
                ],
                [
                    'attribute' => 'remark',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'value' => function ($model)use($status) {
                        $html = Html::encode($model->remark);
                        $hiddenHtml = "<input type='hidden' name='id[]' class='checkitemid' value='$model->id'>";
                        if ($status == 4) {
                            $text = Html::input('text', 'PharmacyRecord[remark]', $model->remark, ['class' => 'form-control']);
                            $text.=$hiddenHtml;
                        } else {
                            $text = '<p>' . $html . '</p>';
                            $text .= Html::input('text', 'PharmacyRecord[remark]', $model->remark, ['class' => 'form-control hid L-remark', 'size' => 100]);
                            $text .=$hiddenHtml;
                        }
                        return $text;
                    }
                ],
                [
                    'attribute' => 'type',
                    'headerOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model::$getType[$model->type] . Html::input('hidden', 'recipeOut', $model->type);
                    }
                ],
            ],
        ]);
        ?>
    </div>
    <div>
        <?php
        if ($status == 4) {
            $title = '确认退药';
            $class = "btn btn-default btn-form confirm-dispense";
            echo Html::button($title, [
                'class' => $class, 
                'data-url' => Url::to(['prebatch', 'id' => Yii::$app->request->get('id')]), 
                'data-confirm'=>false, 
                'data-method'=>false,
               
            ]);
        } else {
            $title = '修改';
            $class = "btn btn-default btn-form update-dispense";
            echo Html::button($title, ['class' => $class]);
        }
        ?>
        <?php if ($status == 4): ?>
            <?= Html::a('取消', Yii::$app->request->referrer, ['class' => 'btn btn-cancel btn-form']) ?>
        <?php endif; ?>
    </div>
    <?php ActiveForm::end() ?>
</div>



