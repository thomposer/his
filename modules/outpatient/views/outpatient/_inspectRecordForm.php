<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;
use yii\grid\GridView;
use app\modules\outpatient\models\InspectRecord;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CheckRecord */
/* @var $form ActiveForm */
$attributes = $model->attributeLabels();
$chargeInfoList = $chargeInfoList ? $chargeInfoList : array();
$firstCheckWeightEdit = !$patientOtherInfo['firstCheckCount'];
?>

<div class="inspectRecordForm col-xs-12">
<?php if($firstCheckWeightEdit): ?>
    <div class="first-check-info-tips" style="color: #55657d;font-weight: normal;padding-top: 20px;">
       <?php  if(isset($this->params['permList']['role'])||in_array(Yii::getAlias('@outpatientOutpatientUpdatePatientInfo'), $this->params['permList'])):?>
        填写初步诊断后，才能开实验室检查医嘱    
       <?= 
           Html::a("<i class='fa fa-plus' style=\"text-decoration-line: underline;\"></i> 请补充", Url::to(['@outpatientOutpatientUpdatePatientInfo', 'id' => Yii::$app->request->get('id'), 'type' => 1]),
               ['data-pjax' => 0, 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large', 'style' => "text-decoration:underline"]
               ) 
        ?>
       <?php endif?>
    </div>
<?php else: //如果没填写初步诊断，隐藏选择模版 ?>
    <div id ='inspect-template-select-ui'>
        <div class="inspect-template-select-container">
            <div id ="inspect-template-select" class="inspect-template-sel"></div>
            <div class="inspect-template-desc">请选择检验模板<span class="glyphicon glyphicon-triangle-bottom inspect-template-arrow"></span></div>
        </div>
    </div>
<?php endif ?>
    <?php
    Pjax::begin([
        'id' => 'inspectPjax',
        'timeout' => 5000,
        'enablePushState' => false
    ])
    ?>
    <?php
    $form = ActiveForm::begin([
                'action' => Url::to(['@outpatientOutpatientInspectRecord', 'id' => Yii::$app->request->get('id')]),
                'id' => 'inspect-record',
                'options' => ['data' => ['pjax' => '#inspectPjax']],
    ]);
    ?>
    <?php 
    // 对实验室检查列表数据进行修饰
//     foreach ($inspectList as $key => &$value) {
//         if(!empty($value['phonetic'])){//拼音码不为空时
//             $value['phonetic'] = '-'.$value['phonetic'];
//         }
//         $value['name'] = $value['name'].$value['phonetic'].'('.$value['price'].'元)';
//     }
    echo $form->field($model, 'inspectName')->dropDownList([], ['class' => 'form-control select2', 'style' => 'width:100%', 'disabled' => $firstCheckWeightEdit]);
    ?>
    <div class="box">
        <div id="w1" class="grid-view table-responsive">
            <table class="table table-hover inspect-form">
                <thead>
                    <tr class="header">
                        <th class="col-sm-4">项目</th>
                        <th class="col-sm-4">零售价</th>
                        <th class="col-sm-4">状态</th>
                        <th class="col-sm-4 action-column">操作</th>
                    </tr>
                </thead>
                <tbody>
        <?php if ($inspectRecordDataProvider): ?>
            <?php foreach ($inspectRecordDataProvider as $v): ?>
            <?php
            $itmTitle = "";
            if ($v['inspectItem']) {
                foreach ($v['inspectItem'] as $itm) {
                    $itmTitle .='<p>' . $itm['name'];
                    $itmTitle .= $itm['english_name'] ? '(' . $itm['english_name'] . ')</p>' : '</p>';
                }
            }?>
            
                 <tr class="inspect-list">
                    <td>
                        <span title= "<?= $itmTitle ?>"  data-toggle="tooltip" data-html="true" data-placement="bottom"><?=$v['name'] ?></span>
                    </td>
                    <td><?= $v['price']?></td>
                     <td>
                         <?php
                                echo $html = Html::tag('div',InspectRecord::$getStatus[$v['status']], ['style' => 'color:'.InspectRecord::getStatusColor($v['status'])]);
                         ?>
                     </td>
                    <td class="op-group">
                        <?php if (!in_array($v['id'], $chargeInfoList) && $v['status'] == 3 && $v['package_record_id'] == 0): ?>
                        <input type="hidden" name="InspectRecord[deleted][]" value="">
                        <input type="hidden" name="InspectRecord[inspect_id][]" value='<?= Html::encode(Json::encode(array_merge($v, ['isNewRecord' => 0]), JSON_ERROR_NONE)) ?>'>
                        <img src="<?=Yii::$app->request->baseUrl?>/public/img/common/delete.png" alt="">
                        <?php endif; ?> 
                    </td>
                </tr>
    
            <?php endforeach; ?>
        <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>
    <div class="form-group">
        <?= Html::button('修改', ['class' => 'btn btn-default btn-form btn-disalbed-custom','disabled' => $firstCheckWeightEdit]) ?>
        <span>
             <?php if($inspectBackStatus == 1){
                 echo Html::a('取消执行',Url::to(['@outpatientOutpatientInspectBack', 'id' => Yii::$app->request->get('id')]),  [ 'role'=>'modal-remote','class' => 'btn btn-default btn-form inspect-back','data-modal-size' => 'large']);
             }
             ?>
        </span>
        <span class="btn-inspect-application">
            <?= Html::a('打印申请单', Url::to(['@apiOutpatientInspectApplication', 'id' => Yii::$app->request->get('id')]), ['data-modal-size' => 'normal', 'role' => 'modal-remote', 'class' => 'btn btn-default btn-form  print-check', 'name' => Yii::$app->request->get('id') . 'inspect-application-myshow']); ?>
        </span>
    </div>
    <?php ActiveForm::end(); ?>
    <div id='inspect-application-print' class="tab-pane hide">
    </div>


    <?php
    $inspectCount = count($inspectRecordDataProvider);
    $this->registerJs("
    var inspectCount = $inspectCount;
    var inspectRecordUrl = $('a[href=\"#labCheck\"]').attr('data-url');
    inspectTemplateMenu = $inspectTemplateMenu;
    var isInspectCommitted = false;//表单是否已经提交标识，默认为false
    $('.inspect-template-desc').click();
    $('#inspect-record').yiiAjaxForm({
	   beforeSend: function() {
	       if(isInspectCommitted == false){
               isInspectCommitted = true;//提交表单后，将表单是否已经提交标识设置为true
               return true;//返回true让表单正常提交
            }else{
               return false;//返回false那么表单将不提交
            }		   					
	   },
	   complete: function() {
			
	   },
	   success: function(data) {
       
    	   if(data.errorCode == 0){
               $('#inspect-template-select-ui').hide();
    	        showInfo(data.msg,'180px');
                if(isInspectCommitted == true){
			        $.pjax.reload({container:'#inspectPjax',url:inspectRecordUrl,cache:false,push:false,replace:false,scrollTo:false,timeout : 5000});  //Reload
                }		   
    	   }else{
               isInspectCommitted = false;
    	       showInfo(data.msg,'180px',2);
            }
	},
});
    if(inspectCount == 0){  
                
            $('#labCheck button').html('保存');
            $('.field-inspectrecord-inspectname').show();
            $('#inspect-template-select-ui').show();
            $('#inspect-template-select').hide();
            setTimeout(function () {
                main.inspectSelect2();
                $('#labCheck button').attr({'type': 'submit'});
            }, 500);
            $('.btn-inspect-application').html('');
     }    
")
    ?>
    <?php Pjax::end() ?>
</div><!-- _checkRecordForm -->