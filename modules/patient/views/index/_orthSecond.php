<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/23
 * Time: 22:13
 */
use yii\helpers\Html;
use app\modules\patient\models\Patient;
use app\modules\triage\models\TriageInfo;
use app\modules\triage\models\TriageInfoRelation;
use app\modules\outpatient\models\OutpatientRelation;
use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\outpatient\models\OrthodonticsReturnvisitRecord;
$patientAttributeLabels = (new Patient())->attributeLabels();
$triageAttributeLabels = (new TriageInfo())->attributeLabels();
$outpatientRelationAttributeLabels = (new OrthodonticsReturnvisitRecord())->attributeLabels();
$triageRelationAttributeLabels = (new TriageInfoRelation())->attributeLabels();
$allergy= isset($allergyOutpatient[$patient_info['recordId']])?$allergyOutpatient[$patient_info['recordId']]:[];
?>
    <table id="w0 " class="table detail-view history-record-<?= $patient_info['recordId'] ?> hidden">
        <tbody>
        <tr>
            <th> <?php echo $outpatientRelationAttributeLabels['returnvisit'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['orthReturnvisit']) ?>
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
        <tr>
            <th> <?php echo $outpatientRelationAttributeLabels['check'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['check']); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $outpatientRelationAttributeLabels['treatment'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['orthTreatment']) ?>
            </td>
        </tr>
        <tr>
            <th>附件</th>
            <td>
                <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => $fileUploadData]); ?>
            </td>
        </tr>
        </tbody>
    </table>
