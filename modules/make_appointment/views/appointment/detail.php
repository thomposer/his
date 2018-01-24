<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Json;
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
$tabArray[]=['title' => '预约管理', 'url' => Url::to(['@make_appointmentAppointmentAppointmentDetail', 'type' => 3]), 'type' => 3,'icon_img' => $public_img_path . 'make_appointment/tab_order.png'];
if(in_array(1, $appointment_type)){
    $tabArray[]=['title' => '医生预约时间设置', 'url' => Url::to(['@make_appointmentAppointmentTimeConfig', 'type' => 4]), 'type' => 4,'icon_img' => $public_img_path . 'make_appointment/tab_setting.png'];
}
if(in_array(2, $appointment_type)){
    $tabArray[]=['title' => '科室预约设置', 'url' => Url::to(['@make_appointmentAppointmentRoomConfig', 'type' => 5]), 'type' => 5,'icon_img' => $public_img_path . 'make_appointment/tab_setting.png'];
}

$tabData = [
    'titleData' => $tabArray,
    'activeData' => [
        'type' => 3
    ]
];
$params = [
    'searchName' => 'appointment',
    'statusName' => 'type',
    'buttons' => [
        [
            'title' => '人数统计',
            'statusCode' => 0,
            'url' =>  Url::to(['@make_appointmentAppointmentAppointmentDetail'])
        ],
        [
            'title' => '患者列表',
            'statusCode' => 2,
            'url' => Url::to(['@make_appointmentAppointmentList'])
        ],
        [
            'title' => '预约详情',
            'statusCode' => 1,
            'url' => Url::to(['@make_appointmentAppointmentDetail'])
        ]
    ]
];
//$timeLine=json_encode([]);
if(!$timeLine){
    $timeLine=json_encode([]);
}
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css')?>
<?php AppAsset::addCss($this, '@web/public/plugins/easyhincalendar/easyhincalendar.css')?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css')?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/appointmentConfig.css')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content');?>

<div class="appointment-index col-xs-12">
    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>

    <div class = 'row search-margin'>
        <div class = 'col-sm-5 col-md-5'>
            <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/create', $this->params['permList'])):?>
                <?= Html::a("<i class='fa fa-plus'></i>新增预约", ['create'], ['class' => 'btn btn-default font-body2','data-pjax' => 0]) ?>
            <?php endif?>
            <?php  if(isset($this->params['permList']['role'])||in_array($this->params['requestModuleController'].'/list', $this->params['permList'])):?>
                <!--           //加载日历表格button-->
                <?= $this->render(Yii::getAlias('@searchStatusSkip'),$params) ?>
            <?php endif?>

        </div>

    </div>


        <div id="calendar" class="no-radius">

            <!-- </div> -->

        </div>


    <?php $this->endBlock();?>
    <?php $this->beginBlock('renderJs');?>
    <script type="text/javascript">
        var  appointDoctorConfig = '<?= Url::to(['@apiAppointmentDoctorConfig']); ?>';
        var  viewAppointmentCreatbyDoctor = '<?= Url::to(['@apiAppointmentCreatbyDoctor']); ?>';
        var  baseUrl = '<?= $baseUrl ?>';
        var  doctorInfo= <?=  Json::encode($doctorInfo,JSON_ERROR_NONE); ?>;
		var  createUrl = '<?= Url::to(['@make_appointmentAppointmentCreate']) ?>';
        var  timeLine=<?=  $timeLine; ?>;
        var nowYearMonthDate = '<?= date("Y-m-d"); ?>';
        $(".btn-group-right").find("i").remove();
        $(".btn-group-center").find("i").remove();
        $(".btn-group-left").find("i").remove();
		
        require(["<?= $baseUrl ?>"+"/public/js/make_appointment/appointmentdoctorconfig.js?v="+'<?= $versionNumber ?>'],function(main){
            main.init();
        });
    </script>
    <?php $this->endBlock();?>
    <?php AutoLayout::end();?>

