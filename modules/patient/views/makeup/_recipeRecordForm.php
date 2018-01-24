<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\modules\spot\models\RecipeList;
use yii\helpers\Json;
use app\modules\outpatient\models\RecipeRecord;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CureRecord */
/* @var $form ActiveForm  */
$attributes = $model->attributeLabels();
?>
<?php
Pjax::begin([
    'id' => 'recipePjax'
])
?>

<div class="cure-record-index col-xs-12"> 
    <?php
    $form = ActiveForm::begin([
                'action' => Url::to([
                    'save-recipe',
                    'id' => $id,
                    'patientId' => $patientId,
                    'doctorId' => $doctorId
                ]),
                'id' => 'recipe-record',
                'options' => [
                    'data' => [
                        'pjax' => true
                    ]
                ]
            ])
    ?>
    <div class="row">
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'billingTime')->widget(
                    DateTimePicker::className(), [
                'inline' => false,
                'language' => 'zh-CN',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii',
                    'minuteStep' => 1,
                ]
                    ]
            )->label($attributes['billingTime'] . '<span class = "label-required">*</span>');
            ?>
            <?php // echo  $form->field($model, 'diagnosis_time')->textInput(['maxlength' => 30])->label($labels['diagnosis_time'] . '<span class = "label-required">*</span>')  ?>
        </div>
    </div>
    <div class='box-body recipe-record-form row'>
        <div class="col-md-8">
            <?php echo $form->field($model, 'recipeName')->dropDownList(ArrayHelper::map($recipeList, 'id', 'name'), ['prompt' => '请选择', 'class' => 'form-control select2', 'style' => 'width:100%']) ?>
        </div>
        <div class="col-md-3">
            <?php echo Html::a('处方医嘱配置', Url::to(['@spotRecipeListCreate']), ['class' => 'btn btn-default btn-mt25 blank','target'=>'_blank']) ?>
        </div>
    </div>
    <div class='box'>
        <div id="w3" class="grid-view table-responsive">
            <table class="table table-hover recipe-form">
                <thead>
                    <tr class="header">
                        <th><?= $attributes['name'] ?></th>
                        <th><?= $attributes['dosage_form'] ?></th>
                        <th><?= $attributes['dose'] ?></th>
                        <th><?= '' ?></th>
                        <th><?= $attributes['used'] ?></th>
                        <th><?= $attributes['frequency'] ?></th>
                        <th><?= $attributes['day'] ?></th>
                        <th><?= $attributes['num'] ?></th>
                        <th><?= $attributes['unit'] ?></th>
                        <th><?= $attributes['remark'] ?></th>
                        <th><?= '操作' ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recipeRecordDataProvider): ?>
                        <?php foreach ($recipeRecordDataProvider as $v): ?>	
                            <?php
                            $isEdit = false; //是否可编辑，默认为false
                            ?>	
                            <tr class = "recipeNameTd">
                                <td>
                                   <?= Html::encode($v['name']).Html::input('hidden','RecipeRecord[recipe_id][]',Json::encode(array_merge($v,['isNewRecord' => 0]),JSON_ERROR_NONE)).Html::input('hidden','RecipeRecord[isNewRecord][]',0); ?>
                                </td>
                                <td>
                                    <?= RecipeList::$getType[$v['dosage_form']] ?>
                                </td>
                                <td>
                                    <?= Html::input('input', 'RecipeRecord[dose][]', $v['dose'], ['class' => 'form-control','style'=>'']) ?>
                                </td>
                                <td>
                                    <div style="margin-left: -10px;">
                                        <?= Html::dropDownList('RecipeRecord[dose_unit][]',$v['r_dose_unit'],$v['l_dose_unit'],['class' => 'form-control','style' => '']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?= Html::dropDownList('RecipeRecord[used][]', $v['used'], RecipeList::$getDefaultUsed, ['class' => 'form-control']) ?>
                                </td>
                                <td>
                                    <?= Html::dropDownList('RecipeRecord[frequency][]', $v['frequency'], RecipeList::$getDefaultConsumption, ['class' => 'form-control']) ?>
                                </td>
                                <td>
                                    <?= Html::input('input', 'RecipeRecord[day][]', $v['day'], ['class' => 'form-control']) ?>
                                </td>
                                <td>
                                    <?= Html::input('input', 'RecipeRecord[num][]', $v['num'], ['data-id' => $v['recipe_id'], 'class' => 'form-control recipeNum num_' . $v['recipe_id']]) ?>
                                </td>
                                <td id="unit">
                                    <?= Html::tag('span',RecipeList::$getUnit[$v['unit']]) ?>
                                </td>
                                <td class="status-recipe-column">
                                    <?= Html::input('input', 'RecipeRecord[description][]', $v['description'], ['class' => 'form-control']) ?>
                                </td>
                                <td class = 'recipe-delete op-group'>
                                    <?= Html::hiddenInput('RecipeRecord[deleted][]').Html::img(Yii::$app->request->baseUrl.'/public/img/common/delete.png');?>
                                </td>
                            </tr>
                            <?php if($v['skin_test_status'] != 0):  ?>
        					<tr class = "skinTestTr">
        						<?php 
        						    $display = '';
        						    if($v['skin_test_status'] == 2){
        						        $display = 'style="display:none"';
        						    }
        						    $skinHtml = '<td>皮试：</td>';
        						    $skinHtml .= '<td colspan=2>';
        						    $skinHtml .= Html::dropDownList('RecipeRecord[skin_test_status][]',$v['skin_test_status'],RecipeRecord::$getSkinTestStatus,['class' => 'skinTestStatus form-control','style' => 'display:inline-block']);
        						    $skinHtml .= '</td>';
        						    $skinHtml .= '<td colspan="8">';
        						    $skinHtml .= '<label class ="skinTestContent"  ';
        						    $skinHtml .= $display.'>';
        						    $skinHtml .= Html::encode($v['skin_test']);
        						    $skinHtml .= '</label>';
        						    $skinHtml .= '</td>';
        						    echo $skinHtml;
        						?>
        					</tr>
    					<?php else:?>
    					
    						<?= Html::hiddenInput('RecipeRecord[skin_test_status][]',0);//若没有配置皮试，则默认为没，即是0 ?>
    					
    					<?php endif;?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class = 'row makeup-outpatient'>
        <div class="button-center form-group">
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form ', 'type' => 'button', 'data-dismiss' => 'modal']) ?>
            <?= Html::button('保存', ['class' => 'btn btn-default btn-form ', 'type' => 'submit']) ?>
        </div>

    </div>
    <?php ActiveForm::end() ?>    
</div>

<div id = 'recipe_print' class = "tab-pane hide">
</div>
<?php
$recipeCount = count($recipeRecordDataProvider);
$this->registerJs("
    var recipeCount = $recipeCount;
    $('#recipe-record').yiiAjaxForm({
	   beforeSend: function() {
						
	   },
	   complete: function() {
					   
	   },
	   success: function(data) {

		    
	   if(data.errorCode == 0){
            showInfo(data.msg,'180px');
		$.pjax.reload({container:'#ump_reception'+$id,cache : false,timeout : 5000});  //Reload
                    $('.recipeIsNewRecord').each(function(key){
                            $(this).val(0);
                            if(data.data!=''){
                                    var ret=JSON.parse(data.data);
                                    var recordData=JSON.parse($(this).siblings('#recipe_id').val());
                                     recordData.id=ret[key];
                                     $(this).siblings('#recipe_id').val(JSON.stringify(recordData));
                            }
                        });
                        $('[name=\'RecipeRecord[deleted][]\'][value=1]').each(function(){
                                $(this).parent().parent().remove();
                             }); 
                $('#ajaxCrudModal').modal('hide');
                document.location.reload();//当前页面
	   }else{
	       showInfo(data.msg,'180px',2);
        }
	}, 
});
//    if(recipeCount == 0){
            setTimeout(function () {
                $('.field-reciperecord-recipename .select2').select2();
            }, 500);
//     }
")
?>
<?php Pjax::end() ?>
