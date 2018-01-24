<?php
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
use dosamigos\datetimepicker\DateTimePicker;
use yii\helpers\Html;
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\make_appointment\models\search\AppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$baseUrl = Yii::$app->request->baseUrl;

?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/closeAppointment.css')?>
<?php $this->endBlock()?>
<form id="closeAppointmentForm">
<div id = 'appointConfig' >
    <div>
        <span>
            <label class="control-label doctor_time_label" for="appoint-config-begin-time">关闭预约时间段
                <span class="label-required">*</span>
            </label>
        </span>
        <span class="close_reason">
            <label class="control-label doctor_count_label" for="appoint-config-doctor-count">关闭原因<span class="label-required">*</span></label>
        </span>
    </div>
    <?php foreach($closeAppointment as $key=>$value): ?>
    <div class="row appointConfig">
        <div class="close-appointment-row">
            <div class="col-sm-3 bootstrap-timepicker">
                <div class="form-group">
                    <?php
                    echo DateTimePicker::widget([
                        'id' => 'appointment-config-begin-time'.$key,
                        'name' => 'begin_time',//当没有设置model时和attribute时必须设置name
                        'language'=>'zh-CN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd hh:ii',
                            'size'=>'lg',
                            'minuteStep'=>10,
                            'modalBackdrop'=>true,
                            'class'=>"appointment-config-begin-time",
                        ],
                        'options' => [
                            'autocomplete' => 'off',
                            'class'=>"appointment-config-begin-time",
                            'placeholder' => '开始时间',
                        ],
                        'value'=> $value['begin_time']==0?'':date("Y-m-d H:i",$value['begin_time'])
                    ]);
                    ?>
                    <div class="help-block"></div>
                </div>
            </div>
            <span class="me-col-xs-1">
                <label class="po-cell" for="">-</label>
            </span>
            <div class="col-sm-3 bootstrap-timepicker">
                <div class="form-group">
                    <?php
                    echo DateTimePicker::widget([
                        'id' => 'appointment-config-end-time'.$key,
                        'name' => 'end_time',//当没有设置model时和attribute时必须设置name
                        'class'=>"appointment-config-end-time",
                        'clientOptions' => [
                            'autoclose' => true,
                            'language'=>'zh-CN',
                            'format' => 'yyyy-mm-dd hh:ii',
                            'size'=>'lg',
                            'minuteStep'=>10,
                            'class'=>"appointment-config-end-time",
                        ],
                        'options' => [
                            'autocomplete' => 'off',
                            'language'=>'zh',
                            'class'=>"appointment-config-end-time",
                            'placeholder' => '结束时间',
                        ],
                        'value'=> $value['end_time']==0?'':date("Y-m-d H:i",$value['end_time'])
                    ]);
                    ?>
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-sm-3 col-sm-3-custom">
                <div class="form-group field-appoint-config-doctor-count close_reason_input">
                    <?= Html::textInput('close_reason',$value['close_reason'],['id' => 'appointment-config-doctor-count','class' => 'form-control timepicker','placeholder' => '不多于25字','maxlength' => 25]) ?>
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-sm-2 col-sm-2-custom">
                <div class="form-group ">
                    <?php
                        if(count($closeAppointment>1)){
                            $style = "display: none;";
                        }else{
                            $style = "display: inline-block;";
                        }
                        if($key+1==count($closeAppointment)) {
                            $style = "display: inline-block;";
                        }


                        echo    '<a href="javascript:void(0);" class="btn-from-delete-add btn-from-delete-add-top-0 btn clinic-delete" style="display: inline-block;">
                            <i class="fa fa-minus"></i>
                           </a>';

                        echo    '<a href="javascript:void(0);" class="btn-from-delete-add  btn-from-delete-add-top-0 btn clinic-add" style="'.$style.'">
                                    <i class="fa fa-plus"></i>
                                 </a>';
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach;?>
 </div>
</form>
