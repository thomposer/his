<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datetimepicker\DateTimePicker;
use dosamigos\datepicker\DatePicker;
use app\common\Common;
use yii\helpers\Url;
$baseUrl = Yii::$app->request->baseUrl;
$publicSettingPng = $baseUrl . '/public/img/'. '/tab/tab_setting.png';
?>

<div class='row'>
    <div class="col-sm-12 col-md-12 tc nurse-title">
        <span class="nurse-detail-title">
            <?php
            echo $date . Common::getWeekDay($date) . "待办工作";
            ?>

        </span>
    </div>
    <div class="col-sm-12 col-md-12">
        <div class="pull-left">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['class' => 'form-horizontal search-form', 'data-pjax' => true],
                'fieldConfig' => [
                    'template' => "{input}",
                ]
            ]);
            ?>
            <div class="pull-left" style="margin-left: -5px;">
                    <?php if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@nurseIndexCreateRecord'), $this->params['permList'])): ?>
                        <?= Html::a(' 方便门诊 ',['@nurseIndexCreateRecord'],['class' => 'btn btn-default font-body2 fa fa-plus','data-pjax' => 0]) ?>
                    <?php endif ?>
                    <div class="nurse-datepicker-wrapper">
                        <a  href="<?= Url::to(['@nurseIndexIndex','doctor_id'=>$_REQUEST['doctor_id'], 'date'=>$yesterday])?>" class="eh-prev-button eh-button  eh-update-button">
                            <i class="fa fa-angle-left"></i>
                        </a>
                        <!-- 日期选择器 -->
                        <div class="nurse-datepicker clearfix">
                            <?= $form->field($model, 'nurseDate')->widget(
                                DatePicker::className(), [
                                    'inline' => false,
                                    'addon' => '',
                                    'language' => 'zh-CN',
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd',
                                    ],
                                    'options' => [
                                        'value' => $date ? $date.' '.Common::getWeekDay($date) : date('Y-m-d'),
                                        'placeholder' => '请选择日期',
                                        'class' => 'form-control',
                                        'readonly' => true
                                    ]
                                ]
                            )->label() ?>
                        </div>
                        <a href="<?= Url::to(['@nurseIndexIndex','doctor_id'=>$_REQUEST['doctor_id'],'date'=>$nextday])?>" class="eh-next-button eh-button  eh-update-button">
                            <i class="fa fa-angle-right"></i>
                        </a>
                        <a class="eh-thisweek-button  eh-button eh-state-default eh-corner-left eh-corner-right"  href="<?= Url::to(['@nurseIndexIndex','doctor_id'=>$_REQUEST['doctor_id'],'date'=>date('Y-m-d')])?>">
                                 今天
                        </a>
                    </div>
            </div>
            

            <!--            Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default'])-->
        </div>
        <div class="pull-right focus-doc cursor" state="1">
            <span>我关注的医生</span>
            <i class="fa fa-angle-down"></i>
        </div>
        <?php if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@roomDoctorRoomConfig'), $this->params['permList'])): ?>
            <?= Html::a(Html::tag("i","",['class' => 'icon_url','style' => 'background:url('. $publicSettingPng . ');background-size:16px;'])."医生常用诊室设置", ['@roomDoctorRoomConfig'], ['class' => 'font-body2 pull-right','data-pjax' => 0, 'style' => 'font-size:14px;margin-right:20px;', 'target' => '_blank']) ?>
        <?php endif ?>
    </div>
</div>
<?php ActiveForm::end(); ?>

