<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use rkit\yii2\plugins\ajaxform\Asset;
use app\modules\stock\models\Outbound;
use app\modules\spot\models\RecipeList;
/* @var $this yii\web\View */
/* @var $model app\modules\pharmacy\models\Stock */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->getModel('outbound')->attributeLabels();
Asset::register($this);
$this->params['recipeList'] = $recipeList;
?>

<div class="stock-form col-md-12">

    <?php $form = ActiveForm::begin([
        'id' => 'outbound-form',
    ]); ?>
    <div class = 'row'>
    <div class = 'col-md-3'>
    <?= $form->field($model->getModel('outbound'), 'outbound_time')->widget(
        DatePicker::className(),[
            'inline' => false,
            'language' => 'zh-CN',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ]
        ])->label($attributeLabels['outbound_time'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-md-2'>
    <?= $form->field($model->getModel('outbound'), 'outbound_type')->dropDownList(Outbound::$getOutboundType,['prompt' => '请选择'.$attributeLabels['outbound_type']])->label($attributeLabels['outbound_type'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-md-2'>
    <?= $form->field($model->getModel('outbound'), 'leading_department_id')->dropDownList(ArrayHelper::map($departmentList, 'id', 'name'),['prompt' => '请选择'.$attributeLabels['leading_department_id']]) ?>
    </div>
    <div class = 'col-md-2'>
    <?= $form->field($model->getModel('outbound'), 'leading_user_id')->dropDownList(ArrayHelper::map($userList, 'id', 'username'),['prompt' => '请选择领用人']) ?>
    </div>
    <div class = 'col-md-3'>
    <?= $form->field($model->getModel('outbound'), 'remark')->textInput(['maxlength' => true]) ?>
    </div>
    </div>
    <div class = 'box'>
        <?= $form->field($model->getModel('outboundInfo'), 'recipeName')->dropDownList(ArrayHelper::map($recipeList, 'recipe_id', 'name'),['prompt' => '请选择','class' => 'form-control select2','style' => 'width:100%'])->label(false) ?>
        <?= GridView::widget([ 
            'dataProvider' => $dataProvider, 
            'options' => ['class' => 'grid-view table-responsive'], 
            'tableOptions' => ['class' => 'table table-hover table-border outbound-info'], 
            'headerRowOptions' => ['class' => 'header'],
            'layout'=> '{items}', 
            'columns' => [
                'name',
                'specification',
                'manufactor',
//                 'price',
                [
                    'attribute' => 'default_price',
                    'contentOptions' => ['class' => 'default_price']
                ],
                [
                    'attribute' => 'batch_number',
                    'headerOptions' => ['style' => 'width:11%'],
                    'format' => 'raw',
                    'value' => function($dataProvider){
                        $batch_number = $this->params['recipeList'][$dataProvider->recipe_id]['batch_number'];
                        
                        return Html::hiddenInput('OutboundInfo[recipe_id][]',$dataProvider->recipe_id,['class' => 'recipe_id']).Html::dropDownList('OutboundInfo[batch_number][]',$dataProvider->stock_info_id,ArrayHelper::map($batch_number, 'id', 'batch_number'),['prompt' => '请选择','class' => 'form-control outboundinfo-batch_number']);
                        
                    }
                ],
                
                [
                    'attribute' => 'expire_time',
                    'format' => 'date',
                    'contentOptions' => ['class' => 'expire_time']
                ],
                [
                    'attribute' => 'inbound_num',
                    'contentOptions' => ['class' => 'num'],
                ],  
                [
                    'attribute' => 'num',
                    'format' => 'raw',
                    'value' => function ($dataProvider){
                        return Html::input('text','OutboundInfo[num][]',$dataProvider->num,['class'=>'form-control']);
                    }
                ],
                [
                    'attribute' => 'unit',
                    'value' => function($dataProvider){
                        return RecipeList::$getUnit[$dataProvider->unit];
                    }
                ],
                [ 
                    'class' => 'app\common\component\ActionColumn', 
                    'template' => '{delete}',
                    'headerOptions' => ['class' => 'col-sm-1 action-column'],
                    'contentOptions' => ['class' => "op-group",'style' => 'display:table-cell'],
                    'buttons' => [
                          'delete' => function($url,$model,$key){
                            $html = '';
                            $html .= Html::hiddenInput('OutboundInfo[outboundInfoId][]',$model->id);
                            $html .= Html::hiddenInput('OutboundInfo[stock_info_id][]',$model->stock_info_id,['class' => 'stock_info_id']);
                            $html .= Html::hiddenInput('OutboundInfo[deleted][]').Html::img(Yii::$app->request->baseUrl.'/public/img/common/delete.png');
                            return $html;
                        } 
                    ]
                ], 
            ], 
        ]); ?> 
    </div>

    <div class="form-group">
        <?= Html::a('取消',['@pharmacyIndexOutboundIndex'],['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form ajaxform-btn']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
