<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Json;
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;

$this->title = '新增补录';
$this->params['breadcrumbs'][] = ['label' => '病历库', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
CrudAsset::register($this);
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/patient_info.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/city-picker.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/tab.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/patient/form.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class="outpatient-update col-xs-12 col-sm-12 col-md-12">
    <div class="box">
        <div class="box-header with-border">
            <span class='left-title'><?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', ['index'], ['class' => 'right-cancel']) ?>
        </div>
        <div class="box-body">

            <?=
            $this->render('_form', [
                'model' => $model,
//                'allergy_list' => $allergy_list,
                'familyData' => $familyData,
                'historyPatientInfo' => $historyPatientInfo,

                'nurseRecordData' => $nurseRecordData,
                'healthEducationData' => $healthEducationData,
                'inspectData' => $inspectData,
                'checkData' => $checkData,
                'cureData' => $cureData,
                'recipeData' => $recipeData,
//                'patientAppointmentInfo' => $patientAppointmentInfo
            ])
            ?>
        </div>
    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<?php AppAsset::addScript($this, '@web/public/js//lib/city-picker.data.js') ?>
<?php AppAsset::addScript($this, '@web/public/js/lib/city-picker.js') ?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var getPatients = '<?= Url::to(['@patientPatientGetPatients']); ?>';
    var getIphone = '<?= Url::to(['@apiPatientGetIphone']); ?>';
    var createUrl = '<?= Url::to(['@patientIndexMakeup']) ?>';
    var triageInfo = <?= Json::encode($patientInfo, JSON_ERROR_NONE); ?>;
    require(["<?= $baseUrl ?>" + "/public/js/patient/ump_form.js?v=" + '<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock() ?>
<?php AutoLayout::end() ?>


