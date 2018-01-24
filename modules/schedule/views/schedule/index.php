<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Json;
use app\modules\user\models\User;
use dosamigos\datetimepicker\DateTimePickerAsset;
DateTimePickerAsset::register($this)->js[] = 'js/locales/bootstrap-datetimepicker.zh-CN.js';
/* @var $this yii\web\View */
/* @var $searchModel app\modules\schedule\models\search\SchedulingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '排班';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/easyhincalendar/easyhincalendar.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/appointmentConfig.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/reception/index.css') ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<?php //echo $this->render('_scheduleSearch', ['model' => $searchModel,]); ?>
<div class="scheduling-index col-xs-12">
    <div class = "box">
        <div id="schedule_grid">

        </div>
    </div>
</div>


<?php $this->endBlock(); ?>
<?php $this->beginBlock('renderJs'); ?>
<script type="text/javascript">

    var baseUrl = '<?= $baseUrl ?>';
    var apiSchedulingIndex = '<?= Url::to(['@apiSchedulingIndex']); ?>';
    var apiSchedulingScheduleConf = '<?= Url::to(['@apiSchedulingScheduleConf']); ?>';
    var scheduleSchedulingAddScheduling = '<?= Url::to(['@scheduleSchedulingAddScheduling']); ?>';
    var size = 'modal-normal';
    var nowYearMonthDate = '<?= date("Y-m-d"); ?>';
    var  scheduleOpt=<?= json_encode($schedule,true)?>;
    var occupationList = <?=  json_encode(User::$getOccuption,true); ?>;
    var entrance = '1';
    require([baseUrl+"/public/plugins/easyhinmodal/easyhinmodal.js"],function(easyhinmodal){
        window.easyhinModal = easyhinmodal;
    });
    require(["<?= $baseUrl ?>" + "/public/js/schedule/schedule.js?v="+'<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
