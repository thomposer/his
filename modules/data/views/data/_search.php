<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datetimepicker\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\modules\data\models\search\DataSearch */
/* @var $form yii\widgets\ActiveForm */
$action = Yii::$app->controller->action->id;
?>
<?php $this->beginBlock('renderCss') ?>
<?php
$css = <<<CSS
    .datetimepicker table tr td.active.active {
        color: #55657d;
        background-color: #E5F2FF;!important;
        background: #E5F2FF;!important;
    }
    .datetimepicker table tr td.day:hover {
        color: #ffffff;!important;
        background: #76A6EF;!important;
        cursor: pointer;!important;
    }
CSS;
$this->registerCss($css);
?>
<?php $this->endBlock() ?>
<div class = 'row'>
    <div class="col-sm-12 col-md-12 tc chart-title">
        <span class="chart-detail-title">
            <?php
            if($dateBegin == $dateEnd){
                echo date("Y年m月d日",strtotime($dateBegin));
            }else{
                echo date("Y年m月d日",strtotime($dateBegin)).'—'. date("m月d日",strtotime($dateEnd));
            }

            ?>
            <?php
                if($action == 'index'){
                    echo "收费日报表汇总";
                }else if($action == 'recharge'){
                    echo "充值情况";
                }
            ?>

        </span>
    </div>
    <div class="col-sm-12 col-md-12">
            <div style="float: right">
                <?php $form = ActiveForm::begin([
                    'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true,'id'=>'searchForm'],
                    'fieldConfig' => [
                        'template' => "{input}",
                    ]
                ]); ?>
                <span class='search-default'>筛选：</span>
                <div style="width: 170px;float: left;margin-right: 5px">
                    <?=
                    $form->field($model, 'beginTime')->widget(
                        DateTimePicker::className(), [
                            'id' => 'chartDetailStartDate',
                            'name' => 'beginTime',//当没有设置model时和attribute时必须设置name
                            'language' => 'zh-CN',
                            'template' => '{input}{button}',
                            'value' => $_POST['DataSearch']['beginTime'],
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd',
                                'minView' => 2,
                                'pickerPosition' => 'bottom-left',
                            ],
                            'options' => [
                                'placeholder'=>'请选择开始日期',
                            ]
                        ]
                    );
                    ?>
                </div>
                <div style="width: 170px;float: left;margin-right: 5px">
                    <?=

                    $form->field($model, 'endTime')->widget(
                        DateTimePicker::className(), [
                            'id' => 'chartDetailEndDate',
                            'name' => 'endTime',//当没有设置model时和attribute时必须设置name
                            'language' => 'zh-CN',
                            'template' => '{input}{button}',
                            'value' => $_POST['DataSearch']['endTime'],
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd',
                                'minView' => 2,
                                'pickerPosition' => 'bottom-left',
                            ],
                            'options' => [
                                'placeholder'=>'请选择结束日期',
                            ]
                        ]
                    );
                    ?>
                </div>
                <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
            </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

