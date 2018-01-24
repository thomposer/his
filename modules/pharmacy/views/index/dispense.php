<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Json;
use app\modules\outpatient\models\CureRecord;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
use rkit\yii2\plugins\ajaxform\Asset;
use app\modules\outpatient\models\RecipeRecord;

$this->title = '处方医嘱（发药）';
$this->params['breadcrumbs'][] = ['label' => '药房管理', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
CrudAsset::register($this);
Asset::register($this);
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/patient_info.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/highRisk.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/check/print.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/lib/commonPrint.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/pharmacy/pharmacy.css') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class = 'col-xs-2 col-sm-2 col-md-2' id = 'outpatient-patient-info'>

</div>
<div class="col-xs-10 col-sm-10 col-md-10">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Url::to(['index']), ['class' => 'right-cancel']) ?>   
        </div>
        <div class = "box-body">    
            <?=
            $this->render('_form', [
                'model' => $model,
                'dataProvider' => $recipeRecordDataProvider,
                'status' => $status,
                'recipePrintData' => $recipePrintData,
                'recipeData' => $recipeData,
            ])
            ?>
        </div>
        <div class = "box-body hide">
            <?php
//            $this->render('_printPharmacyForm', [
//                'model' => $model,
//                'dataProvider' => $recipeRecordDataProvider,
//                'status' => $status,
//                'triageInfo' => $triageInfo,
//                'repiceInfo' => $repiceInfo,
//                'soptInfo' => $soptInfo,
//                'patientRecordInfo' => $patientRecordInfo,
//                'recipePrintData' => $recipePrintData,
//                'spotConfig' => $spotConfig
//            ])
            ?>
        </div>
    </div>
</div>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<?php $recipeData = json_encode($recipeData, true); ?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var status = '<?= $status ?>';
    var triageInfo = <?= json_encode($triageInfo, true); ?>;
    var recipePrintData = <?= Json::encode($recipePrintData, JSON_ERROR_NONE); ?>;
//    var completeUrl = '<? Url::to(['@pharmacyIndexComplete']); ?>';
    var indexUrl = '<?= Url::to(['@pharmacyIndexIndex']); ?>';
    var completeUrl = '<?= Url::to(['@pharmacyIndexComplete']); ?>';
    var recordId = '<?= $model->record_id ?>';
    var dispensingUrl = '<?= Url::to(['@pharmacyIndexComplete']) ?>';
    var preserveUrl = '<?= Url::to(['@pharmacyIndexPreserve']) ?>';//保存用药须知
    var dispenseUrl = '<?= Url::to(['@pharmacyIndexDispense']) ?>';//返回发药首页
    var recipeData = <?= $recipeData ?>;
    var allergy = <?= json_encode($allergy, true); ?>;
    var getCureResult = <?= json_encode(CureRecord::$getCureResult) ?>;
    var getStatusOtherDesc = <?= Json::encode(RecipeRecord::$getStatusOtherDesc, JSON_ERROR_NONE) ?>;
    var recipePrintUrl = '<?= Url::to(['@apiOutpatientRecipePrint']) ?>';  //处方打印接口
    require([baseUrl + "/public/js/pharmacy/dispense.js"], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>