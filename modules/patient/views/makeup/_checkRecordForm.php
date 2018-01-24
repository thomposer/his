<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\CheckRecord */
/* @var $form ActiveForm */
$attributes = $model->attributeLabels();
?>
<?php
Pjax::begin([
    'id' => 'makeupCheckPjax'
])
?>
<div class="inspectRecordForm col-xs-12">

    <?php
    $form = ActiveForm::begin([
                'action' => Url::to(
                        [
                            'save-check',
                            'id' => $id,
                            'patientId' => $patientId,
                            'doctorId' => $doctorId
                        ]
                ),
                'id' => 'check-record',
    ]);
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
            )->label($attributes['billingTime'] . '<span class = "label-required">*</span>')
            ?>
            <?php // echo  $form->field($model, 'diagnosis_time')->textInput(['maxlength' => 30])->label($labels['diagnosis_time'] . '<span class = "label-required">*</span>')  ?>
        </div>
    </div>
    <div class='box-body check-record-form row'>
        <div class="col-md-8">
            <?= $form->field($model, 'checkName')->dropDownList(ArrayHelper::map($checkList, 'id', 'name'), ['prompt' => '请选择', 'class' => 'form-control select2', 'style' => 'width:100%']) ?>
        </div>
        <div class="col-md-3">
            <?php echo Html::a('检查医嘱配置', Yii::$app->request->getHostInfo() . Url::to(['@spotCheckListCreate']), ['class' => 'btn btn-default btn-mt25 blank', 'target' => '_blank']) ?>
        </div>
    </div>
    <div class = 'check-content'>
        <?php if (!empty($checkRecordDataProvider)): ?>
            <?php foreach ($checkRecordDataProvider as $v): ?>
                <div class = 'check-list'>
                    <div class="check-name-top">
                        <div class = 'check-name' ><span title= "" ><?= Html::encode($v['name']) ?></span></div>
                        <div class = 'check-id'>
                            <input type="hidden" class="form-control" name="CheckRecord[deleted][]" value="">
                            <input type="hidden" class="form-control" name="CheckRecord[check_id][]" value='<?= Html::encode(Json::encode($v, JSON_ERROR_NONE)) ?>'>
                            <input type="hidden" class="form-control" name="CheckRecord[isNewRecord][]" value='0'>
                        </div>
                        <div class="op-group"><?= Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png') ?></div>
                    </div>
                    <div id="grid" class="grid-view table-responsive">
                        <?= $form->field($model, 'description[]')->textarea([ 'rows' => 5, 'maxlength' => '255', 'id' => 'description_' . $v['id'], 'value' => $v['description'], 'placeholder' => '描述症状及可能存在的病情等...'])->label($attributes['description'], ['class' => "col-sm-12 header"]) ?>

                        <?= $form->field($model, 'result[]')->textarea([ 'rows' => 5, 'maxlength' => '255', 'id' => 'result_' . $v['id'], 'value' => $v['result'], 'placeholder' => '描述治疗结论等...'])->label($attributes['result'], ['class' => "col-sm-12 header"]) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class = 'row makeup-outpatient-check'>
        <div class="button-center form-group">
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form ', 'type' => 'button', 'data-dismiss' => 'modal']) ?>
            <?= Html::button('保存', ['class' => 'btn btn-default btn-form ', 'type' => 'submit']) ?>
        </div>

    </div>
    <?php ActiveForm::end(); ?>

</div><!-- _checkRecordForm -->
<?php
$inspectCount = count($checkRecordDataProvider);
$this->registerJs("
    var inspectCount = $inspectCount;
    $('#check-record').yiiAjaxForm({
	   beforeSend: function() {
						
	   },
	   complete: function() {
					   
	   },
	   success: function(data) {

	   if(data.errorCode == 0){
	        showInfo(data.msg,'180px');
			$.pjax.reload({container:'#makeupCheckPjax',cache : false,timeout : 5000});  //Reload
                        $.pjax.reload({container:'#ump_reception'+$id,cache : false,timeout : 5000});  //Reload
                            $('.checkIsNewRecord[value=1]').each(function(key){
                                 $(this).val(0);
                                 if(data.data!=''){
                                    var ret=JSON.parse(data.data);
                                    var recordData=JSON.parse($(this).siblings('#check_id').val());
                                     recordData.id=ret[key]
                                     $(this).siblings('#check_id').val(JSON.stringify(recordData));
                                 }
                             });
                             $('[name=\'CheckRecord[deleted][]\'][value=1]').each(function(){
                                $(this).parents('.check-list').remove();
                             }); 
                        $('.ump-reception>ul').find('li').eq(2).find('a').click();
	   }else{
	       showInfo(data.msg,'180px',2);
        }
	},
});
//if(recipeCount == 0){
            setTimeout(function () {
                $('.field-checkrecord-checkname .select2').select2();
            }, 500);
//     }
")
?>
<?php Pjax::end() ?>