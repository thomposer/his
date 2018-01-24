<?php

use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Html;
use rkit\yii2\plugins\ajaxform\Asset;
use app\common\Common;

Asset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;

?>

<div class="charge-record-form">
    <?php
    $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal'],
                'id' => 'create-discount-form'
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'grid-view table-responsive'],
                'tableOptions' => ['class' => 'table table-hover table-border'],
                'layout' => '{items}<div class="text-right">{pager}</div>',
                'id' => 'crud-datatable',
                'striped' => false,
                'columns' => [
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'value' => function($dataProvider){
                            return Html::encode($dataProvider['name']).$html = $dataProvider['is_charge_again']?'<span class="text-red-mine">（重收）</span>':'';
                        }
                    ],
                    [
                        'attribute' => 'total_price',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'contentOptions' => ['class' => 'totalPrice'],
                        'value' => function ($model) {
                            return Common::num($model->unit_price * $model->num);
                        }
                    ],
                    [
                        'attribute' => 'discount',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::input('text','discount[]',$model->discount,['class' => 'form-control discount-text','maxlength'=>"8"]);
                        }
                    ],
                    [
                        'attribute' => 'discount_price',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'contentOptions' => ['class' => 'discount_price'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::input('text','discount_price[]',$model->discount_price,['class' => 'form-control discountPrice','maxlength'=>"8"]);
                        }
                    ],
                    [
                        'attribute' => '折后金额（元）',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'contentOptions' => ['class' => 'discountPirceAfter'],
                        'value' => function($model){
                            return Common::num($model->unit_price * $model->num - $model->discount_price);
                        }
                    ],
                    [
                        'attribute' => 'discount_reason',
                        'headerOptions' => ['class' => 'col-sm-2'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            $html = Html::hiddenInput('chargeInfoId[]',$model->id);
                            $html.= Html::input('text', 'discount_reason[]', $model->discount_reason, ['class' => 'form-control discountReason','maxlength' => 20]);
                            return $html;
                        }
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
    <div class = 'modal-footer' id = "my-modal-footer">
        <div class = 'form-group'>
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal", 'id' => 'batch-myform-cancel']) ?>
            <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form batch-myform', 'id' => 'batch-myform']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
   require(["$baseUrl/public/js/charge/createDiscount.js"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
