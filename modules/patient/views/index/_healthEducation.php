<?php
use yii\helpers\Html;
use app\modules\patient\models\Patient;
use app\modules\triage\models\HealthEducation;
$healthEducationAttributeLabels = (new HealthEducation())->attributeLabels();
?>
<table id="w0 " class="table detail-view health-education-<?= $patient_info['recordId'] ?> hidden" >
    <tbody>
    <tr >
        <?php
            echo " <th class='recipe-border-right'> 健康教育：</th>" ;
        ?>
        <td class="padding-none" colspan="2">
            <?php
//             $healthEducationRecord = HealthEducation::getHealthEducationRecord($patient_info['recordId']);
            $healthEducationRecord  = $healthEducationData[$patient_info['recordId']];
            if (!empty($healthEducationRecord)) {
                echo '<table id="w1" class="table recipe-table"><tbody>';
                foreach ($healthEducationRecord as $key => $value) {
                    echo '<tr>';
                    $str = '';
                    $value['education_content'] = $value['education_content'] !=''?Html::encode($value['education_content']):'';
                    $str .= Html::tag('div',$healthEducationAttributeLabels['education_content'].'：'.$value['education_content']);
                    $value['education_object'] = empty($value['education_object'])?'':HealthEducation::$getEducationObject[$value['education_object']];
                    $str .= Html::tag('div',$healthEducationAttributeLabels['education_object'].'：'.$value['education_object']);
                    $value['education_method'] = empty($value['education_method'])?'':HealthEducation::$getEducationMethod[$value['education_method']];
                    $str .= Html::tag('div',$healthEducationAttributeLabels['education_method'].'：'. $value['education_method']);
                    $value['accept_barrier'] = empty($value['accept_barrier'])?'':HealthEducation::$getAcceptBarrier[$value['accept_barrier']];
                    $str .= Html::tag('div',$healthEducationAttributeLabels['accept_barrier'].'：'.$value['accept_barrier']);
                    $value['accept_ability'] = empty($value['accept_ability'])?'':HealthEducation::$getAcceptAbility[$value['accept_ability']];
                    $str .= Html::tag('div',$healthEducationAttributeLabels['accept_ability'].'：'. $value['accept_ability']);
                    echo '<td style="width: 30%">' . $str. '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }else{
                echo '<table id="w1" class="table recipe-table"><tbody>';
                    echo '<tr>';
                    $str = '';
                    $str .= Html::tag('div',$healthEducationAttributeLabels['education_content'].'：');
                    $str .= Html::tag('div',$healthEducationAttributeLabels['education_object'].'：');
                    $str .= Html::tag('div',$healthEducationAttributeLabels['education_method'].'：');
                    $str .= Html::tag('div',$healthEducationAttributeLabels['accept_barrier'].'：');
                    $str .= Html::tag('div',$healthEducationAttributeLabels['accept_ability'].'：');
                    echo '<td style="width: 30%">' . $str . '</td>';
                    echo '</tr>';
                echo '</tbody></table>';
            }
            ?>
        </td>
    </tr>
    </tbody>
</table>
