<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\common\Common;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\assets\AppAsset;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\spot\models\RecipeList;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;

?>

<div class="recipe-back-form">

    <?php $form = ActiveForm::begin([
        'id' => 'recipe-check',
        'options' => ['data' => ['pjax' => true]],
    ]); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive', 'id' => 'grid'],
        'tableOptions' => ['class' => 'table table-hover cure-form'],
        'headerRowOptions' => ['class' => 'header'],
        'layout' => '{items}',
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'col-sm-1'],
                'checkboxOptions' => function ($dataProvider) {
                    return ['data-type' => $dataProvider['type'],'class' => 'recipe-check'];
                },
            ],
            [
                'attribute' => 'name',
                'headerOptions' => ['class' => 'col-sm-3'],
                'value' => function($dataProvider){
                    if($dataProvider->high_risk == 1){
                        return '<span class="high-risk">高危</span>'.Html::encode($dataProvider->name).'('.Html::encode($dataProvider->specification).')';
                    }else{
                        return Html::encode($dataProvider->name).'('.Html::encode($dataProvider->specification).')';
                    }

                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'dosage_form',
                'headerOptions' => ['class' => 'col-sm-1'],
                'value' => function($dataProvider){
                    return RecipeList::$getType[$dataProvider->dosage_form];
                }
            ],
            [
                'attribute' => 'dose',
                'headerOptions' => ['class' => 'col-sm-1'],
                'value' => function($dataProvider){
                    return $dataProvider->dose.RecipeList::$getDoseUnit[$dataProvider->dose_unit];
                }
            ],
            [
                'attribute' => '发药数量',
                'headerOptions' => ['class' => 'col-sm-1'],
                'value' => function($dataProvider){
                    return $dataProvider->num;
                }

            ],
            [
                'attribute' => '退药数量',
                'headerOptions' => ['class' => 'col-sm-1'],
                'value' => function($dataProvider){
                    return $dataProvider->num;
                }

            ],

        ],
    ]);
    ?>
    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs("
      function checkBtnState() {
            if ($('.recipe-check:checked').length > 0) {
                $('.btn-recipe-back').attr('disabled', false);
                $('.btn-recipe-back').attr('style', 'background:#76A6EF');
            } else {
                $('.btn-recipe-back').attr('disabled', true);
                $('.btn-recipe-back').attr('style', 'background:#BAD2F7');
            }
            
      }
      function isCheck() { // 判断check框是否要选中

            var flag = 0;
            $('input:checkbox[name=\"selection[]\"]').each(function (index) {
                var value = $(this).attr('checked');
                if (value != 'checked') {
                    flag++;
                    return false;
                }
            });
            console.log(flag);
            if (flag > 0) {
                $('.select-on-check-all').attr({'checked': false});
            } else {
                $('.select-on-check-all').attr({'checked': true});
            }
      }
      $('.select-on-check-all').on('click', function () {
          setTimeout('checkBtnState()', 100);
           var value = $(this).attr('checked');
                if (value == 'checked') {
                    $('input:checkbox[name=\"selection[]\"]').attr('checked', true);
                } else {
                    $('input:checkbox[name=\"selection[]\"]').removeAttr('checked');
                }
            }).click(); 
        $('input:checkbox[name=\"selection[]\"]').on('click', function () {
            
            checkBtnState();
            isCheck();
        });

",\yii\web\View::POS_END)?>




