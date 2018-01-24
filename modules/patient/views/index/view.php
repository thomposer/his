<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Json;
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;

$this->title = '患者信息';
$this->params['breadcrumbs'][] = ['label' => '病历库', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
CrudAsset::register($this);
?>

<?php
 AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']);
 $this->beginBlock('renderCss');
 AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css');
 AppAsset::addCss($this, '@web/public/css/patient/patient_info.css');
 AppAsset::addCss($this, '@web/public/css/lib/city-picker.css');
 AppAsset::addCss($this, '@web/public/css/lib/tab.css');
 AppAsset::addCss($this, '@web/public/css/patient/form.css');
 AppAsset::addCss($this, '@web/public/css/patient/follow.css');
 AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css');
 AppAsset::addCss($this, '@web/public/css/lib/upload.css');
 AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css');
 AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css');
 AppAsset::addCss($this, '@web/public/css/user/manage.css');
 AppAsset::addCss($this, '@web/public/css/lib/search.css');
 $this->endBlock(); ?>

<?php $this->beginBlock('content') ?>

<div class="outpatient-update col-xs-12 col-sm-12 col-md-12">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', ['index'], ['class' => 'right-cancel']) ?>
        </div>

        <div class="container-fluid" id = 'outpatient-patient-info'>
        </div>

        <div class = "box-body">   

            <?=
            $this->render('_form', [
                'model' => $model,
                'allergy_list' => $allergy_list,
                'familyData' => $familyData,
            ])
            ?>
        </div>
    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<?php AppAsset::addScript($this, '@web/public/js//lib/city-picker.data.js') ?>
<?php AppAsset::addScript($this, '@web/public/js/lib/city-picker.js') ?>
<!--<div id="growth_print" class="common-print-container" style="display: none;"></div>-->
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var uploadUrl = '<?= Url::to(['@manageSitesUpload']); ?>';
    var triageInfo = <?= json_encode($patientInfo, true); ?>;
    var patientId = '<?= $model->getModel('patient')->id ?>';
    var cdnHost = '<?= Yii::$app->params['cdnHost'] ?>';
    var apiGrowthViewUrl = '<?= Url::to(['@apiGrowthView', 'id' => $model->getModel('patient')->id]) ?>';
    var allergy = <?= json_encode($allergy, true); ?>;
    require(["<?= $baseUrl ?>" + "/public/js/patient/form.js?v=" + '<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock() ?>
<?php AutoLayout::end() ?>

