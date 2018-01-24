<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

?>

<div class="doc-info pull-left">
    <?php
    $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['class' => 'form-horizontal search-form choice-doc-form', 'data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ],
    ]); ?>
    <div class="doc-selected-form hidden">
    <div class="cover"></div>
    <?= $form->field($nurseDoctorConfigModel, 'doctor_id')->checkboxList(ArrayHelper::map($allDoctor, 'doctor_id', 'doctorName'))->label(false); ?>
    <div class="col-sm-12 text-center choice-btn">
        <?= Html::submitButton('确定', ['class' => 'submit btn btn-default btn-form']) ?>
    </div>
    </div>

<?php

//var_dump($docFocusList);
if ($docFocusList) {
    $a = '';
    $a .= '<div class="doc-info-form-display">';
    foreach ($docFocusList as $key => $value) {

        if ($value['selected']) {
            $a .= '<label title="'. Html::encode($value['doctorName']) .'" class="cursor doc-selected" doctor-id="'.$value['doctor_id'].'">';
            $a .= '<div class="circle pull-left circle-seleted"></div>';
            $a .= '<span class="doctor-name">';
            $a .= Html::encode($value['doctorName']);
            $a .='</span>';
            $a .= '</label>';
        } else {
            $isSchedule = $value['isSchedule'] == true ? 'circle-has-schedule' : 'circle-not-schedule';
            $a .= '<label title="'. Html::encode($value['doctorName']) .'" class="cursor" doctor-id="'.$value['doctor_id'].'">';
            $a .= '<div class="circle pull-left ' . $isSchedule . '">';
            $a .= '</div>';
            $a .= '<span class="doctor-name">';
            $a .= Html::encode($value['doctorName']);
            $a .='</span>';
            $a .= '</label>';
        }


    }
    $a .= '</div>';
    echo $a;
}

?>
</div>

<?php ActiveForm::end(); ?>

