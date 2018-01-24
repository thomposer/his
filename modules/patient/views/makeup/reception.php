<?php

use yii\bootstrap\Tabs;
use rkit\yii2\plugins\ajaxform\Asset;
use app\modules\charge\models\ChargeInfo;
use yii\helpers\Json;
use app\modules\spot\models\RecipeList;
use app\modules\outpatient\models\RecipeRecord;

$defaultUsed = Json::encode(RecipeList::$getDefaultUsed, JSON_ERROR_NONE);
$defaultFrequency = Json::encode(RecipeList::$getDefaultConsumption, JSON_ERROR_NONE);
$defaultDosageForm = Json::encode(RecipeList::$getType, JSON_ERROR_NONE);
$defaultUnit = Json::encode(RecipeList::$getUnit, JSON_ERROR_NONE);
$recipeListJson = json_encode($recipeList, true);
$inspectListJson = json_encode($inspectList, true);
$checkListJson = json_encode($checkList, true);
$defaultAddress = Json::encode(RecipeList::$getAddress, JSON_ERROR_NONE);
$cureListJson = json_encode($cureList, true);

array_unshift(RecipeList::$getDefaultConsumption, '请选择');
array_unshift(RecipeList::$getDefaultUsed, '请选择');
array_unshift(RecipeList::$getDoseUnit, '请选择');

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
/* @var $form yii\widgets\ActiveForm */
Asset::register($this);
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");


$permissonType = [];
if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientInspectRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$inspectType] = true;
    $items[] = [
        'label' => '实验室检查',
        'options' => ['id' => 'labCheck'],
    ];
}
if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientCheckRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$checkType] = true;
    $items[] = [
        'label' => '影像学检查',
        'options' => ['id' => 'auxiliary']
    ];
}
if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientCureRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$cureType] = true;
    $items[] = [
        'label' => '治疗',
        'options' => ['id' => 'cure']
    ];
}
if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientRecipeRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$recipeType] = true;
    $items[] = [
        'label' => '处方',
        'options' => ['id' => 'recipe']
    ];
}
?>

<div class="ump-reception">

    <?=
    Tabs::widget([
        'renderTabContent' => false,
        'navType' => ' nav-tabs outpatient-form',
        'items' => $items
    ]);
    ?>
    <div class = 'tab-content'>
        <?php if (isset($permissonType[ChargeInfo::$inspectType])): ?>
            <div id = 'labCheck' class="tab-pane active">
                <?=
                $this->render('_inspectRecordForm', [
                    'model' => $inspectRecordModel,
                    'inspectRecordDataProvider' => $inspectRecordDataProvider,
                    'inspectList' => $inspectList,
                    'id' => $id,
                    'patientId' => $patientId,
                    'doctorId' => $doctorId,
                ])
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($permissonType[ChargeInfo::$checkType])): ?>
            <div id = 'auxiliary' class="tab-pane">
                <?=
                $this->render('_checkRecordForm', [
                    'model' => $checkRecordModel,
                    'checkRecordDataProvider' => $checkRecordDataProvider,
                    'checkList' => $checkList,
                    'id' => $id,
                    'patientId' => $patientId,
                    'doctorId' => $doctorId,
                ])
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($permissonType[ChargeInfo::$cureType])): ?>
            <div id = 'cure' class="tab-pane">
                <?=
                $this->render('_cureRecordForm', [
                    'model' => $cureRecordModel,
                    'cureRecordDataProvider' => $cureRecordDataProvider,
                    'cureList' => $cureList,
                    'id' => $id,
                    'patientId' => $patientId,
                    'doctorId' => $doctorId,
                ])
                ?>
            </div> 
        <?php endif; ?>
        <?php if (isset($permissonType[ChargeInfo::$recipeType])): ?>
            <div id = 'recipe' class="tab-pane">
                <?=
                $this->render('_recipeRecordForm', [
                    'model' => $recipeRecordModel,
                    'recipeRecordDataProvider' => $recipeRecordDataProvider,
                    'recipeList' => $recipeList,
                    'id' => $id,
                    'patientId' => $patientId,
                    'doctorId' => $doctorId,
                ])
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$skinTestStatusList = Json::encode(RecipeRecord::$getSkinTestStatus);
$unit = Json::encode(RecipeList::$getUnit);
$js = <<<JS
   var baseUrl = '$baseUrl';
   var recipeList = $recipeListJson;
   var inspectList = $inspectListJson;
   var checkList = $checkListJson;
   var defaultUsed =  $defaultUsed ;
    var defaultFrequency = $defaultFrequency ;
    var dosage_form = $defaultDosageForm;
    var defaultUnit = $defaultUnit ;
    var defaultAddress =  $defaultAddress ;
    var cureList =  $cureListJson;
    var skinTestStatusList = $skinTestStatusList;//皮试状态
    var unit = $unit;
   require(["$baseUrl/public/js/patient/ump_record.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
