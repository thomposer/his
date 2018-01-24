<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
use app\common\Common;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\user\models\User;
use yii\widgets\Pjax;
use yii\helpers\Url;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\RecipeRecord;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '护士工作台';
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$spotId = $_COOKIE['spotId'];
$yesterday = date("Y-m-d", strtotime($date) - 86400);
$nextday = date("Y-m-d", strtotime($date) + 86400);
$choseRoomUrl = Url::to(['@triageTriageChoseroom']);
?>

<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/search.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/triage/triage.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/nurse/nurse.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/check/print.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/nurse/print.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/report/form.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/commonPrint.css') ?>

<?php $this->endBlock() ?>
<?php $this->beginBlock('content'); ?>
<?php /* if($this->beginCache($cacheId,$options)): */ ?>
<div class="col-xs-12">
    <!--    下面是下拉框的最外层DIV-->
    <div class="box box-success">
        <div class="box-body" >
            <div class="nurse-index col-xs-12">

                <div class = 'row search-margin'>
                    <div class = 'urse-search-padding'>
                        <?php echo $this->render('_search', ['model' => $searchModel, 'date' => $date, 'yesterday' => $yesterday, 'nextday' => $nextday]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?php echo $this->render('_choiceDoctorSearch', ['model' => $searchModel, 'nurseDoctorConfigModel' => $nurseDoctorConfigModel, 'allDoctor' => $allDoctor, 'doctorId' => $doctorId, 'docFocusList' => $docFocusList, 'doctorIdArr' => $doctorIdArr]); ?>
                    </div>
                </div>
                <span class="clearfix"></span>
                <?php echo $this->render('_nurseReport', [ 'reportDataProvider' => $reportDataProvider, 'date' => $date, 'reportNum' => $reportNum, 'reportCardInfo' => $reportCardInfo, 'patientOrders' => $patientOrders]); ?>
                <?php echo $this->render('_nurseAppointment', ['appointmentDataProvider' => $appointmentDataProvider, 'date' => $date, 'appointmentNum' => $appointmentNum, 'appointmentCardInfo' => $appointmentCardInfo,]); ?>

                <div id='print-show-none'>
                    <div id='print-view'>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div id='inspect-application-print' class="tab-pane hide"></div>
<div id='recipe-print' class="tab-pane hide"></div>
<div id='cure-print' class="tab-pane hide"></div>
<div id='record-print-view' class="hide"></div>
<div id="child-record-print-view" class="hide"><div id="child-record-print-view-child"></div></div>
    <?php $this->endBlock(); ?>
    <?php $this->beginBlock('renderJs'); ?>
<script type="text/javascript">
    var nurseIndexIndex = '<?= Url::to(['@nurseIndexIndex']) ?>';
    var reportRecordUpdate = '<?= Url::to(['@reportRecordUpdate']) ?>';
    var cdnHost = '<?= Yii::$app->params['cdnHost'] ?>';
    var baseUrl = '<?= $baseUrl ?>';
    var getDoctorRecordData = '<?= Url::to(['@apiOutpatientGetDoctorRecordData']) ?>'; //病历打印接口
    var getChildRecordData = '<?= Url::to(['@apiOutpatientGetChildInfoData']) ?>'; //儿保病历信息打印接口
    var doctorSelectId = <?= json_encode($doctorId, true) ?>;
    var choseRoomUrl = '<?= Url::to(['@triageTriageChoseroom']) ?>';
    var inspectApplicationPrintUrl = '<?= Url::to(['@apiOutpatientInspectApplicationPrint']) ?>';  //实验室检查打印接口
    var inspectReportPrintUrl = '<?= Url::to(['@apiOutpatientInspectReportPrint']) ?>';  //实验室检查打印接口
    var recipePrintUrl = '<?= Url::to(['@apiOutpatientRecipePrint']) ?>';  //处方打印接口
    var curePrintUrl = '<?= Url::to(['@apiOutpatientCurePrint']) ?>';  //治疗打印接口
    var getCureRecord = <?= json_encode(CureRecord::$getCureResult, true) ?>;
    var getStatusOtherDesc = <?= json_encode(RecipeRecord::$getStatusOtherDesc, true) ?>;
    require([baseUrl + "/public/js/nurse/index.js?v=" + '<?= $versionNumber ?>'], function (main) {
        main.init();
    });
    require([baseUrl + '/public/js/report/record/index.js?v=' + '<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock(); ?>
<?php AutoLayout::end(); ?>
