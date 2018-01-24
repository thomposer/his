<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\modules\outpatient\models\ConsumablesRecord;

$chargeInfoList = $chargeInfoList?$chargeInfoList:array();
$this->params['chargeInfoList']['consumables'] = $chargeInfoList;
?>
<?php Pjax::begin([
    'id' => 'consumablesPjax',
    'timeout' => 5000,
])?>

<div class="consumables-record-index col-xs-12"> 
     <?php $form = ActiveForm::begin([
         'action' => Url::to(['@outpatientOutpatientConsumablesRecord','id' => Yii::$app->request->get('id')]),
         'id' => 'consumables-record',
          'options' => ['data' => ['pjax' => '#consumablesPjax']],
     ]) ?>
    <div class = 'consumables-record-form'>

        <?= $form->field($model, 'consumablesName')->dropDownList([],['class' => 'form-control select2','style' => 'width:100%']) ?>
    </div>
    <div class = 'box'>
        <?= GridView::widget([ 
            'dataProvider' => $consumablesRecordDataProvider, 
            'options' => ['class' => 'grid-view table-responsive'], 
            'tableOptions' => ['class' => 'table table-hover consumables-form'], 
            'headerRowOptions' => ['class' => 'header'],
            'layout'=> '{items}', 
            'columns' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-sm-3'],
                ],
                [
                    'attribute' => 'specification',  
                    'headerOptions' => ['class' => 'col-sm-2'],
                ],
                [

                    'attribute' => 'price',
                    'headerOptions' => ['class' => 'col-sm-1'],
                ],
                [
                    'attribute' => 'num',
                    'headerOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model){
                        if(!in_array($model->id, $this->params['chargeInfoList']['consumables'])){
                           $html = Html::tag('span',Html::encode($model->num));
                           $html .= Html::input('hidden','ConsumablesRecord[num][]',$model->num,['class'=>'form-control']);
                           $html .= Html::input('hidden','ConsumablesRecord[consumables_id][]',json_encode(array_merge(['id' => $model->id],['isNewRecord' => 0]),true));
                        }else{
                            $html = Html::encode($model->num);
                        }
                        
                        return $html;
                    }
                ],
                [
                    'attribute' => 'unit',
                    'headerOptions' => ['class' => 'col-sm-2'],
                ],
                [
                    'attribute' => 'remark',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'value' => function ($model){
                        
                        if(!in_array($model->id, $this->params['chargeInfoList']['consumables']) ){
                            $html = Html::tag('span',Html::encode($model->remark));
                            $html .= Html::input('hidden','ConsumablesRecord[remark][]',$model->remark,['class'=>'form-control']);
                        }else{
                            $html = Html::encode($model->remark);
                        }
                        return  $html;
                    }
                ],
                [
                    'attribute' => 'totalNum',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                    'value' => function($model)use($consumablesTotal) {
                        return ($consumablesTotal[$model->consumables_id] ? $consumablesTotal[$model->consumables_id] : 0);
                     }
                ],
                [ 
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{delete}',
                    'headerOptions' => ['class' => 'col-sm-1 action-column'],
                    'buttons' => [
                          'delete' => function($url,$model,$key){
                            $html = '';
                            if(!in_array($model->id, $this->params['chargeInfoList']['consumables'])  ){
                               $html = Html::hiddenInput('ConsumablesRecord[deleted][]').Html::img(Yii::$app->request->baseUrl.'/public/img/common/delete.png');
                            }
                            return $html;
                        } 
                    ]
                ], 
            ], 
        ]); ?> 
    </div>
    <div class="form-group">
        <?= Html::button('修改', ['class' => 'btn btn-default btn-form'])?>
        <?= Html::a('打印医疗耗材清单',Url::to(['@outpatientOutpatientConsumablesPrinkInfo', 'id' => Yii::$app->request->get('id')]), ['data-modal-size' => 'normal','class' => 'btn btn-default btn-form pull-right print-consumables' ,'role'=>'modal-remote','name'=>Yii::$app->request->get('id').'consumables-myshow' ]);?>
    </div>
    <?php ActiveForm::end()?>    
</div>

<div id ="consumables_print" class = "tab-pane hide">
</div>

<?php
$consumablesCount = $consumablesRecordDataProvider->query->count();
$consumablesTotal = json_encode($consumablesTotal,true);
$this->registerJs("
    var consumablesCount = $consumablesCount;
    var isconsumablesCommitted = false;//表单是否已经提交标识，默认为false
    var consumablesRecordUrl = $('a[href=\"#consumables\"]').attr('data-url');
    consumablesTotal = $consumablesTotal;
    $('.empty').parents('tr').remove();
    $('#consumables-record').yiiAjaxForm({
	   beforeSend: function() {
     
            if(isconsumablesCommitted == false){
               isconsumablesCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
               return true;//返回true让表单正常提交
            }else{
               return false;//返回false那么表单将不提交
            }		   
	   },
	   complete: function() {
			
	   },
	   success: function(data) {
            
    	   if(data.errorCode == 0){
    		    showInfo(data.msg,'180px');
                    consumablesTotal = data.data;//更新库存
                if(isconsumablesCommitted == true){
    			     $.pjax.reload({container:'#consumablesPjax',url:consumablesRecordUrl,push:false,replace:false,scrollTo:false,cache:false,timeout:5000});  //Reload
                }
    	   }else{
                isconsumablesCommitted = false;
    		    showInfo(data.msg,'180px',2);
            }
	},
});
    if(consumablesCount == 0){
         $('#consumables button').html('保存');
         $('.field-consumablesrecord-consumablesname').show();
         $('.consumables-form .action-column').show();
         $('#consumables .print-consumables').hide();
         setTimeout(function () {
                main.consumablesSelect2();
                $('#consumables button').attr({'type': 'submit'});
            }, 500);
     }    
")?>
<?php Pjax::end()?>
