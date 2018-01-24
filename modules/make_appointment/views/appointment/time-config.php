<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\modules\make_appointment\models\Appointment;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
use dosamigos\datetimepicker\DateTimePickerAsset;
use yii\helpers\Json;
use app\modules\user\models\User;
use Faker\Provider\zh_CN\DateTime;

DateTimePickerAsset::register($this)->js[] = 'js/locales/bootstrap-datetimepicker.zh-CN.js';
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\make_appointment\models\search\AppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '预约';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$public_img_path = $baseUrl . '/public/img/';
$tabArray = array();
$tabArray[] = ['title' => '预约管理', 'url' => Url::to(['@make_appointmentAppointmentAppointmentDetail', 'type' => 3]), 'type' => 3, 'icon_img' => $public_img_path . 'make_appointment/tab_order.png'];
if (in_array(1, $appointment_type)) {
    $tabArray[] = ['title' => '医生预约时间设置', 'url' => Url::to(['@make_appointmentAppointmentTimeConfig', 'type' => 4]), 'type' => 4, 'icon_img' => $public_img_path . 'make_appointment/tab_setting.png'];
}
if (in_array(2, $appointment_type)) {
    $tabArray[] = ['title' => '科室预约设置', 'url' => Url::to(['@make_appointmentAppointmentRoomConfig', 'type' => 5]), 'type' => 5, 'icon_img' => $public_img_path . 'make_appointment/tab_setting.png'];
}
$tabData = [
    'titleData' => $tabArray,
    'activeData' => [
        'type' => 4
    ]
];
$params = [
    'searchName' => 'appointment',
    'statusName' => 'type',
    'buttons' => [
    ]
];
//$timeLine=json_encode([]);
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/easyhincalendar/easyhincalendar.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/appointmentConfig.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>

<div class="appointment-index col-xs-12">
<?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>

    <div class = 'row search-margin'>
        <div class = 'col-sm-4 col-md-4'>
            <!--           //加载日历表格button-->
<?= $this->render(Yii::getAlias('@searchStatusSkip'), $params) ?>
        </div>
    </div>
    <div id="schedule_grid" class="no-radius">

    </div>
</div>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>
<!--@apiAppointmentTimeConfig-->
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var copyWeekConfig = '<?= Url::to(['@make_appointmentAppointmentCopyTimeConfig']); ?>';
    var apiSchedulingIndex = '<?= Url::to(['@apiAppointmentTimeConfig']); ?>';
    var scheduleSchedulingAddScheduling = '<?= Url::to(['@make_appointmentAppointmentSaveTimeConfig']); ?>';
    var size = 'modal-normal';
    var timeConfig = <?= json_encode($timeConfig, true) ?>;
    var nowYearMonthDate = '<?= date("Y-m-d"); ?>';
    var appointmentTimeTemplateUrl = '<?= URL::to(['@appointmentTimeTemplate']) ?>'
    var appointmentTimeTemplateListUrl = '<?= URL::to(['@appointmentTimeTemplateList']) ?>';
    var occupationList = <?= json_encode(User::$getOccuption, true); ?>;
    var addScheduleConfUrl = '<?= Url::to(['@scheduleSchedulingAddScheduling']); ?>';
    var scheduleSchedulingIndex = '<?= Url::to(['@scheduleSchedulingIndex']); ?>';
    var entrance = '2';
    var scheduleOpt =<?= json_encode($schedule, true) ?>;
    require([baseUrl + "/public/plugins/easyhinmodal/easyhinmodal.js"], function (easyhinmodal) {
        window.easyhinModal = easyhinmodal;
    });
    require(["<?= $baseUrl ?>" + "/public/js/make_appointment/timeConfig.js?v=" + '<?= $versionNumber ?>'], function (main) {
        main.init();
        window.main = main;
    });

</script>
<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
