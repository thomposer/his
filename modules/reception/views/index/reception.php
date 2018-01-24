<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\helpers\Json;
use app\modules\user\models\User;
use dosamigos\datetimepicker\DateTimePickerAsset;
DateTimePickerAsset::register($this)->js[] = 'js/locales/bootstrap-datetimepicker.zh-CN.js';
/* @var $this yii\web\View */
/* @var $searchModel app\modules\schedule\models\search\SchedulingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '前台工作台';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$public_img_path = $baseUrl . '/public/img';
$tabData = [
    'titleData' => [
        ['title' => '所有医生预约信息', 'url' => Url::to(['@receptionIndexReception', 'type' => 3]), 'type' => 3, 'icon_img' => $public_img_path . '/tab/tab_order.png'],
        ['title' => '所有人员排班信息', 'url' => Url::to(['@receptionIndexIndex', 'type' => 4]), 'type' => 4, 'icon_img' => $public_img_path . '/tab/tab_paiban.png'],
    ],
    'activeData' => [
        'type' => 3
    ]
];
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/easyhincalendar/easyhincalendar.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/reception/index.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<div class="scheduling-index col-xs-12">


    <?php echo $this->render(Yii::getAlias('@contentTopTab'), ['data' => $tabData]); ?>

    <div class = "box">
        <div id="schedule_grid">

        </div>

    </div>

</div>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
        var apiAppointmentWorkstationIndex = '<?= Url::to(['@apiAppointmentWorkstationIndex']); ?>';
        var apiAppointmentWorkstationConf = '<?= Url::to(['@apiAppointmentWorkstationConf']); ?>';
        var appointmentAdd = '<?= Url::to(['@appointmentAdd']); ?>';
        var nowYearMonthDate = '<?= date("Y-m-d"); ?>';
        var  scheduleOpt=<?= json_encode($schedule,true)?>;
		var occupationList = <?=  json_encode(User::$getOccuption,true); ?>;
        var entrance = '2';
        require(["<?= $baseUrl ?>" + "/public/js/reception/reception-appointment.js?v="+'<?= $versionNumber ?>'], function (main) {
            main.init();
    });
</script>

<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
