<?php
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
use dosamigos\datetimepicker\DateTimePickerAsset;
DateTimePickerAsset::register($this)->js[] = 'js/locales/bootstrap-datetimepicker.zh-CN.js';
CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\modules\make_appointment\models\search\AppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '预约';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';
$versionNumber = Yii::getAlias("@versionNumber");


$tabArray=array();
$tabArray[]=['title' => '预约管理', 'url' => Url::to(['@make_appointmentAppointmentIndex', 'type' => 3]), 'type' => 3,'icon_img' => $public_img_path . 'make_appointment/tab_order.png'];
if(in_array(1, $appointment_type)){
    $tabArray[]=['title' => '医生预约设置', 'url' => Url::to(['@make_appointmentAppointmenDoctortConfig', 'type' => 4]), 'type' => 4,'icon_img' => $public_img_path . 'make_appointment/tab_setting.png'];
}
if(in_array(2, $appointment_type)){
    $tabArray[]=['title' => '科室预约设置', 'url' => Url::to(['@make_appointmentAppointmentRoomConfig', 'type' => 5]), 'type' => 5,'icon_img' => $public_img_path . 'make_appointment/tab_setting.png'];
}
$tabData = [
    'titleData' => $tabArray,
    'activeData' => [
        'type' => 5
    ]
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/plugins/easyhincalendar/easyhincalendar.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/make_appointment/appointmentConfig.css')?>
    <?php AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="appointment-index col-xs-12">
    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>
	<!-- <div class = "box"> -->
	<div id="calendars" class="no-radius">

		<!-- </div> -->

	</div>
    <div id = 'appointmentConfigModal'>
    
    </div>
</div>

<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs');?>
<script type="text/javascript">
    var apiAppointmentAppointmentConfig = '<?= Url::to(['@apiAppointmentAppointmentConfig']); ?>';
    var makeAppointmentSaveConfig = '<?= Url::to(['@make_appointmentAppointmentSaveConfig']); ?>';
    var copyWeekConfig = '<?= Url::to(['@make_appointmentAppointmentCopyConfig']); ?>';
    var referrer = '<?= Yii::$app->request->referrer ?>';//返回上一页路径
    var baseUrl = '<?= $baseUrl ?>';
    var size = 'modal-lg';//大模型框
    var nowYearMonthDate = '<?= date("Y-m-d"); ?>';
    var timeConfig = <?= json_encode($timeConfig)?>;
    require([baseUrl+"/public/plugins/easyhinmodal/easyhinmodal.js"],function(easyhinmodal){
        
        window.easyhinModal = easyhinmodal;
    });
    require(["<?= $baseUrl ?>"+"/public/js/make_appointment/appointmentset.js?v="+'<?= $versionNumber ?>'],function(main){
        main.init();
        window.main = main;
    });
</script>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
