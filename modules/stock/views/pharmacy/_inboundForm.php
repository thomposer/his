<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\modules\stock\models\Stock;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use rkit\yii2\plugins\ajaxform\Asset;
use app\modules\spot\models\RecipeList;

/* @var $this yii\web\View */
/* @var $model app\modules\pharmacy\models\Stock */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->getModel('stock')->attributeLabels();
Asset::register($this);
?>

<div class="stock-form col-md-12">

    <?php
    $form = ActiveForm::begin([
                'id' => 'inbound-form',
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-3'>
            <?=
            $form->field($model->getModel('stock'), 'inbound_time')->widget(
                    DatePicker::className(), [
                'inline' => false,
                'language' => 'zh-CN',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ])->label($attributeLabels['inbound_time'] . '<span class = "label-required">*</span>')
            ?>
        </div>
        <div class = 'col-md-3'>
            <?= $form->field($model->getModel('stock'), 'inbound_type')->dropDownList(Stock::$getInboundType, ['prompt' => '请选择入库方式'])->label($attributeLabels['inbound_type'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-3'>
            <?= $form->field($model->getModel('stock'), 'supplier_id')->dropDownList(ArrayHelper::map($supplierConfig, 'id', 'name'), ['prompt' => '请选择供应商']) ?>
        </div>
        <div class = 'col-md-3'>
            <?= $form->field($model->getModel('stock'), 'remark')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'box'>
        <?= $form->field($model->getModel('stockInfo'), 'recipeName')->dropDownList(ArrayHelper::map($recipeList, 'id', 'name'), ['prompt' => '请选择', 'class' => 'form-control select2', 'style' => 'width:100%'])->label(false) ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => ['class' => 'table table-hover table-border stock-info'],
            'headerRowOptions' => ['class' => 'header'],
            'layout' => '{items}',
            'columns' => [
                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2']
                ],
                [
                    'attribute' => 'specification',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1']
                ],
                [
                    'attribute' => 'manufactor',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1']
                ],
                [
                    'attribute' => 'total_num',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                    'value' => function ($dataProvider) {
                return Html::input('text', 'StockInfo[total_num][]', $dataProvider->total_num, ['class' => 'form-control stock-inbound-focusout total_num-focusout']);
            }
                ],
                [
                    'attribute' => 'unit',
                    'value' => function($dataProvider) {
                        return RecipeList::$getUnit[$dataProvider->unit];
                    },
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1']
                ],
                [
                    'attribute'=>'invoice_number',
                    'format'=>'raw',
                    'headerOptions'=>['class'=>'col-xs-1 col-sm-1 col-md-2'],
                    'value'=>function($dataProvider){
                        return Html::input('text','StockInfo[invoice_number][]',$dataProvider->invoice_number,['class'=>'form-control']);
                    }
                ],
//                 [
//                     'attribute' => 'price',
//                     'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1']
//                 ],
                [
                    'attribute' => 'default_price',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                    'value' => function($dataProvider) {
                return Html::input('text', 'StockInfo[default_price][]', $dataProvider->default_price, ['class' => 'form-control stock-inbound-focusout default_price-focusout']);
            }
                ],
                [
                    'attribute' => 'batch_number',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                    'value' => function($dataProvider) {
                return Html::input('text', 'StockInfo[batch_number][]', $dataProvider->batch_number, ['class' => 'form-control']);
            }
                ],
                [
                    'attribute' => 'expire_time',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2'],
                    'value' => function($dataProvider) {
                return '<div class="date">' . Html::input('text', 'StockInfo[expire_time][]', date('Y-m-d', $dataProvider->expire_time), ['class' => 'form-control']) . '</div>';
            }
                ],
                [
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{delete}',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1 action-column'],
                    'contentOptions' => ['class' => "op-group", 'style' => 'display:table-cell'],
                    'buttons' => [
                        'delete' => function($url, $model, $key) {
                            $html = '';
                            $html .= Html::hiddenInput('StockInfo[stockInfoId][]', $model->id);
                            $html .= Html::hiddenInput('StockInfo[recipe_id][]', $model->recipe_id);
                            $html .= Html::hiddenInput('StockInfo[deleted][]','',['class'=>'stock-inbound-delete']) . Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png');
                            return $html;
                        }
                    ]
                ],
            ],
        ]);
        ?>
        <div class="stock-total">
            <span class="stock-total-pre">成本合计： <span class="stock-total-pre-num"></span></span>
        </div>
    </div>
    <div>
        <?= Html::a('取消', Yii::$app->request->referrer, ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form ajaxform-btn']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
