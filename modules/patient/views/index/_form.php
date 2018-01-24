<?php

use yii\bootstrap\Tabs;
use kartik\tabs\TabsX;
use yii\helpers\Url;

$params = Yii::$app->request->queryParams;
$permissonType = [];
$items[] = [
    'label' => '基本信息',
    'options' => ['id' => 'basic'],
];
$items[]=[
    'label'=>'会员信息',
    'options'=>['id' => 'card'],
    'linkOptions' => ['data-url' => Url::to(['@apiPatientGetCardList','id' => $params['id']])]
];
$items[]=[
    'label'=>'预约信息',
    'options'=>['id' => 'appointment'],
    'linkOptions' => ['data-url' => Url::to(['@apiPatientGetAppointmentRecord','id' => $params['id']])]
];
$items[] = [
    'label' => '就诊信息',
    'options' => ['id' => 'treatment'],
    'linkOptions' => ['data-url' => Url::to(['@apiPatientGetPatientRecord','id' => $params['id']])]
];

$items[] = [
    'label' => '收费信息',
    'options' => ['id' => 'chargeInfo'],
    'linkOptions' => ['data-url' => Url::to(['@apiPatientGetChargeRecord','id' => $params['id']])]
];

if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@followIndexIndex'), $this->params['permList'])) {
    $items[] = [
        'label' => '随访信息',
        'options' => ['id' => 'follow'],
        'linkOptions' => ['data-url' => Url::to(['@apiPatientGetFollowRecord','id' => $params['id'],'FollowSearch[follow_state]' => $params['FollowSearch']['follow_state']])]
    ];
}
?>

<div class="patient-form">

    <?=
    TabsX::widget([
        'renderTabContent' => false,
        'navType' => ' nav-tabs outpatient-form',
        'items' => $items
    ]);
    ?>
    <div class = 'tab-content'>
        <div id = 'basic' class="tab-pane">
            <?= 
            $this->render('_basic', [
                'model' => $model,
                'allergy_list' => $allergy_list,
                'familyData' => $familyData,
            ])
            ?>
        </div>
        <div id = 'card' class="tab-pane">

        </div>
        <div id = 'treatment' class="tab-pane">

        </div>
        <div id="appointment" class="tab-pane">

        </div>

        <div id="chargeInfo" class="tab-pane">
            
        </div>

        <?php if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@followIndexIndex'), $this->params['permList'])):?>
        <div id = 'follow' class="tab-pane">
        </div>
        <?php endif;?>
    </div>
</div>
<div class = 'hide'>

                <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => ['fileList' => '','fileNameList' =>'', 'fileSizeList' => '','fileIdList' => '']]); ?>

</div>
