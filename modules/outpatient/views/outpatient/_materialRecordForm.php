<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Json;
use app\modules\outpatient\models\MaterialRecord;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\MaterialRecord */
/* @var $form ActiveForm  */
$chargeInfoList = $chargeInfoList?$chargeInfoList:array();
$this->params['chargeInfoList']['material'] = $chargeInfoList;
?>
<?php Pjax::begin([
    'id' => 'materialPjax',
    'timeout' => 5000,
])?>

<div class="material-record-index col-xs-12"> 
     <?php $form = ActiveForm::begin([
         'action' => Url::to(['@outpatientOutpatientMaterialRecord','id' => Yii::$app->request->get('id')]),
         'id' => 'material-record',
          'options' => ['data' => ['pjax' => '#materialPjax']],
     ]) ?>
    <div class = 'material-record-form'>
        <?= $form->field($model, 'materialName')->dropDownList([],['class' => 'form-control select2','style' => 'width:100%']) ?>
    </div>
    <div class = 'box'>
        <?= GridView::widget([ 
            'dataProvider' => $materialRecordDataProvider, 
            'options' => ['class' => 'grid-view table-responsive'], 
            'tableOptions' => ['class' => 'table table-hover material-form'], 
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
                    'headerOptions' => ['class' => 'col-sm-2'],
                ],
                [
                    'attribute' => 'num',
                    'headerOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model){
                        if(!in_array($model->id, $this->params['chargeInfoList']['material'])){
                           $html = Html::tag('span',Html::encode($model->num));
                           $html .= Html::input('hidden','MaterialRecord[num][]',$model->num,['class'=>'form-control']);
                           $html .= Html::input('hidden','MaterialRecord[material_id][]',json_encode(array_merge(['id' => $model->id],['isNewRecord' => 0]),true));
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
                        
                        if(!in_array($model->id, $this->params['chargeInfoList']['material']) ){
                            $html = Html::tag('span',Html::encode($model->remark));
                            $html .= Html::input('hidden','MaterialRecord[remark][]',$model->remark,['class'=>'form-control']);
                        }else{
                            $html = Html::encode($model->remark);
                        }
                        return  $html;
                    }
                ],

                [
                    'attribute' => 'totalNum',
                    'headerOptions' => ['class' => 'col-xs-1 col-sm-1 col-md-1'],
                    'value' => function($model)use($materialTotal) {
                        if($model->attribute == 2){
                            return ($materialTotal[$model->material_id] ? $materialTotal[$model->material_id] : 0);
                        }
                        return '--';
                     }
                ],
                [ 
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{delete}',
                    'headerOptions' => ['class' => 'col-sm-1 action-column'],
                    'buttons' => [
                          'delete' => function($url,$model,$key){
                            $html = '';
//                            if(!in_array($model->id, $this->params['chargeInfoList']['material']) && $model->status == 3 ){
                            if(!in_array($model->id, $this->params['chargeInfoList']['material'])  ){
                               $html = Html::hiddenInput('MaterialRecord[deleted][]').Html::img(Yii::$app->request->baseUrl.'/public/img/common/delete.png');
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
        <?= Html::a('打印其他清单',Url::to(['@outpatientOutpatientMaterialPrinkInfo', 'id' => Yii::$app->request->get('id')]), ['data-modal-size' => 'normal','class' => 'btn btn-default btn-form pull-right print-material' ,'role'=>'modal-remote','name'=>Yii::$app->request->get('id').'material-myshow' ]);?>
    </div>
    <?php ActiveForm::end()?>    
</div>

<div id ="material_print" class = "tab-pane hide">
</div>

<?php
$materialCount = $materialRecordDataProvider->query->count();
$materialTotal = json_encode($materialTotal,true);
$this->registerJs("
    var materialCount = $materialCount;
    materialTotal = $materialTotal;
    var ismaterialCommitted = false;//表单是否已经提交标识，默认为false
    var materialRecordUrl = $('a[href=\"#material\"]').attr('data-url');

    $('.empty').parents('tr').remove();
    $('#material-record').yiiAjaxForm({
	   beforeSend: function() {
     
            if(ismaterialCommitted == false){
               ismaterialCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
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
                    materialTotal = data.data;//更新库存
                if(ismaterialCommitted == true){
    			     $.pjax.reload({container:'#materialPjax',url:materialRecordUrl,push:false,replace:false,scrollTo:false,cache:false,timeout:5000});  //Reload
                }
    	   }else{
                ismaterialCommitted = false;
    		    showInfo(data.msg,'180px',2);
            }
	},
});
    if(materialCount == 0){
                
         $('#material button').html('保存');
         $('.field-materialrecord-materialname').show();
         $('.material-form .action-column').show();
         $('#material .print-material').hide();
         setTimeout(function () {
                main.materialSelect2();
                $('#material button').attr({'type': 'submit'});
            }, 500);
     }    
")?>
<?php Pjax::end()?>
