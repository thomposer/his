<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use rkit\yii2\plugins\ajaxform\Asset;
use app\modules\charge\models\ChargeInfo;
use app\modules\report\models\Report;
use yii\helpers\Url;
use kartik\tabs\TabsX;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
/* @var $form yii\widgets\ActiveForm */
Asset::register($this);
$queryParams = Yii::$app->request->queryParams;
$permissonType = [];
if (2 == $recordType) {
    $items[] = [
        'label' => '儿童保健档案',
        'options' => ['id' => 'childCheck'],
        'active' => true
    ];
} else { 
    $items[] = [
        'label' => '病历',
        'options' => ['id' => 'record'],
        'active' => true
    ];
}
if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientInspectRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$inspectType] = true;
    $items[] = [
        'label' => '实验室检查',
        'options' => ['id' => 'labCheck'],
        'linkOptions' => ['data-url' => Url::to(['@apiOutpatientGetInspectRecord','id' => $queryParams['id']])]
    ];
}
if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientCheckRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$checkType] = true;
    $items[] = [
        'label' => '影像学检查',
        'options' => ['id' => 'auxiliary'],
        'linkOptions' => ['data-url' => Url::to(['@apiOutpatientGetCheckRecord','id' => $queryParams['id']])]
    ];
}
if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientCureRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$cureType] = true;
    $items[] = [
        'label' => '治疗',
        'options' => ['id' => 'cure'],
        'linkOptions' => ['data-url' => Url::to(['@apiOutpatientGetCureRecord','id' => $queryParams['id']])]
        
    ];
}
if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientRecipeRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$recipeType] = true;
    $items[] = [
        'label' => '处方',
        'options' => ['id' => 'recipe'],
        'linkOptions' => ['data-url' => Url::to(['@apiOutpatientGetRecipeRecord','id' => $queryParams['id']])]
        
    ];
}

if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientConsumablesRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$consumablesType] = true;
    $items[] = [
        'label' => '医疗耗材',
        'options' => ['id' => 'consumables'],
        'linkOptions' => ['data-url' => Url::to(['@apiOutpatientGetConsumablesRecord','id' => $queryParams['id']])]
        
    ];
}

if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientMaterialRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$materialType] = true;
    $items[] = [
        'label' => '其他',
        'options' => ['id' => 'material'],
        'linkOptions' => ['data-url' => Url::to(['@apiOutpatientGetMaterialRecord','id' => $queryParams['id']])]
        
    ];
}

// if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@outpatientOutpatientReportRecord'), $this->params['permList'])) {
    $permissonType[ChargeInfo::$reportType] = true;
    $items[] = [
        'label' => '报告',
        'options' => ['id' => 'report'],
        'linkOptions' => ['data-url' => Url::to(['@apiOutpatientGetReportRecord','id' => $queryParams['id']])]
        
    ];
// }
?>

<div class="outpatient-form">

    <?=
    TabsX::widget([
        'renderTabContent' => false,
        'navType' => ' outpatient-tab nav-tabs outpatient-form',
        'items' => $items
    ]);
    ?>
    <div class = 'tab-content'>
        <?php if(4 == $recordType || 5 == $recordType): ?>
            <div id = 'record' class="tab-pane active">
                <?=
                    $this->render('_dentalRecordForm', [
                        'dentalHistory' => $dentalHistory,
                        'dentalHistoryRelation' => $dentalHistoryRelation,
                        'firstCheckDataProvider' => $firstCheckDataProvider,
                    ])
                ?>
            </div>
        <?php elseif(2 == $recordType): ?>
            <div id = 'childCheck' class="tab-pane">
                <?=
                $this->render('_childForm', [
                    'childMultiModel' => $childMultiModel,
                    'triageInfo' => $triageInfo,
                    'firstCheckDataProvider' => $firstCheckDataProvider,
                 ])
                ?>
            </div>
        <?php elseif($recordType == Report::$orthodonticsReturnvisit): ?>
            <div id = 'record' class="tab-pane">
                <?=
                $this->render('_orthodonticsReturnvisitForm', [
                    'model' => $orthodonticsReturnvisit,
                    'medicalFile' => $medicalFile,
                    'firstCheckDataProvider' => $firstCheckDataProvider,
                 ])
                ?>
        </div>
        <?php elseif($recordType == Report::$orthodonticsFirst): ?>
            <div id = 'record' class="tab-pane">
                <?=
                $this->render('_orthodonticsFirstRecordForm', [
                    'model' => $orthodonticsFirst,
                    'medicalFile' => $medicalFile,
                    'recordType' => $recordType,
                    'firstCheckDataProvider' => $firstCheckDataProvider,
                 ])
                ?>
        </div>
        <?php else : ?>
            <div id = 'record' class="tab-pane active">
                <?=
                $this->render('_recordForm', [
                    'model' => $model,
                    'hasTemplateCase' => $hasTemplateCase,
                    'medicalFile' => $medicalFile,
                    'firstCheckDataProvider' => $firstCheckDataProvider,
                    'reportResult'  => $reportResult,
                ])
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($permissonType[ChargeInfo::$inspectType])): ?>
            <div id = 'labCheck' class="tab-pane">

            </div>
        <?php endif; ?>
        <?php if (isset($permissonType[ChargeInfo::$checkType])): ?>
            <div id = 'auxiliary' class="tab-pane">

            </div>
        <?php endif; ?>
        <?php if (isset($permissonType[ChargeInfo::$cureType])): ?>
            <div id = 'cure' class="tab-pane">
               
            </div>
        <?php endif; ?>
        <?php if (isset($permissonType[ChargeInfo::$recipeType])): ?>
            <div id = 'recipe' class="tab-pane">
                
            </div>
        <?php endif; ?>
        
        <?php if (isset($permissonType[ChargeInfo::$consumablesType])): ?>
            <div id = 'consumables' class="tab-pane">
                
            </div>
        <?php endif; ?>
        
        <?php if (isset($permissonType[ChargeInfo::$materialType])): ?>
            <div id = 'material' class="tab-pane">
            </div>
        <?php endif; ?>
        
        <?php if (isset($permissonType[ChargeInfo::$reportType])): ?>
            <div id = 'report' class="tab-pane">
            
            </div>
        <?php endif; ?>
    </div>
</div>

