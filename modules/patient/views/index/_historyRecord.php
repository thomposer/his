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
$patientAttributeLabels = (new Patient())->attributeLabels();
$triageAttributeLabels = (new TriageInfo())->attributeLabels();
$outpatientRelationAttributeLabels = (new OutpatientRelation())->attributeLabels();
$triageRelationAttributeLabels = (new TriageInfoRelation())->attributeLabels();
$allergy= isset($allergyOutpatient[$patient_info['recordId']])?$allergyOutpatient[$patient_info['recordId']]:[];
?>
    <table id="w0 " class="table detail-view history-record-<?= $patient_info['recordId'] ?> hidden">
        <tbody>
<!--        <tr>-->
<!--            <th>--><?php //echo $triageAttributeLabels['incidence_date'].'：';?><!--</th>-->
<!--            <td>-->
<!--                --><?php
//                if ($patient_info['incidence_date'] == 0) {
//                    echo '';
//                } else {
//                    echo date('Y-m-d', $patient_info['incidence_date']);
//                }
//                ?>
<!--            </td>-->
<!--        </tr>-->
        <tr>
            <th> <?php echo $outpatientRelationAttributeLabels['chiefcomplaint'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['chiefcomplaint']) ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $outpatientRelationAttributeLabels['historypresent'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['historypresent']) ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $outpatientRelationAttributeLabels['pasthistory'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['pasthistory']) ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $triageRelationAttributeLabels['pastdraghistory'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['pastdraghistory']) ?>
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
            <th> <?php echo $outpatientRelationAttributeLabels['personalhistory'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['personalhistory']) ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $outpatientRelationAttributeLabels['genetichistory'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['genetichistory']) ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $outpatientRelationAttributeLabels['physical_examination'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['physical_examination']) ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $triageAttributeLabels['examination_check'].'：';?></th>
            <td>
                <?= nl2br(Html::encode($patient_info['examination_check']))?>
            </td>
        </tr>
        <tr>
            <th><?php echo $triageAttributeLabels['first_check'].'：';?></th>
            <td>
                <?= isset($firstCheckData[$patient_info['recordId']])?Html::encode($firstCheckData[$patient_info['recordId']]):'' ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $triageAttributeLabels['cure_idea'].'：';?></th>
            <td>
                <?= nl2br(Html::encode($patient_info['cure_idea'])); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $triageRelationAttributeLabels['followup'].'：';?></th>
            <td>
                <?= Html::encode($patient_info['followup']) ?>
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
