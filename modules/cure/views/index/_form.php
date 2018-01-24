<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\grid\GridView;
use yii\helpers\Url;
use app\modules\outpatient\models\CureRecord;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cure-record-index col-xs-12">
    <?php
    $form = ActiveForm::begin([
        'id' => 'cure-record',
        'options' => ['data' => ['pjax' => true]],
    ])
    ?>
    <?= $this->render(Yii::getAlias('@orderFillerInfo'),['record_id'=>Yii::$app->request->get('id'),'type'=>3]) ?>
    <div class = 'box shadow'>
        <?php
        $nameArr = [];
        if ($status == 2) {
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
            'columns' => [
                $nameArr,
                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'col-sm-2'],
                ],
                [
                    'attribute' => 'unit',
                    'headerOptions' => ['class' => 'col-sm-1'],
                ],
                [
                    'attribute' => 'price',
                    'headerOptions' => ['class' => 'col-sm-1'],
                ],
                [
                    'attribute' => 'time',
                    'headerOptions' => ['class' => 'col-sm-1'],

                ],
                [
                    'attribute' => 'description',
                    'headerOptions' => ['class' => 'col-sm-2'],
                ],
                [
                    'attribute' => 'cure_result',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'value' => function ($model)use($status) {
                    //$status 值为1时 治疗结束  2为 治疗中
                    //type 值为1时 为皮试  0为 普通治疗
                        if($model->type == 1){
                            if($status == 1){
                                $result = Html::dropDownList('CureRecord[cure-result]', $model->cure_result , CureRecord::$getCureResult, ['class' => 'form-control cure_result L-cure-result', 'prompt' => '请选择','disabled' => 'disabled']);
                            }else{
                                $result = Html::dropDownList('CureRecord[cure-result]', $model->cure_result , CureRecord::$getCureResult, ['class' => 'form-control cure-result-status', 'prompt' => '请选择']);
                            }

                        }else if($model->type == 0){
                            if($status == 1){
                                $result = '<span>' . Html::encode($model-> cure_result) . '</span>';
                                $result .= Html::input('text','CureRecord[cure-result]', $model->cure_result,['class'=>'hid form-control input-cure-result','maxlength' => 10]);
                            }else{
                                $result = Html::input('text','CureRecord[cure-result]', $model->cure_result,['class'=>'form-control','maxlength' => 10]);
                            }

                        }
                        return $result;
                    }
                ],
                [
                    'attribute' => 'remark',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'value' => function ($model)use($status) {
                        $html = Html::encode($model->remark);
                        $hiddenHtml="<input type='hidden' name='id[]' class='checkitemid' value='$model->id'>";
                        if ($status == 2) {
                            $text = Html::input('text', 'CureRecord[remark]', $model->remark, ['class' => 'form-control']);
                            $text.=$hiddenHtml;
                        } else {
                            $text = '<span>' . $html . '</span>';
                            $text .= Html::input('text', 'CureRecord[remark]', $model->remark, ['class' => 'form-control hid L-remark']);
                            $text .=$hiddenHtml;
                        }
                        return $text;
                    }
                ],
//                [
//                    'attribute' => 'status',
//                    'headerOptions'=>['class' => 'col-sm-1'],
//                    'format' => 'raw',
//                    'value' => function($model){
//                        if($model->status != null){
//                            $html= Html::tag('i','',CureRecord::getChargeStatusOptions($model->status));
//                        }else{
//                            $html='';
//                        }
//                        return $html;
//                    }
//                ],

            ],
        ]);
        ?>
    </div>
    <div>
        <?php if($status==2){ ?>
            <?= Html::button('保存', ['class' => 'btn btn-default btn-form confirm-cure']) ?>
        <?php }else{ ?>
            <?= Html::button('修改', ['class' => 'btn btn-default btn-form update-cure']) ?>
            <?= Html::button('打印治疗单', ['class' => 'btn btn-default btn-form print-check' ,'name'=>Yii::$app->request->get('id').'cure-myshow' ]) ?>
        <?php } ?>
    </div>
    <?php ActiveForm::end() ?>
</div>


