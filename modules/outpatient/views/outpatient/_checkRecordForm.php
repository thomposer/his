<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;
use yii\grid\GridView;
use app\modules\outpatient\models\CheckRecord;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CheckRecord */
/* @var $form ActiveForm */
$attributes = $model->attributeLabels();
$chargeInfoList = $chargeInfoList?$chargeInfoList:array();
$firstCheckWeightEdit = !$patientOtherInfo['firstCheckCount'];
$checkListJson = json_encode($checkList,true); 
?>
<div class="checkRecordForm col-xs-12">
<?php if($firstCheckWeightEdit): ?>
    <div class="first-check-info-tips" style="color: #55657d;font-weight: normal;padding-top: 20px;">
       <?php  if(isset($this->params['permList']['role'])||in_array(Yii::getAlias('@outpatientOutpatientUpdatePatientInfo'), $this->params['permList'])):?>
        填写初步诊断后，才能开影像学检查医嘱    
       <?= 
           Html::a("<i class='fa fa-plus' style=\"text-decoration-line: underline;\"></i> 请补充", Url::to(['@outpatientOutpatientUpdatePatientInfo', 'id' => Yii::$app->request->get('id'), 'type' => 1]),
               ['data-pjax' => 0, 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large', 'style' => "text-decoration:underline"]
               ) 
        ?>
       <?php endif?>
    </div>
<?php else: //如果没填写初步诊断，隐藏选择模版 ?>
    <div id ='check-template-select-ui'>
        <div class="check-template-select-container">
            <div id ="check-template-select" class="check-template-sel"></div>
            <div class="check-template-desc ">请选择检查模板<span class="glyphicon glyphicon-triangle-bottom check-template-arrow"></span></div>
        </div>
    </div>
<?php endif ?>
    <?php Pjax::begin([
        'id' => 'checkPjax',
        'timeout' => 5000,
        'enablePushState' => false
    ])?>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['@outpatientOutpatientCheckRecord','id' => Yii::$app->request->get('id')]),
        'id' => 'check-record',
        'options' => ['data' => ['pjax' => '#checkPjax']],
    ]); ?>
        
        <?php
        echo $form->field($model, 'checkName')->dropDownList([],['class' => 'form-control select2','style' => 'width:100%','disabled' => $firstCheckWeightEdit]); ?>
        <?php 
        echo GridView::widget([
            'dataProvider' => $checkRecordProvider, 
            'options' => ['class' => 'grid-view table-responsive'], 
            'tableOptions' => ['class' => 'table table-hover check-form'], 
            'headerRowOptions' => ['class' => 'header'],
            'rowOptions' => ['class' => 'check-list'],
            'layout'=> '{items}',
            'emptyCell' => '<span></span>',
            'columns' => [
                [
                    'attribute' => 'name',
                    'label' => '项目',
                    'headerOptions' => ['class' => 'col-sm-4'],
                ],
                [
                    'attribute' => 'price',
                    'label' => '零售价',
                    'headerOptions' => ['class'=>'col-sm-4'],
                    
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'label' => '状态',
                    'headerOptions' => ['class'=>'col-sm-4'],
                    'value' => function($model){
                        return  Html::tag('span',CheckRecord::$getStatus[$model->status], ['style' => 'color:'.CheckRecord::getStatusColor($model->status)]);
                    }
                ],
                [ 
                    'class' => 'app\common\component\ActionColumn',
                    'template' => '{delete}',
                    'headerOptions' => ['class' => 'col-sm-4 action-column'],
                    'buttons' => [
                          'delete' => function($url,$model,$key)use($chargeInfoList){
                            $html = '';
                            $list = [//需要提交的数据
                                'id' => $model->id,
                                'name' => $model->name,
                                'unit' => $model->unit,
                                'price' => $model->price,
                                'status' => $model->status,
                                'package_record_id' => $model->package_record_id,
                            ];
                            if(!in_array($model->id,$chargeInfoList) && $model->status == 3 && $model->package_record_id == 0){
                                $hideDelete = Html::hiddenInput('CheckRecord[deleted][]');
                                $subData = Html::hiddenInput('CheckRecord[check_id][]',Json::encode(array_merge($list, ['isNewRecord' => 0]), JSON_ERROR_NONE));
                                $deleteImg = Html::img(Yii::$app->request->baseUrl.'/public/img/common/delete.png');
                                $html = $hideDelete.$subData.$deleteImg;
                            }
                            return $html;
                        } 
                    ]
                ],
            ]
        ]);
        ?>
    
        <div class="form-group">
            <?= Html::button('修改', ['class' => 'btn btn-default btn-form btn-disalbed-custom','disabled' => $firstCheckWeightEdit])?>
            <span class="btn-check-application">
            <?= Html::a('打印申请单',Url::to(['@apiOutpatientCheckApplication', 'id' => Yii::$app->request->get('id')]),  ['data-modal-size' => 'normal', 'role'=>'modal-remote','class' => 'btn btn-default btn-form  print-check', 'name' =>Yii::$app->request->get('id') . 'check-application-myshow']); ?>
        </span>
        </div>
    <?php ActiveForm::end(); ?>

    <div id='check-application-print' class="tab-pane hide">
    </div>

</div><!-- _checkRecordForm -->
<?php 
$checkCount = $checkRecordProvider->count;
$this->registerJs("
    var checkCount = $checkCount;
    var isCheckCommitted = false;//表单是否已经提交标识，默认为false
    var checkRecordUrl = $('a[href=\"#auxiliary\"]').attr('data-url');
    checkTemplateMenu = $checkTemplateMenu;
    $('.check-template-desc').click();
    $('#check-record').yiiAjaxForm({
	   beforeSend: function() {
			if(isCheckCommitted == false){
               isCheckCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
               return true;//返回true让表单正常提交
            }else{
               return false;//返回false那么表单将不提交
            }		   			
	   },
	   complete: function() {
			
	   },
	   success: function(data) {

    	   if(data.errorCode == 0){
    	   $('#check-template-select-ui').hide();
    		    showInfo(data.msg,'180px');
                if(isCheckCommitted == true){
			         $.pjax.reload({container:'#checkPjax',url:checkRecordUrl,cache:false,push:false,replace:false,scrollTo:false,timeout: 5000});  //Reload
                }		   
    	   }else{
                isCheckCommitted = false
    		    showInfo(data.msg,'180px',2);
            }
	},
});
    if(checkCount == 0){
         $('.check-form .empty').parents('tr').remove();  
         $('#auxiliary button').html('保存');
         $('.field-checkrecord-checkname').show();
          $('#check-template-select-ui').show();
          $('#check-template-select').hide();
         setTimeout(function () {
                main.checkSelect2();
                $('#auxiliary button').attr({'type': 'submit'});
            }, 500);

         $('.btn-check-application').html('');
     }        
")?>
<?php Pjax::end() ?>