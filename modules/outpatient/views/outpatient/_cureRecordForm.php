<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Json;
use app\modules\outpatient\models\CureRecord;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CureRecord */
/* @var $form ActiveForm  */
$chargeInfoList = $chargeInfoList?$chargeInfoList:array();
$this->params['chargeInfoList']['cure'] = $chargeInfoList;
$firstCheckWeightEdit = !$patientOtherInfo['firstCheckCount'];
?>
<div class="cure-record-index col-xs-12"> 
<?php if($firstCheckWeightEdit): ?>
    <div class="first-check-info-tips" style="color: #55657d;font-weight: normal;padding-top: 20px;">
       <?php  if(isset($this->params['permList']['role'])||in_array(Yii::getAlias('@outpatientOutpatientUpdatePatientInfo'), $this->params['permList'])):?>
        填写初步诊断后，才能开治疗医嘱    
       <?= 
           Html::a("<i class='fa fa-plus' style=\"text-decoration-line: underline;\"></i> 请补充", Url::to(['@outpatientOutpatientUpdatePatientInfo', 'id' => Yii::$app->request->get('id'), 'type' => 1]),
               ['data-pjax' => 0, 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large', 'style' => "text-decoration:underline"]
               ) 
        ?>
       <?php endif?>
    </div>
<?php else: //如果没填写初步诊断或体重，隐藏选择模版 ?>
    <div id ='cure-template-select-ui'>
        <div class="cure-template-select-container">
            <div id ="cure-template-select" class="cure-template-sel"></div>
            <div class="cure-template-desc ">请选择治疗模板<span class="glyphicon glyphicon-triangle-bottom cure-template-arrow"></span></div>
        </div>
    </div>
<?php endif ?>    
    <?php Pjax::begin([
    'id' => 'curePjax',
    'timeout' => 5000,
    ])?>
     <?php $form = ActiveForm::begin([
         'action' => Url::to(['@outpatientOutpatientCureRecord','id' => Yii::$app->request->get('id')]),
         'id' => 'cure-record',
          'options' => ['data' => ['pjax' => '#curePjax']],
     ]) ?>
    <div class = 'cure-record-form'>

        <?= $form->field($model, 'cureName')->dropDownList([],['class' => 'form-control select2','style' => 'width:100%', 'disabled' => $firstCheckWeightEdit]) ?>
    </div>
    <div class = 'box'>
        <?= GridView::widget([ 
            'dataProvider' => $cureRecordDataProvider, 
            'options' => ['class' => 'grid-view table-responsive'], 
            'tableOptions' => ['class' => 'table table-hover cure-form'], 
            'headerRowOptions' => ['class' => 'header'],
            'layout'=> '{items}', 
            'columns' => [
                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'col-sm-3'],
                ],
                [
                    'attribute'=>'price',
                    'headerOptions'=>['class'=>'col-sm-2'],
                ],
                [
                    'attribute' => 'unit',
                    'headerOptions' => ['class' => 'col-sm-2'],
                ],
                [
                    'attribute' => 'time',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'value' => function ($model){
                       
                        if(!in_array($model->id, $this->params['chargeInfoList']['cure']) && $model->status == 3 && ($model->charge_status == 0 || $model->charge_status == null)  && $model->package_record_id == 0){
                            if($model->type == 0){
                                $html = Html::tag('span',Html::encode($model->time));
                                $list = [
                                    'id' => $model->id,
                                    'name' => $model->name,
                                    'unit' => $model->unit,
                                    'price' => $model->price
                                ];
                                $html .= Html::input('hidden','CureRecord[time][]',$model->time,['class'=>'form-control']);
                                $html .= Html::input('hidden','CureRecord[cure_id][]',Json::encode(array_merge($list,['isNewRecord' => 0]),JSON_ERROR_NONE));
                            }else{
                                $html = Html::encode($model->time);
                                $list = [
                                    'id' => $model->id,
                                    'name' => $model->name,
                                    'unit' => $model->unit,
                                    'price' => $model->price
                                ];
                                $html .= Html::hiddenInput('CureRecord[time][]',$model->time);
                                $html .= Html::hiddenInput('CureRecord[cure_id][]',Json::encode(array_merge($list,['isNewRecord' => 0]),JSON_ERROR_NONE));
                            }
                        }else{
                            $html = Html::encode($model->time);
                        }
                        
                        return $html;
                    }
                ],
                [
                    'attribute' => 'description',
                    'headerOptions' => ['class' => 'col-sm-3'],
                    'format' => 'raw',
                    'value' => function ($model){
                        
                        if(!in_array($model->id, $this->params['chargeInfoList']['cure']) && $model->status == 3 && ($model->charge_status == 0 || $model->charge_status == null) && $model->package_record_id == 0){
                            $html = Html::tag('span',Html::encode($model->description));
                            $html .= Html::input('hidden','CureRecord[description][]',$model->description,['class'=>'form-control']);
                        }else{
                            $html = Html::encode($model->description);
                        }
                        return  $html;
                    }
                ],
                [

                    'attribute' => 'status',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'col-sm-1 status-column'],
                    'contentOptions'=> ['class'=>'status-column '],
                    'value' => function($model){
                        $html = Html::tag('div',CureRecord::$getStatus[$model->status], ['style' => 'color:'.CureRecord::getStatusColor($model->status)]);
                        //收费状态显示
                        // if($model->charge_status != null){
                        //     $html .= Html::tag('i','',CureRecord::getChargeStatusOptions($model->charge_status));
                        // }
                        return $html;
                    }
                ],
                [ 
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{delete}',
                    'headerOptions' => ['class' => 'col-sm-1 action-column'],
                    'buttons' => [
                          'delete' => function($url,$model,$key){
                            $html = '';
                            if(!in_array($model->id, $this->params['chargeInfoList']['cure']) && $model->status == 3 && ($model->charge_status == 0 || $model->charge_status == null)  && $model->package_record_id == 0){
                                if($model->type == 0){
                                    $html = Html::hiddenInput('CureRecord[deleted][]').Html::img(Yii::$app->request->baseUrl.'/public/img/common/delete.png');
                                }else{
                                    $html = Html::hiddenInput('CureRecord[deleted][]',0);
                                }
                            }
                            return $html;
                        } 
                    ]
                ], 
            ], 
        ]); ?> 
    </div>
    <div class="form-group">
        <?= Html::button('修改', ['class' => 'btn btn-default btn-form btn-disalbed-custom','disabled' => $firstCheckWeightEdit])?>

        <?= Html::a('打印治疗单',Url::to(['@apiOutpatientCureApplication', 'id' => Yii::$app->request->get('id')]),  ['data-modal-size' => 'normal', 'role'=>'modal-remote','class' => 'btn btn-default btn-form  print-check', 'name' =>Yii::$app->request->get('id') . 'cure-application-myshow']); ?>
    </div>
    <?php ActiveForm::end()?>    
</div>

<div id ="cure_print" class = "tab-pane hide">
</div>

<?php
$cureCount = $cureRecordDataProvider->query->count();
$this->registerJs("
    var cureCount = $cureCount;
    var isCureCommitted = false;//表单是否已经提交标识，默认为false
    $('.empty').parents('tr').remove();
    cureTemplateMenu = $cureTemplateMenu;
    $('.cure-template-desc').click();

    $('#cure-record').yiiAjaxForm({
	   beforeSend: function() {
     
            if(isCureCommitted == false){
               isCureCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
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
                    $('#cure-template-select-ui').hide();
                if(isCureCommitted == true){
    			     $.pjax.reload({container:'#curePjax',url:cureRecordUrl,push:false,replace:false,scrollTo:false,cache:false,timeout:5000});  //Reload
                }
    	   }else{
                isCureCommitted = false;
    		    showInfo(data.msg,'180px',2);
            }
	},
});
    if(cureCount == 0){
                
         $('#cure button').html('保存');
         $('.cure-record-form').show();
         $('.cure-form .action-column').show();
         $('.cure-form .status-column').show();
         $('#cure .print-check').hide();
         $('#cure-template-select-ui').show();
         $('#cure-template-select').hide();
         setTimeout(function () {
                main.cureSelect2();
                $('#cure button').attr({'type': 'submit'});
            }, 500);
     }else{
        $('#cure-template-select-ui').hide();
     }  
")?>
<?php Pjax::end()?>
