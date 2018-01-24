<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/23
 * Time: 21:57
 */
use yii\helpers\Html;
use app\modules\patient\models\Patient;
use app\modules\triage\models\TriageInfo;

$patientAttributeLabels = (new Patient())->attributeLabels();
$triageAttributeLabels = (new TriageInfo())->attributeLabels();
?>

<table id="w0 " class="table detail-view key-sign-<?= $patient_info['recordId'] ?>">
    <tbody>
        <tr>
            <th><?php echo $triageAttributeLabels['treatment_type'] . '：'; ?></th>
            <td>
                <?php
                if (5 == $patient_info['treatment_type']) {
                    echo Html::encode($patient_info['treatment']);
                } else if (0 == $patient_info['treatment_type']) {
                    echo '';
                } else {
                    echo Html::encode(Patient::$treatment_type[$patient_info['treatment_type']]);
                }
                ?>
            </td>
            <th><?php echo $patientAttributeLabels['heightcm'] . '：'; ?></th>
            <td>
                <?php
                if (!is_null($patient_info['heightcm'])) {
                    echo Html::encode($patient_info['heightcm']) . 'cm';
                }
                ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $patientAttributeLabels['weightkg'] . '：'; ?></th>
            <td>
                <?php
                if (!is_null($patient_info['weightkg'])) {
                    echo Html::encode($patient_info['weightkg']) . 'kg';
                }
                ?>
            </td>
            <th><?php echo $patientAttributeLabels['head_circumference'] . '：'; ?></th>
            <td>
                <?php
                if (!is_null($patient_info['head_circumference'])) {
                    echo Html::encode($patient_info['head_circumference']) . 'cm';
                }
                ?>
            </td>
        </tr>
        <tr>
            <th>BMI指数：</th>
            <td>
                <?php
                $bmi = Patient::getBmi($patient_info['heightcm'], $patient_info['weightkg']);
                if (!is_null($bmi)) {
                    echo Html::encode($bmi) . "kg/m²";
                }
                ?>
            </td>
            <th><?php echo $patientAttributeLabels['bloodtype'] . '：'; ?></th>
            <td>
                <?php
                echo $patient_info['bloodtype'];
                $bloodTypeSupplement = $patient_info['blood_type_supplement'] ? explode(',', $patient_info['blood_type_supplement']) : '';
                if (!empty($bloodTypeSupplement)) {
                    $bloodTypeSupplementStr = '，';
                    foreach ($bloodTypeSupplement as $key => $value) {
                        $bloodTypeSupplementStr .= TriageInfo::$bloodTypeSupplement[$value] . '，';
                    }
                    $bloodTypeSupplement = rtrim($bloodTypeSupplementStr, '，');
                }
                echo $bloodTypeSupplement;
                ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $patientAttributeLabels['temperature'] . '：'; ?></th>
            <td>
                <?php
                if (!is_null($patient_info['temperature'])) {
                    echo Html::encode($patient_info['temperature']) . '℃';
                    echo '－' . Html::encode(Patient::$temperature_type[$patient_info['temperature_type']]);
                }
                ?>
            </td>
            <th><?php echo $patientAttributeLabels['breathing'] . '：'; ?></th>
            <td>
                <?php
                if (!is_null($patient_info['breathing'])) {
                    echo Html::encode($patient_info['breathing']) . '次/分钟';
                }
                ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $patientAttributeLabels['pulse'] . '：'; ?></th>
            <td>
                <?php
                if (!is_null($patient_info['pulse'])) {
                    echo Html::encode($patient_info['pulse']) . '次/分钟';
                }
                ?>
            </td>
            <th> 血压：</th>
            <td>
                <?php
                if (!is_null($patient_info['shrinkpressure'])) {
                    echo "收缩压" . $patient_info['shrinkpressure'] . 'mmHg';
                }
                if (!is_null($patient_info['diastolic_pressure'])) {
                    echo "-" . "舒张压" . $patient_info['diastolic_pressure'] . 'mmHg';
                }
                ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $patientAttributeLabels['oxygen_saturation'] . '：'; ?></th>
            <td>
                <?php
                if (!is_null($patient_info['oxygen_saturation'])) {
                    echo Html::encode($patient_info['oxygen_saturation']) . '%';
                }
                ?>
            </td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th>疼痛评估：</th>
            <td colspan="3">
                <?php
                if (isset($assessment[$patient_info['recordId']]) && isset($assessment[$patient_info['recordId']][1])) {
                    echo $assessment[$patient_info['recordId']][1];
                }
//                if (!is_null($patient_info['pain_score'])) {
//                    if ($patient_info['pain_score'] >= 4) {
//                        echo '<span style="color: #ff4b00;">' . Html::encode($patient_info['pain_score']) . '分' . '</span>';
//                    } else {
//                        echo Html::encode($patient_info['pain_score']) . '分';
//                    }
//                }
                ?>
            </td>
        </tr>
        <tr>
            <th>跌倒评估：</th>
            <td>
                <?php
                if (isset($assessment[$patient_info['recordId']]) && isset($assessment[$patient_info['recordId']][2])) {
                    echo $assessment[$patient_info['recordId']][2];
                }
                ?>
            </td>
            <th></th>
            <td></td>
        </tr>
    <tr>
        <th>备注：</th>
        <td>
            <?php
                    if(!is_null($patient_info['triage_remark'])){
                        echo Html::encode($patient_info['triage_remark']);
                    }
            ?>

        </td>
        <td></td>
        <td></td>
    </tr>
    </tbody>
</table>