<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\Appointment */

$this->title = '新增预约';
$this->params['breadcrumbs'][] = ['label' => '预约', 'url' => ['appointment-detail']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>
    <?php AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css') ?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/upload.css')?>
    <?php AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/make_appointment/create.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="appointment-create col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Url::to(['@make_appointmentAppointmentAppointmentDetail']),['class' => 'right-cancel right-cancel-confirmed']) ?>
    </div>
        <div class = "box-body">    

            <?= $this->render('_form', [
                'model' => $model,
//                 'departmentInfo' => $departmentInfo,
                'doctorInfo' => $doctorInfo,
                'hasAppointmentDoctor' => $hasAppointmentDoctor,
                'onlyAppointmentDoctor' => $onlyAppointmentDoctor,
                'type' => $type,
                
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
   <script type="text/javascript">
 		var baseUrl = '<?= $baseUrl ?>';
   		var uploadUrl = '<?= Url::to(['@manageSitesUpload']); ?>';
   		var getPatients = '<?= Url::to(['@patientPatientGetPatients']); ?>';
   		var getIphone = '<?= Url::to(['@apiPatientGetIphone']); ?>';
   		var createUrl = '<?= Url::to(['@make_appointmentAppointmentCreate']) ?>';
   		var apiAppointmentDoctorInfo = '<?= Url::to(['@apiAppointmentDoctorInfo']) ?>';
   		var manageSitesAppointmentTime = '<?= Url::to(['@manageSitesAppointmentTime'])?>';
   		var apiAppointmentDoctorTime = '<?= Url::to(['@apiAppointmentDoctorTime']) ?>';
   		var apiAppointmentGetAppointmentType = '<?= Url::to(['@apiAppointmentGetAppointmentType']) ?>';
   		var action = '<?= Yii::$app->controller->action->id ?>';
   		var doctorInfo = <?= Json::encode($doctorInfo,JSON_ERROR_NONE); ?>;
   		var hasAppointmentDoctor = '<?= $hasAppointmentDoctor ?>';
   		var onlyAppointmentDoctor = '<?= $onlyAppointmentDoctor ?>';
   		
   		var userId = '<?= $model->errors?$model->doctor_id:0 ?>';
   		var doctorName = '';
   		var spotTypeId = '<?= $model->errors?$model->type:0 ?>';
   		var spotTypeName = '';
   		var error = '<?= $model->errors?1:0 ?>';//后台验证错误状态
   		var date = '<?= $model->time?date('Y-m-d',$model->time):"" ?>';
   		var dateTimeText = '<?= $model->time?date('H:i',$model->time):"" ?>';
   		var dateTimeValue = '<?= $model->time ?>';
   		var deleteStatus = '';
		require(["<?= $baseUrl ?>"+"/public/js/make_appointment/form.js?v="+ '<?= $versionNumber ?>'],function(main){
			main.init();
		});
	</script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>