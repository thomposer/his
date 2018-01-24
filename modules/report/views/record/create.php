<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use rkit\yii2\plugins\ajaxform\Asset;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);
Asset::register($this);

$this->title = '新增报到';
$this->params['breadcrumbs'][] = ['label' => '报到', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/user/manage.css') ?>
<?php AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/upload.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/city-picker.css') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/report/form.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class="patient-create col-xs-12">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
<?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['index']), ['class' => 'right-cancel right-cancel-confirmed']) ?>
        </div>
        <div class = "box-body">    

            <?=
            $this->render('_form', [
                'model' => $model,
                'familyInfo' => $familyInfo,
                'doctorInfo' => $doctorInfo,
//                 'departmentInfo' => $departmentInfo,
                'actionUrl'=>  Url::to(['create'])
            ])
            ?>
        </div>
    </div>
</div>

<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<?php AppAsset::addScript($this, '@web/public/js//lib/city-picker.data.js') ?>
<?php AppAsset::addScript($this, '@web/public/js/lib/city-picker.js') ?>
<script type = "text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var uploadUrl = '<?= Url::to(['@manageSitesUpload']); ?>';
    var getPatients = '<?= Url::to(['@patientPatientGetPatients']); ?>';
    var getIphone = '<?= Url::to(['@apiPatientGetIphone']); ?>';
    var createUrl = '<?= Url::to(['@reportRecordCreate']) ?>';
    var apiAppointmentDoctorInfo = '<?= Url::to(['@apiAppointmentDoctorInfo']) ?>';
    var apiAppointmentGetAppointmentType = '<?= Url::to(['@apiAppointmentGetAppointmentType']) ?>';
    var error = '<?= $model->errors ? 1 : 0 ?>';//后台验证错误状态
    var action = '<?= Yii::$app->controller->action->id ?>';
    var deleteStatus = '';
    var userId = '<?= $model->errors ? $model->getModel('report')->doctor_id : 0 ?>';
    var spotTypeId = '<?= $model->errors ? $model->getModel('report')->type : 0 ?>';
    var spotTypeName = '';
    var secondDepartmentId = '<?= $model->errors ? $model->getModel('report')->second_department_id : 0 ?>';
	var secondDepartmentName = '';
	var apiAppointmentGetDoctorDepartment = '<?= Url::to(['@apiAppointmentGetDoctorDepartment']) ?>';
    require(['<?= $baseUrl ?>' + '/public/js/report/record/create.js?v=' + '<?= $versionNumber ?>'], function (main) {
        main.init();
    })
</script>
<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>