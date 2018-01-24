<?php

use yii\helpers\Html;
use app\modules\outpatient\models\dentalHistory;
use app\modules\outpatient\models\dentalHistoryRelation;
use app\modules\triage\models\TriageInfo;

$triageAttributeLabels = (new TriageInfo())->attributeLabels();
$dentalHistoryAttributeLabels = (new dentalHistory())->attributeLabels();
$dentalType = dentalHistory::$getRecordType;
$checkType = dentalHistoryRelation::$getType;
$allergy= isset($allergyOutpatient[$patient_info['recordId']])?$allergyOutpatient[$patient_info['recordId']]:[];
?>

<table id="w0 " class="table detail-view history-record-<?= $patient_info['recordId'] ?> hidden">
    <tbody>

    <tr>
        <th> <?php echo $dentalHistoryAttributeLabels['chiefcomplaint'].'：';?></th>
        <td>
            <?= Html::encode($patient_info['dental_chiefcomplaint']) ?>
        </td>
    </tr>

    <tr>
        <th> <?php echo $dentalHistoryAttributeLabels['historypresent'].'：';?></th>
        <td>
            <?= Html::encode($patient_info['dental_historypresent']) ?>
        </td>
    </tr>
    
    <tr>
        <th> <?php echo $dentalHistoryAttributeLabels['pasthistory'].'：';?></th>
        <td>
            <?= Html::encode($patient_info['dental_pasthistory']) ?>
        </td>
    </tr>

    <tr>
       <th> 过敏史</th>
            <td>
               <?php if(empty($allergy)): ?>
                <?php echo '无' ?>
                <?php else: ?>
                <?php 
                    echo $allergy[1]?'药物过敏：<span class="allergy-red">' .  Html::encode($allergy[1]).'</span><br>':'';
                    echo $allergy[2]?'食物过敏：<span class="allergy-red">'.  Html::encode($allergy[2]).'</span><br>':'';
                    echo $allergy[3]?'其它过敏：<span class="allergy-red">'.  Html::encode($allergy[3]).'</span>':'';
                ?>
                <?php endif;?>
            </td>
    </tr>

    <tr>
        <th><?php echo $triageAttributeLabels['first_check'].'：';?></th>
        <td>
            <?= isset($firstCheckData[$patient_info['recordId']])?Html::encode($firstCheckData[$patient_info['recordId']]):'' ?>
        </td>
    </tr>

    <?php
    foreach ($checkType as $k => $v){
        echo '<tr>';
        echo '<th>'.$v.'：</th>';
        echo '<td>';

        $info = $dentalHistoryData[$patient_info['recordId']][$k];

        for($index = 0; $index<count($info); $index++){
            $showLine = $index != count($info) - 1 ? true : false;
            $item = $info[$index];
            echo $this->render('_dentalCheckRow', ['dental' => $item, 'showLine' => $showLine]);
        }

        echo '</td>';
        echo '</tr>';

    }
    ?>

    <tr>
        <th><?php echo $dentalHistoryAttributeLabels['advice'].'：';?></th>
        <td>
            <?= Html::encode($patient_info['advice']) ?>
        </td>
    </tr>

    <tr>
        <th><?php echo $dentalHistoryAttributeLabels['remarks'].'：';?></th>
        <td>
            <?= Html::encode($patient_info['remarks']) ?>
        </td>
    </tr>

    </tbody>
</table>

