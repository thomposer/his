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
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$disabledClass = 1;
if (empty($batchDataProvider->getModels())) {
    $disabledClass = 2;
}
Yii::$app->params['totalRecipeList'] = $totalRecipeList;
?>

<div class="charge-record-form">
    <?php
    $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'id' => 'batch-form'
    ]);
    ?>
    <div class='row'>
        <?= Html::hiddenInput('remark', $remark) ?>
        <?= Html::hiddenInput('recipe_record', $recipe_record) ?>
        <?= Html::hiddenInput('idArr', $idArr) ?>
        <div class='col-md-12'>
            <?=
            GridView::widget([
                'dataProvider' => $batchDataProvider,
                'options' => ['class' => 'grid-view table-responsive'],
                'tableOptions' => ['class' => 'table table-hover'],
                'layout' => '{items}<div class="text-left">{pager}</div>',
                'id' => 'crud-datatable',
                'emptyText' => '该药品库存数量为零',
                'striped' => false,
                'columns' => [
                    [
                        'attribute' => 'recipe_name',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            $model->recipe_name = Html::encode($model->recipe_name);
                            if($model->high_risk == 1){
                                $model->recipe_name = '<span class="high-risk">高危</span>'.$model->recipe_name;
                            }
                            return $model->recipe_name. '(' . $model->num . ')' . '<span class="hide_id">' . $model->pr_id . '</span>';
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
                        'attribute' => 'num',
                        'headerOptions' => ['class' => 'col-sm-1'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            $batch_id = $model->batch_id ? $model->batch_id : 0;
                            $html = "<input name='batch_id[]' value={$batch_id}  type='hidden' />";
                            $html .= "<input name='recipe_record_id[]' value={$model->recipe_record_id}  type='hidden' />";
                            $html .= "<input name='storage_limit[$batch_id]' value={$model->storage}  type='hidden' />";
                            $needNum = 0;
                            $totalRecipeList = Yii::$app->params['totalRecipeList'];
                            if($totalRecipeList[$model->recipe_record_id] > 0 && $totalRecipeList[$model->recipe_record_id] <= $model->storage){
                                $needNum = $totalRecipeList[$model->recipe_record_id];
                                Yii::$app->params['totalRecipeList'][$model->recipe_record_id] = 0;
                            }else if($totalRecipeList[$model->recipe_record_id] > 0){
                                $needNum = $model->storage;
                                Yii::$app->params['totalRecipeList'][$model->recipe_record_id] -= $model->storage;
                            }
                            
                            $html .= Html::input('text', 'PharmacyRecord[need_num][]',$needNum, ['class' => 'form-control L-remark']);
                            return $html;
                        }
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
    <div class='modal-footer' id="my-modal-footer">
        <div class='form-group'>
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal", 'id' => 'batch-myform-cancel']) ?>
            <?php if ($disabledClass == 1): ?>
                <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form batch-myform','id' => 'batch-myform']) ?>
                <?= Html::submitButton('保存并打印标签', ['class' => 'btn btn-default btn-form batch-myform-print', 'id' => 'batch-myform']) ?>
            <?php endif; ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
   require(["$baseUrl/public/js/pharmacy/batch.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
