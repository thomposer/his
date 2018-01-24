<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Json;
use app\modules\outpatient\models\DentalHistoryRelation;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
use app\modules\spot\models\RecipeList;
use johnitvn\ajaxcrud\CrudAsset;
use app\modules\charge\models\ChargeInfo;
use yii\helpers\Url;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\outpatient\models\ChildExaminationAssessment;
use app\modules\outpatient\models\ChildExaminationCheck;
use app\modules\spot\models\CureList;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\RecipeTemplate;
use kartik\file\FileInputAsset;
use app\modules\spot_set\models\ClinicCure;
use app\modules\report\models\Report;
FileInputAsset::register($this)->addLanguage(Yii::$app->language, '', 'js/locales');
$this->title = '接诊';
$this->params['breadcrumbs'][] = ['label' => '门诊', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
CrudAsset::register($this);
$has_save=Yii::$app->request->get('has_save');
$recordStatus = Yii::$app->request->get('recordType');
$action = 'outpatient';
$firstCheckWeightEdit = !($patientOtherInfo['firstCheckCount'] && $patientOtherInfo['weightkg']);
$orthodonticsFirstStatus = 0;
if(Report::$orthodonticsFirst == $recordType){
    $orthodonticsFirstStatus = $orthodonticsFirst->getModel('firstRecord')->id?1:0;
}else if(Report::$orthodonticsReturnvisit == $recordType){
    $orthodonticsReturnvisitHasSave = $orthodonticsReturnvisit->id ? 1 : 2;//1-已保存 2未保存
    $apiGetOrthodonticsReturnvisitRecord = Url::to(['@apiGetOrthodonticsReturnvisitRecord']);
}
?>

<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>

<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/patient_info.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/form.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/preview.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/highRisk.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/check/print.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/commonPrint.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/bootstrap/bootstrap-treeview.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/print.css') ?>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('content') ?>
<div class = 'col-xs-2 col-sm-2 col-md-2' id = 'outpatient-patient-info'>

</div>
<div class="outpatient-update col-xs-10 col-sm-10 col-md-10">
    <div class = "box">

        <div class="box-header with-border">
            <span class='left-title'><?= Html::encode($this->title) ?></span>
            <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/end', $this->params['permList'])): ?>
                <?php if (!empty($chargeInfoList)): ?>
                    <?= Html::tag('button', Html::tag('span', '', ['class' => 'fa fa-check outpatient-check']) . '结束就诊', ['class' => 'end-outpatient btn-disabled disabled']) ?>
                <?php else: ?>
                    <?= Html::a(Html::tag('span', '', ['class' => 'fa fa-check outpatient-check']) . '结束就诊', '#', ['class' => 'end-outpatient', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'normal', 'data-url' => Url::to(['end', 'id' => Yii::$app->request->get('id')])]) ?>
                <?php endif; ?>
            <?php endif; ?>
            <?= Html::a(Html::tag('span', '', ['class' => 'fa fa-dollar blue outpatient-dollar']) . '预览费用', '#', ['class' => 'dollar-outpatient', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large', 'data-url' => Url::to(['view', 'id' => Yii::$app->request->get('id')])]) ?>
            <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/select-package', $this->params['permList'])): ?>
                <?php if(!$firstCheckWeightEdit): ?>
                    <?= Html::a('选择医嘱模板/套餐', '#', ['class' => 'select-package', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'normal', 'data-url' => Url::to(['select-package', 'id' => Yii::$app->request->get('id')])]) ?>
                <?php else: ?>
                    <?= Html::tag('span','选择医嘱模板/套餐', ['class' => 'select-package', 'data-value' => 'false', 'style' => 'cursor:pointer']) ?>
                <?php endif; ?>
            <?php endif; ?>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', ['index'], ['class' => 'right-cancel']) ?>
        </div>
        <div class = "box-body">    

            <?=
            $this->render('_form', [
                'model' => $model,
                'childMultiModel' => $childMultiModel,
                
                'hasTemplateCase' => $hasTemplateCase,
               
                'medicalFile' => $medicalFile,
                'triageInfo' => $triageInfo,
                
                'dentalHistory' => $dentalHistory,
                'dentalHistoryRelation' => $dentalHistoryRelation,
                'recordType' => $recordType,
                'firstCheckDataProvider' => $firstCheckDataProvider,
                'patientOtherInfo' => $patientOtherInfo,
                'reportResult'  => $reportResult,
                'inspectBackStatus' => $inspectBackStatus,
                //正畸
                'orthodonticsReturnvisit' => $orthodonticsReturnvisit,
                'orthodonticsFirst' => $orthodonticsFirst
            ])
            ?>
        </div>
    </div>
</div>

<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<?php AppAsset::addScript($this, '@web/public/plugins/select2/select2.full.min.js')?>
<?php AppAsset::addScript($this, '@web/public/plugins/select2/i18n/zh-CN.js')?>
<?php AppAsset::addScript($this, '@web/public/js/lib/html2canvas.min.js')?>
<div id="teeth_img_print" class="common-print-container" style="display: none;"></div>
<script type="text/javascript">
    var action = '<?= $action ?>';
    var baseUrl = '<?= $baseUrl ?>';
    var triageInfo = <?= json_encode($triageInfo, true); ?>;
    var defaultUsed = <?= Json::encode(RecipeList::$getDefaultUsed, JSON_ERROR_NONE) ?>;
    var defaultDosageForm = <?= Json::encode(RecipeList::$getType, JSON_ERROR_NONE) ?>;
    var unit = <?= Json::encode(RecipeList::$getUnit, JSON_ERROR_NONE) ?>;
    var defaultFrequency = <?= Json::encode(RecipeList::$getDefaultConsumption, JSON_ERROR_NONE) ?>;
    var defaultUnit = <?= Json::encode(RecipeList::$getUnit, JSON_ERROR_NONE) ?>;
    var dosage_form = <?= Json::encode(RecipeList::$getType, JSON_ERROR_NONE) ?>;
    var defaultAddress = <?= Json::encode(RecipeList::$getAddress, JSON_ERROR_NONE) ?>;
    var outpatientUrl = '<?= Url::to(['@outpatientOutpatientUpdate', 'id' => $id]) ?>';
    var hasTemplateCase = <?= $hasTemplateCase ?>;
    var has_save = <?= $has_save?1:2 ?>;
    var state = '<?= $recordType == 1 ?$model->getModel('triageInfo')->state:'' ?>';//门诊修改状态
    var childCheckStatus = <?= $triageInfo['child_check_status'] ?>;//儿童体检修改状态
    var dentalRecordStatus = <?= ($dentalHistory->id || $orthodonticsFirstStatus)? 1 : 0 ?>;
    var getSummary = <?= Json::encode(ChildExaminationAssessment::$getSummary,JSON_ERROR_NONE) ?>;
    var getCommunicate = <?= Json::encode(ChildExaminationAssessment::$getCommunicate,JSON_ERROR_NONE) ?>;
    var getCureRecord  = <?= Json::encode(CureRecord::$getCureResult,JSON_ERROR_NONE) ?>;
    var getStatusOtherDesc = <?= Json::encode(RecipeRecord::$getStatusOtherDesc,JSON_ERROR_NONE) ?>;
    var getType = <?= Json::encode(ChildExaminationCheck::$getType,JSON_ERROR_NONE) ?>;
    var record_id= '<?= $id ?>';
    var childTemplate = <?= $recordType == 2?json_encode($childTemplate, true):"''" ?>;
    var getDoctorRecordData = '<?= Url::to(['@apiOutpatientGetDoctorRecordData']) ?>'; //病历打印接口
    var findRecordPrinkInfoUrl = '<?= Url::to(['@outpatientOutpatientRecordPrinkInfo']) ?>'; //病历打印接口
    var findTeethPrintInfoUrl = '<?= Url::to(['@outpatientOutpatientTeethPrint']) ?>'; //病历打印接口
    var getChildInfoData = '<?= Url::to(['@apiOutpatientGetChildInfoData']) ?>'; //病历打印接口
    var childExaminationPrinkInfoUrl = '<?= Url::to(['@outpatientOutpatientChildPrinkInfo']) ?>';  //儿童检查打印接口
    var recipePrintUrl = '<?= Url::to(['@apiOutpatientRecipePrint']) ?>';  //处方打印接口
    var curePrinkInfoUrl='<?= Url::to(['@outpatientOutpatientCurePrinkInfo']) ?>';  //治疗打印接口
    var materialPrinkInfoUrl = '<?= Url::to(['@outpatientOutpatientMaterialPrinkInfo','id' => $id]) ?>';     //非处方其他打印接口
    var consumablesPrinkInfoUrl = '<?= Url::to(['@outpatientOutpatientConsumablesPrinkInfo','id' => $id]) ?>';     //医疗耗材打印接口
    var reportInspectPrinkInfoUrl = '<?= Url::to(['@outpatientOutpatientReportInspectPrinkInfo']) ?>';  //实验室检查打印接口
    var reportCheckPrinkInfoUrl = '<?= Url::to(['@outpatientOutpatientReportCheckPrinkInfo']) ?>';  //实验室检查打印接口
    var inspectApplicationPrintUrl = '<?= Url::to(['@apiOutpatientInspectApplicationPrint']) ?>';  //实验室检查打印接口
    var checkApplicationPrintUrl = '<?= Url::to(['@apiOutpatientCheckApplicationPrint']) ?>';  //影像学检查打印接口
    var nursingRecordPrinkInfoUrl = '<?= Url::to(['@apiOutpatientNursingRecordPrinkInfo']) ?>';  //护理记录打印接口
    var itemUrl = '<?= Url::to(['@apiMedicineDescriptionItem']) ?>';//查看用药指南url
    var skinTestStatusList = <?= Json::encode(RecipeRecord::$getSkinTestStatus) ?>;//皮试状态
    var apiGrowthViewUrl = '<?= Url::to(['@apiGrowthView','id' => $triageInfo['patient_id'],'diagnosisTime' =>$triageInfo['diagnosis_time'] ]) ?>';
    skinTestList = <?= json_encode(array_column(ClinicCure::getCureList(null,['b.type' => 1]), 'name','id'),true); ?>;
    var getRecipeTemplateInfo = '<?= Url::to(['@apiOutpatientGetRecipeTemplateInfo']) ?>';
    var getInspectTemplateInfo = '<?= Url::to(['@apiOutpatientGetInspectTemplateInfo']) ?>';
    var getCheckTemplateInfo = '<?= Url::to(['@apiOutpatientGetCheckTemplateInfo']) ?>';
    var getCureTemplateInfo = '<?= Url::to(['@apiOutpatientGetCureTemplateInfo']) ?>';
    var recipeTemplateType = <?= Json::encode(RecipeTemplate::$getType) ?>;
    var recordType = '<?= $recordType ?>';
    var getCheckCodeList = '<?= Url::to(['@apiOutpatientGetCheckCodeList']) ?>';
    var outpatientDoctorRecipeList = '<?= Url::to(['@apiOutpatientDoctorRecipeList']) ?>';
	var outpatientDoctorCheckInspectList = '<?= Url::to(['@apioutpatientDoctorCheckInspectList'])?>';
    var allergy = <?= json_encode($allergy, true); ?>;
    var orthodonticsReturnvisitHasSave = '<?= $orthodonticsReturnvisitHasSave ?>';
    var getOrthodonticsReturnvisitRecord = '<?= $apiGetOrthodonticsReturnvisitRecord ?>'; //正畸复诊病历打印接口
    var dentalDiseaseType = <?= Json::encode(DentalHistoryRelation::$dentalDisease) ?>;
    
    var inspectSearchUrl = '<?= Url::to(['@apiSearchPackageTemplateInspect']) ?>';
    var checkSearchUrl = '<?= Url::to(['@apiSearchPackageTemplateCheck']) ?>';
    var cureSearchUrl = '<?= Url::to(['@apiSearchPackageTemplateCure']) ?>';
    var recipeSearchUrl = '<?= Url::to(['@apiSearchPackageTemplateRecipe']) ?>';
	var consumablesSearchUrl = '<?= Url::to(['@apiSearchClinicConsumables']) ?>';
	var materialSearchUrl = '<?= Url::to(['@apiSearchClinicMaterial']) ?>';
	
	cureRecordUrl = $('a[href=\"#cure\"]').attr('data-url');
    reportRecordUrl = $('a[href=\"#report\"]').attr('data-url');
	
    
    require(["<?= $baseUrl ?>" + "/public/js/outpatient/form.js"], function (main) {
        main.init();
        window.main = main;
    });
</script>
<?php $this->endBlock() ?>
<?php AutoLayout::end() ?>

