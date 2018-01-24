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
if(!empty($consumablesList)){
    foreach ($consumablesList as $key => &$value) {
        $value['name'] = $value['product_number'].' '. $value['name'].' '.'（'.$value['specification'].'）';
    }
}
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
        <?= $form->field($model->getModel('outboundInfo'), 'consumablesName')->dropDownList(ArrayHelper::map($consumablesList, 'id', 'name'),['prompt' => '请选择','class' => 'form-control select2','style' => 'width:100%'])->label(false) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => ['class' => 'table table-hover table-border outbound-info'],
            'headerRowOptions' => ['class' => 'header'],
            'layout'=> '{items}',
            'columns' => [
                'product_number',
                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'col-xs-2 col-sm-2 col-md-2']
                ],
                [
                    'attribute' => 'specification',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1']
                ],
                [
                    'attribute' => 'unit',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1']
                ],
                [
                    'attribute' => 'manufactor',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1']
                ],
                [
                    'attribute' => 'default_price',
                    'contentOptions' => ['class' => 'default_price']
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
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                    'value' => function ($dataProvider) {
                        return Html::input('text', 'ConsumablesOutboundInfo[num][]', $dataProvider->num, ['class' => 'form-control']);
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
                            $html .= Html::hiddenInput('ConsumablesOutboundInfo[outboundInfoId][]',$model->id);
                            $html .= Html::hiddenInput('ConsumablesOutboundInfo[consumables_stock_info_id][]',$model->consumables_stock_info_id,['class' => 'stock_info_id']);
                            $html .= Html::hiddenInput('ConsumablesOutboundInfo[deleted][]').Html::img(Yii::$app->request->baseUrl.'/public/img/common/delete.png');
                            return $html;
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
    <div class="form-group">
        <?= Html::a('取消',['@stockIndexConsumablesOutboundIndex'],['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form ajaxform-btn']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
