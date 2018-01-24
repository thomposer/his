<?php

use yii\widgets\ActiveForm;
use app\modules\make_appointment\models\Appointment;
use yii\web\View;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="charge-record-form">
    <?php
    $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'type')->radioList(ArrayHelper::map($type, 'id','type'),['class' => 'sex'])->label($attributeLabels['type'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>
	<div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'second_department_id')->radioList(ArrayHelper::map($departmentInfo, 'department_id', 'name'),['class' => 'sex'])->label($attributeLabels['second_department_id'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$departmentInfoCount = count($departmentInfo);
$statusListJson = json_encode($statusList,true);
$typeCount = count($type);
$js = <<<JS
    var date = '$date';
    var doctor_id = '$doctor_id';
    var departmentInfoCount = $departmentInfoCount;
    var statusListJson = $statusListJson;
    var typeCount = $typeCount;
    for(var o in statusListJson){
         $('#appointment-type label').children('input[value='+o+']').attr('disabled',true);
    }
    //若预约服务数量为1，并且可选／可预约时。默认选中
    if(typeCount == 1 && statusListJson == ''){
         $('#appointment-type label:first').children('input').attr('checked',true);
    }
    //若为只有一个预约科室，默认选中
    if(departmentInfoCount == 1){
        $('#appointment-second_department_id label:first').children('input').attr('checked',true);
    }
    $('body').on('click','#createAppointment',function(){
		var type = $('input[name="Appointment[type]"]:checked').val();
		var departmentId = $('input[name="Appointment[second_department_id]"]:checked').val();
		if(!type){
			showInfo('请选择预约服务','180px',2);
            return false;
		}
		if(!departmentId){
			showInfo('请选择预约科室','180px',2);
			return false;
		}
		window.location.href = createUrl+'?departmentId='+departmentId+'&doctor_id='+doctor_id+'&date='+date+'&type='+type;
	});            
    
JS;
$this->registerJs($js,View::POS_END);
?>