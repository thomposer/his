<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\spot_set\models\SpotType;

/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\search\AppointmentSearch */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
$params = Yii::$app->request->queryParams;
//if(!isset($params['username'])){//第一次，清空回填
//    unset($params['type']);
//}
?>

<div class="appointment-search hidden-xs">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['message','header_type' => $params['header_type'],'entrance' => $params['entrance'],'time' => $params['time'],'endDate' => $params['endDate'],'date_formate' => $params['date_formate'],'isDoctor' => $params['isDoctor']],
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <div class="form-group">
        <?= Html::textInput('username',$params['username'],['placeholder' => '请输入患者姓名','class' => 'form-control','style'=>'width:120px;']) ?>
    </div>
    <div class="form-group">
        <?= Html::textInput('iphone',$params['iphone'],['placeholder' => '请输入手机号','class' => 'form-control','style'=>'width:100px;']) ?>
    </div>
    <div class="form-group">
        <?php
            if($params['isDoctor']){
                $tmpData = ArrayHelper::map($doctorInfo, 'id', 'username');
                echo Html::dropDownList('doctor_id',$params['doctor_id'],array($params['doctor_id'] => $tmpData[$params['doctor_id']]),['class'=>'form-control popup-width']);
            }else{
                echo Html::dropDownList('doctor_id',$params['doctor_id'],$doctorInfo?ArrayHelper::map($doctorInfo, 'id', 'username'):[],['prompt' => '请选择医生','class'=>'form-control popup-width']);
            } 
        ?>
    </div>
    <div class="form-group">
        <?= Html::dropDownList('type',$params['type'],$spotTypeList?ArrayHelper::map($spotTypeList, 'id', 'name'):[],['prompt' => '服务类型','class'=>'form-control doctor-width']) ?>
    </div>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
