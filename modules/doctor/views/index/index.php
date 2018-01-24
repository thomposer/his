<?php

use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
use dosamigos\datetimepicker\DateTimePickerAsset;
use yii\helpers\Json;
use app\modules\user\models\User;
DateTimePickerAsset::register($this)->js[] = 'js/locales/bootstrap-datetimepicker.zh-CN.js';
CrudAsset::register($this);

$this->title = '医生工作台';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$canJump=1;
if(!isset($this->params['permList']['role']) && !in_array(Yii::getAlias('@patientIndexView'), $this->params['permList'])){
    //无权限 跳转到患者库
    $canJump=2;
}
//预约的参数

if(!$timeLine){
    $timeLine=json_encode([]);
}
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/easyhincalendar/easyhincalendar.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/doctor/index.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="doctor-index col-md-12">
    <div class = 'row'>
        <div class = 'col-xs-3 col-sm-3 col-md-3'>
            <div class="today-job">
                <div class="doctor-h2">今日工作概况</div>
                <div class="today-ren">
                    <div class="today-left">
                        <div class="doctor-yijiezhen"><?= $outpatient_num ?></div>
                        <div class="doctor-yijiezhen-text">已接诊人数</div>
                    </div>
                    <div class="today-right">
                        <div class="doctor-yijiezhen"><?= $triage_num ?></div>
                        <div class="doctor-yijiezhen-text">待接诊人数</div>
                    </div>
                </div>
            </div>
        </div>
        <div class = 'col-xs-9 col-sm-9 col-md-9 serarch-right'>
            <div class="doctor-search-right">
                <div class="huanzhe"> 患者查询</div>
                <div class="doctor-search-btn">
                    <div class="search-doctor">
                        <div class="search-input-div">
                            <div class="icon_search fa fa-search"></div>
                            <div class="search-input-input"><input class="search-input" placeholder="请输入患者姓名或手机号"/></div>
                        </div>
                        <div class="search-btn" id="doctor_search" data-url=<?= Url::to('') ?> role='modal-create' data-toggle='tooltip' data-request-method='POST' data-modal-size = "large" >搜索</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class = "box doctor-box">
        <div id="schedule_grid">

        </div>
    </div>
    <div class = "box doctor-box">
        <div id="calendar" class="no-radius">
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var apiSchedulingIndex = '<?= Url::to(['@apiSchedulingDoctorSchedule']); ?>';
    var canJump = <?= $canJump ?>;
    //    预约相关参数
    var  appointIndex = '<?= Url::to(['@apiGetDoctorAppointment']); ?>';
    var  appointmentIndex = '<?= Url::to(['@make_appointmentAppointmentIndex']); ?>';
    var  viewAppointmentMessage = '<?= Url::to(['@apiAppointmentMessage']); ?>';
    var  closeAppointmentSwitch =  '<?= Url::to(['@closeAppointment']); ?>';
    var colseAppointmentStatus = false;
    var  timeLineSpilt = <?= $timeLineSpilt; ?>;
    var  spiltLength = '<?=  $spiltLength; ?>';
    var  timeLine =<?=  $timeLine; ?>;
    var  position = false;
    var  closeTimeLine=<?=  \yii\helpers\Json::encode($closeTimeLine); ?>;
    var doctorId = '<?= $doctorId ?>';
    var entrance = '<?=  $entrance; ?>';
    var nowYearMonthDate = '<?= date("Y-m-d"); ?>';
    var  scheduleOpt=<?= json_encode($schedule,true)?>;
	var occupationList = <?=  json_encode(User::$getOccuption,true); ?>;
    require(["<?= $baseUrl ?>" + "/public/js/doctor/doctor.js?v="+ '<?= $versionNumber ?>'], function (main) {
        main.init();
    });
    require(["<?= $baseUrl ?>"+"/public/js/make_appointment/appointment.js?v="+'<?= $versionNumber ?>'],function(main){
        main.init();
    });
    require(["<?= $baseUrl ?>"+"/public/js/make_appointment/closeAppointment.js?id=3333"],function(main){
        main.init();
    });
</script>
<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
