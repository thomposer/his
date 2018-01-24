<?php

use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Html;
use rkit\yii2\plugins\ajaxform\Asset;
use app\modules\spot\models\RecipeList;

Asset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>

<div class="charge-record-form">
    
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?=
            GridView::widget([
                'dataProvider' => $expireDataProvider,
                'options' => ['class' => 'grid-view table-responsive'],
                'tableOptions' => ['class' => 'table table-hover'],
                'layout' => '{items}<div class="text-left">{pager}</div>',
                'id' => 'crud-datatable',
                'striped' => false,
                'columns' => [
                    [
                        'attribute' => 'recipe_name',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::encode($model->recipe_name) . '(' . $model->num . ')' . '<span class="hide_id">' . $model->pr_id . '</span>';
                        },
                        'group' => true, // enable grouping
                    ],
                    [
                        'attribute' => 'specification',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'value' => function ($model) {
                            return $model->specification;
                        }
                    ],
                    [
                        'attribute' => 'expire_time',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'value' => function ($model) {
                            return date("Y-m-d", $model->expire_time);
                        }
                    ],
                    [
                        'attribute' => 'storage',
                        'headerOptions' => ['class' => 'col-sm-1'],
                    ],
                    [
                        'attribute' => 'batch_number',
                        'headerOptions' => ['class' => 'col-sm-1'],
                    ],
                    [
                        'attribute' => 'unit',
                        'headerOptions' => ['class' => 'col-sm-1'],
                        'value' => function ($model) {
                            return RecipeList::$getUnit[$model->unit];
                        }
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
    <div class = 'modal-footer'>
        <p class="expire_description"><span class="_description">说明：</span>非常抱歉，以上药品库存数量为0或已过期，请联系医生对待发药品进行调整，方可继续操作，谢谢</p>
        <div class = 'form-group'>
            <?= Html::button('返回', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) ?>
        </div>
    </div>
</div>
<?php
$js = <<<JS
   require(["$baseUrl/public/js/pharmacy/batch.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
