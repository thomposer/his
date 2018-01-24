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
    'id' => 'makeupInspectPjax'
])
?>
<div class="inspectRecordForm col-xs-12">

    <?php
    $form = ActiveForm::begin([
                'action' => Url::to(
                        [
                            'save-inspect',
                            'id' => $id,
                            'patientId' => $patientId,
                            'doctorId' => $doctorId
                        ]
                ),
                'id' => 'inspect-record',
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
    <div class='box-body recipe-record-form row'>
        <div class="col-md-8">
            <?php echo $form->field($model, 'inspectName')->dropDownList(ArrayHelper::map($inspectList, 'id', 'name'), ['prompt' => '请选择', 'class' => 'form-control select2', 'style' => 'width:100%']) ?>
        </div>
        <div class="col-md-3">
            <?php echo Html::a('检验医嘱配置', Yii::$app->request->getHostInfo() . Url::to(['@spotInspectCreate']), ['class' => 'btn btn-default btn-mt25 blank', 'target' => '_blank']) ?>
        </div>
    </div>
    <div class = 'inspect-content'>
        <?php if (!empty($inspectRecordDataProvider)): ?>
            <?php foreach ($inspectRecordDataProvider as $v): ?>
                <div class = 'inspect-list'>
                    <div class="check-name-top">
                        <div class = 'check-name' ><span title= "" ><?= $v['name'] ?></span></div>
                        <div class = 'check-id'>
                            <input type="hidden" class="form-control" name="InspectRecord[deleted][]" value="">
                            <input type="hidden" class="form-control" name="InspectRecord[uuid][]" value="<?= $v['id'] ?>">
                            <input type="hidden" class="form-control" name="InspectRecord[inspect_id][]" value='<?= Html::encode(Json::encode($v, JSON_ERROR_NONE)) ?>'>
                            <input type="hidden" class="form-control" name="InspectRecord[isNewRecord][]" value='0'>
                        </div>
                        <div class="op-group"><?= Html::img(Yii::$app->request->baseUrl . '/public/img/common/delete.png') ?></div>
                    </div>
                    <div id="grid" class="grid-view table-responsive"><table class="table table-hover"><thead>
                                <tr class="header">
                                    <th class="col-sm-1">序号</th>
                                    <th class="col-sm-2">项目名称</th>
                                    <th class="col-sm-2">检查结果</th>
                                    <th class="col-sm-2">单位</th>
                                    <th class="col-sm-2">参考值</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($v['inspectItem'] as $key => $v2): ?>
                                    <tr data-key="2507">
                                        <td><?= $key + 1 ?></td>
                                        <td><?= Html::encode($v2['name']) ?></td>
                                        <td>
                                            <input type="text" class="form-control" name="InspectItem[result][<?= $v['id'] ?>][<?= $v2['id'] ?>]" value=<?= $v2['result'] ?>>
                                            <input type="hidden" name="InspectRecord[id][]" class="checkitemid" value=<?= $v2['id'] ?>>
                                        </td>
                                        <td><?= Html::encode($v2['unit']) ?></td>
                                        <td><?= Html::encode($v2['reference']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class = 'row makeup-outpatient-check'>
        <div class="button-center form-group">
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form ', 'type' => 'button', 'data-dismiss' => 'modal']) ?>
            <?= Html::button('保存', ['class' => 'btn btn-default btn-form  ', 'type' => 'submit']) ?>
        </div>

    </div>
    <?php ActiveForm::end(); ?>

</div><!-- _checkRecordForm -->
<?php
$inspectCount = count($inspectRecordDataProvider);
$this->registerJs("
    var inspectCount = $inspectCount;
    $('#inspect-record').yiiAjaxForm({
	   beforeSend: function() {
						
	   },
	   complete: function() {
					   
	   },
	   success: function(data) {

	   if(data.errorCode == 0){
	        showInfo(data.msg,'180px');
			$.pjax.reload({container:'#makeupInspectPjax',cache : false,timeout : 5000});  //Reload
                        $.pjax.reload({container:'#ump_reception'+$id,cache : false,timeout : 5000});  //Reload
                            $('.inspectIsNewRecord[value=1]').each(function(key){
                                 $(this).val(0);
                                 if(data.data!=''){
                                    var ret=JSON.parse(data.data);
                                    var recordData=JSON.parse($(this).siblings('#inspect_id').val());
                                     recordData.id=ret[key]
                                     $(this).siblings('#inspect_id').val(JSON.stringify(recordData));
                                 }
                             });
                             $('[name=\'InspectRecord[deleted][]\'][value=1]').each(function(){
                                $(this).parents('.inspect-list').remove();
                             }); 
                        $('.ump-reception>ul').find('li').eq(1).find('a').click();
	   }else{
	       showInfo(data.msg,'180px',2);
        }
	},
});
//if(recipeCount == 0){
            setTimeout(function () {
                $('.field-inspectrecord-inspectname .select2').select2();
            }, 500);
//     }
")
?>
<?php Pjax::end() ?>