<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
?>
<div class="package-cure-index"> 
    <div class = 'package-cure-form'>
        <?= $form->field($cureModel, 'cureName')->dropDownList(array(),['class' => 'form-control select2','style' => 'width:100%']) ?>
    </div>
    <div class = 'box'>
        <?= GridView::widget([ 
            'dataProvider' => $cureDataProvider, 
            'options' => ['class' => 'grid-view table-responsive'], 
            'tableOptions' => ['class' => 'table table-hover cure-form table-border'], 
            'headerRowOptions' => ['class' => 'header'],
            'layout'=> '{items}', 
            'columns' => [
                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'col-sm-3'],
                ],
                [
                    'attribute' => 'unit',
                    'headerOptions' => ['class' => 'col-sm-2'],
                ],
                [
                    'attribute' => 'time',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'value' => function ($model)use($disabledCure){
                        if(in_array($model->id, $disabledCure)){
                            return $model->time;
                        }
                        $html = Html::hiddenInput('OutpatientPackageCure[cure_id][]',$model->cure_id).Html::input('text','OutpatientPackageCure[time][]',$model->time,['class'=>'form-control']);
                        return $html;
                    }
                ],
                [
                    'attribute' => 'description',
                    'headerOptions' => ['class' => 'col-sm-3'],
                    'format' => 'raw',
                    'value' => function ($model)use($disabledCure){
                        if(in_array($model->id, $disabledCure)){
                            return $model->description;
                        }
                        $html = Html::input('text','OutpatientPackageCure[description][]',$model->description,['class'=>'form-control']);
                    
                        return  $html;
                    }
                ],
                [ 
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{delete}',
                    'headerOptions' => ['class' => 'col-sm-1 action-column'],
                    'buttons' => [
                          'delete' => function($url,$model,$key)use($disabledCure){
                            if(in_array($model->id, $disabledCure)){
                                  return '';
                            }
                            $html = Html::img(Yii::$app->request->baseUrl.'/public/img/common/delete.png');
                            return $html;
                        } 
                    ]
                ], 
            ], 
        ]); ?> 
    </div>
</div>