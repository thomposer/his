<?php

use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\patient\models\Patient;
use app\common\Common;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/make_appointment/showMaxAppointmentInfo.css');
$apiAppointmentDoctorTimeList  = Url::to(['@apiAppointmentDoctorTimeList']);
$makeAppointmentAppointmentCreate =  Url::to(['@make_appointmentAppointmentCreate']);

?>

<div class="max-appoitment-info" >
    <div class="appointment-type-container">
        <div class="appointment-type-container-left pull-left">
            <span>筛选：</span>
        </div>
        <div class="appointment-type-container-right pull-right">
            <?php
                $str = '';
                foreach($appointmentTypeList as $key => $value){
                    $checked = ($key == 0)?'checked':'';
                    $str .= '<label type-name="'.$value['type'].'" type-id="'.$value['id'].'"><input type="radio" name="type" '.$checked.'><span>'.Html::encode($value['type']).'</span></label>';
                }
                echo $str;
            ?>

        </div>
    </div>
    <span class="clearfix"></span>
    <ul id="max-appointment-body" class="max-appointment-ul">
        <?php
            $content = '';
            $disabledStyle = '';
            $contentText = '';
            if(!empty($rows)){
                foreach ($rows as $key => $value){
                    $disabledStyle = ($value['selected'] == false) ? 'disabled-style' : '';  //判断是否为可点击
                    $contentText = ($value['selected'] == false) ? '已预约' : Html::encode($spotTypeName);  //判断是否为可点击
                    if(!$departmentId){
                        $content .= '<a class="skip-url">';
                    }
                    else if($value['selected']){
                        $content .= '<a class="skip-url" departmentId="'.$departmentId.'" href="'.$makeAppointmentAppointmentCreate.'?departmentId='.$departmentId.'&doctor_id='.$doctorId.'&date='.$date.' '.$value['name'].'&type='.$type.'">';
                    }else{
                        $content .= '<a>';
                    }

                    $content .= '<li class="'.$disabledStyle.'">';
                    $content .= '<span class="pull-left">'.Html::encode($value['name']).'</span>';
                    $content .= '<span class="content-text pull-right">'.$contentText.'</span>';
                    $content .= '</li>';
                    $content .= '</a>';

                }
            }

            echo $content;
        ?>
    </ul>

</div>
<?php
$type=$type?$type:'';
$departmentId = $departmentId?$departmentId:'';
?>
<?php
$js = <<<JS
    var datePost =  "$date";
    var doctorName =  "$doctorName";
    var departmentId = "$departmentId";
    var apiAppointmentDoctorTimeList = "$apiAppointmentDoctorTimeList";
    var makeAppointmentAppointmentCreate = "$makeAppointmentAppointmentCreate";
//    if($type){
    var type = "$type";
    // }
   require(["$baseUrl/public/js/make_appointment/showMaxAppointmentInfo.js"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
