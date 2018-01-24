<?php

use yii\bootstrap\Tabs;
use rkit\yii2\plugins\ajaxform\Asset;

Asset::register($this);
$permissonType = [];
$items[] = [
    'label' => '基本信息',
    'options' => ['id' => 'basic'],
    'active' => true
];
$items[] = [
    'label' => '就诊信息',
    'options' => ['id' => 'treatment'],
];
//$items[] = [
//    'label' => '预约信息',
//    'options' => ['id' => 'outpatient'],
//];
?>

<div class="patient-form">

    <?=
    Tabs::widget([
        'renderTabContent' => false,
        'navType' => ' nav-tabs outpatient-form',
        'items' => $items
    ]);
    ?>
    <div class = 'tab-content'>
        <div id = 'basic' class="tab-pane active">
            <?=
            $this->render('_basic', [
                'model' => $model,
//                'allergy_list' => $allergy_list,
                'familyData' => $familyData,
            ])
            ?>
        </div>
        <div id = 'treatment' class="tab-pane">
            <?=
            $this->render('_information', [
                'model' => $model,
//                'allergy_list' => $allergy_list,
                'historyPatientInfo' => $historyPatientInfo,
                
                'nurseRecordData' => $nurseRecordData,
                'healthEducationData' => $healthEducationData,
                'inspectData' => $inspectData,
                'checkData' => $checkData,
                'cureData' => $cureData,
                'recipeData' => $recipeData,
            ])
            ?>
        </div>

    </div>
</div>

