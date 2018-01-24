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
use app\modules\outpatient\models\OrthodonticsFirstRecord;
use app\modules\outpatient\models\OrthodonticsFirstRecordExamination;
use app\modules\outpatient\models\OrthodonticsFirstRecordFeatures;
use app\modules\outpatient\models\OrthodonticsFirstRecordModelCheck;
use app\modules\outpatient\models\OrthodonticsFirstRecordTeethCheck;

$patientAttributeLabels = (new Patient())->attributeLabels();
$triageAttributeLabels = (new TriageInfo())->attributeLabels();
$outpatientRelationAttributeLabels = (new OrthodonticsReturnvisitRecord())->attributeLabels();
$triageRelationAttributeLabels = (new TriageInfoRelation())->attributeLabels();
$recordLabels = (new OrthodonticsFirstRecord())->attributeLabels();
$recordExaminationLabels = (new OrthodonticsFirstRecordExamination())->attributeLabels();
$recordFeaturesLabels = (new OrthodonticsFirstRecordFeatures())->attributeLabels();
$recordModelCheckLables = (new OrthodonticsFirstRecordModelCheck())->attributeLabels();
$recordTeethCheckLables = (new OrthodonticsFirstRecordTeethCheck())->attributeLabels();

$allergy = isset($allergyOutpatient[$patient_info['recordId']]) ? $allergyOutpatient[$patient_info['recordId']] : [];
?>

<?php
$css = <<<CSS
   .td-fixed-with{
         width:120px;
    }
    .td-pd-30{
         padding-right:30px;
    }
CSS;
$this->registerCss($css);
?>
<table id="w0 " class="table detail-view history-record-<?= $patient_info['recordId'] ?> hidden">
    <tbody>
        <tr>
            <th> <?php echo $recordLabels['chiefcomplaint'] . '：'; ?></th>
            <td>
                <?= Html::encode($patient_info['orthChiefcomplaint']) ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $recordLabels['motivation'] . '：'; ?></th>
            <td>
                <?= Html::encode($patient_info['motivation']) ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $recordLabels['historypresent'] . '：'; ?></th>
            <td>
                <?= Html::encode($patient_info['orthHistorypresent']) ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $recordLabels['all_past_history'] . '：'; ?></th>
            <td>
                <?= Html::encode($patient_info['all_past_history']) ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $recordLabels['pastdraghistory'] . '：'; ?></th>
            <td>
                <?= Html::encode($patient_info['orthPastdraghistory']) ?>
            </td>
        </tr>
        <tr>
            <th> 过敏史</th>
            <td>
                <?php if (empty($allergy)): ?>
                    <?php echo '无' ?>
                <?php else: ?>
                    <?php
                    echo $allergy[1] ? '药物过敏：<span class="allergy-red">' . Html::encode($allergy[1]) . '</span><br>' : '';
                    echo $allergy[2] ? '食物过敏：<span class="allergy-red">' . Html::encode($allergy[2]) . '</span><br>' : '';
                    echo $allergy[3] ? '其它过敏：<span class="allergy-red">' . Html::encode($allergy[3]) . '</span>' : '';
                    ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th rowspan="4">口腔病史：</th>
            <td>
                <?= $recordLabels['retention'] ?>
                <?php echo $this->render('_orthToothMap', ['position' => $patient_info['recordRetention']]); ?> 
                <?php // Html::encode($patient_info['retention'])   ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $recordLabels['early_loss'] ?>
                <?php echo $this->render('_orthToothMap', ['position' => $patient_info['early_loss']]); ?> 
            </td>
        </tr>
        <tr>
            <td>
                <?= $recordLabels['bad_habits'] . '：' . OrthodonticsFirstRecord::formatOutHtml($patient_info['bad_habits'], $patient_info['bad_habits_abnormal'], $patient_info['bad_habits_abnormal_other'], OrthodonticsFirstRecord::$getBadHabits, OrthodonticsFirstRecord::$getBadHabitsAbnormal, 8) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $recordLabels['traumahistory'] . '：' . Html::encode($patient_info['traumahistory']) ?>
            </td>
        </tr>
        <tr>
            <th rowspan="2">先天及遗传史：</th>
            <td>
                <?= $recordLabels['feed'] . '：' ?>
                <?= OrthodonticsFirstRecord::formatNormal($patient_info['feed'], OrthodonticsFirstRecord::$getFeed) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $recordLabels['immediate'] . '：' ?>
                <?= Html::encode($patient_info['immediate']) ?>
            </td>
        </tr>
        <tr>
            <th>全身状态：</th>
            <td>
                <div><?= $triageAttributeLabels['heightcm'] . '：' . $patient_info['heightcm'] ?></div>
                <div><?= $triageAttributeLabels['weightkg'] . '：' . $patient_info['weightkg'] ?></div>
                <div><?= $recordFeaturesLabels['dental_age'] . '：' . $patient_info['dental_age'] ?></div>
                <div><?= $recordFeaturesLabels['bone_age'] . '：' . $patient_info['bone_age'] ?></div>
                <div><?= $recordFeaturesLabels['second_features'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['second_features'], OrthodonticsFirstRecordFeatures::$getSecondFeatures) ?></div>
            </td>
        </tr>
        <tr>
            <th rowspan="2">颜貌：</th>
            <td>
                <div>
                    <table>
                        <tr>
                            <td class="td-fixed-with">正面<td>
                            <td>
                                <div><?= $recordFeaturesLabels['frontal_type'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['frontal_type'], OrthodonticsFirstRecordFeatures::$getFrontalType) ?></div>
                                <div><?= $recordFeaturesLabels['symmetry'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['symmetry'], OrthodonticsFirstRecordFeatures::$getSymmetry) ?></div>
                                <div><?= $recordFeaturesLabels['abit'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['abit'], OrthodonticsFirstRecordFeatures::$getAbit) ?></div>
                                <div><?= $recordFeaturesLabels['face'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['face'], OrthodonticsFirstRecordFeatures::$getFace) ?></div>
                                <div><?= $recordFeaturesLabels['smile'] . '：' . OrthodonticsFirstRecord::formatOutHtml(2, $patient_info['smile'], $patient_info['smile_other'], [], OrthodonticsFirstRecordFeatures::$getSmile, 3) ?></div>
                                <div><?= $recordFeaturesLabels['upper_lip'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['upper_lip'], OrthodonticsFirstRecordFeatures::$getUpperLip) ?></div>
                                <div><?= $recordFeaturesLabels['lower_lip'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['lower_lip'], OrthodonticsFirstRecordFeatures::$getLowerLip) ?></div>
                            <td>
                        </tr>
                    </table>

                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div>
                    <table>
                        <tr>
                            <td class="td-fixed-with">侧面</td>
                            <td>
                                <div><?= $recordFeaturesLabels['side'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['side'], OrthodonticsFirstRecordFeatures::$getSide) ?></div>
                                <div><?= $recordFeaturesLabels['nasolabial_angle'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['nasolabial_angle'], OrthodonticsFirstRecordFeatures::$getNasolabialAngle) ?></div>
                                <div><?= $recordFeaturesLabels['chin_lip'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['chin_lip'], OrthodonticsFirstRecordFeatures::$getChinLip) ?></div>
                                <div><?= $recordFeaturesLabels['mandibular_angle'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['mandibular_angle'], OrthodonticsFirstRecordFeatures::$getMandibularAngle) ?></div>
                                <div><?= $recordFeaturesLabels['upper_lip_position'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['upper_lip_position'], OrthodonticsFirstRecordFeatures::$getUpperLipPosition) ?></div>
                                <div><?= $recordFeaturesLabels['lower_lip_position'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['lower_lip_position'], OrthodonticsFirstRecordFeatures::$getLowerLipPosition) ?></div>
                                <div><?= $recordFeaturesLabels['chin_position'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['chin_position'], OrthodonticsFirstRecordFeatures::$getChinPosition) ?></div> 
                            </td>
                        </tr>
                    </table>

                </div>
            </td>
        </tr>
        <tr>
            <th rowspan="4">功能性检查：</th>
            <td>
                <?= $recordLabels['oral_function'] . '：' ?>
                <?= OrthodonticsFirstRecord::formatOutHtml($patient_info['oral_function'], $patient_info['oral_function_abnormal'], '', OrthodonticsFirstRecord::$getOralFunction, OrthodonticsFirstRecord::$getOralFunctionAbnormal, 7) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $recordLabels['mandibular_movement'] . '：' ?>
                <?= OrthodonticsFirstRecord::formatOutHtml($patient_info['mandibular_movement'], $patient_info['mandibular_movement_abnormal'], '', OrthodonticsFirstRecord::$getMandibularMovement, [], 999) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $recordLabels['mouth_open'] . '：' ?>
                <?= OrthodonticsFirstRecord::formatOutHtml($patient_info['mouth_open'], $patient_info['mouth_open_abnormal'], '', OrthodonticsFirstRecord::$getMouthOpen, [], 999) ?>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="td-fixed-with">颞下颌关节</td>
                        <td>
                            <div>左：<?= OrthodonticsFirstRecord::formatOutHtml($patient_info['left_temporomandibular_joint'], $patient_info['left_temporomandibular_joint_abnormal'], $patient_info['left_temporomandibular_joint_abnormal_other'], OrthodonticsFirstRecord::$getLeftTemporomandibularJoint, OrthodonticsFirstRecord::$getLeftTemporomandibularJointAbnormal, 3) ?></div>
                            <div>右：<?= OrthodonticsFirstRecord::formatOutHtml($patient_info['right_temporomandibular_joint'], $patient_info['right_temporomandibular_joint_abnormal'], $patient_info['right_temporomandibular_joint_abnormal_other'], OrthodonticsFirstRecord::$getRightTemporomandibularJoint, OrthodonticsFirstRecord::$getRightTemporomandibularJointAbnormal, 3) ?></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <th rowspan="15">口腔组织检查</th>
            <td> <?= $recordExaminationLabels['hygiene'] . '：' . Html::encode($patient_info['hygiene']) ?>  </td>
        </tr>
        <tr><td> <?= $recordExaminationLabels['periodontal'] . '：' . Html::encode($patient_info['periodontal']) ?> </td>
        <tr><td> <?= $recordExaminationLabels['ulcer'] . '：' . Html::encode($patient_info['ulcer']) ?></td> </tr>
        <tr><td><?= $recordExaminationLabels['gums'] . '：' . Html::encode($patient_info['gums']) ?></td></tr>
        <tr><td><?= $recordExaminationLabels['tonsil'] . '：' . Html::encode($patient_info['tonsil']) ?></td></tr>
        <tr><td><?= $recordExaminationLabels['frenum'] . '：' . Html::encode($patient_info['frenum']) ?></td></tr>
        <tr><td><?= $recordExaminationLabels['soft_palate'] . '：' . Html::encode($patient_info['soft_palate']) ?></td></tr>
        <tr><td><?= $recordExaminationLabels['lip'] . '：' . Html::encode($patient_info['lip']) ?></td></tr>
        <tr><td><?= $recordExaminationLabels['tongue'] . '：' . Html::encode($patient_info['tongue']) ?></td></tr>
        <tr><td><?= $recordExaminationLabels['dentition'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['dentition'], OrthodonticsFirstRecordExamination::$getDentition) ?></td></tr>
        <tr><td><?= $recordExaminationLabels['arch_form'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['arch_form'], OrthodonticsFirstRecordExamination::$getArchForm) ?></td></tr>
        <tr><td><?= $recordExaminationLabels['arch_coordination'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['arch_coordination'], OrthodonticsFirstRecordExamination::$getArchCoordination) ?></td></tr>
        <tr><td><table><tr><td class="td-fixed-with">覆合</td><td>
                            <div>前牙：<?= OrthodonticsFirstRecord::formatOutHtml($patient_info['overbite_anterior_teeth'], $patient_info['overbite_anterior_teeth_abnormal'], $patient_info['overbite_anterior_teeth_other'], OrthodonticsFirstRecordExamination::$getOverbiteAnteriorTeeth, OrthodonticsFirstRecordExamination::$getOverbiteAnteriorTeethAbnormal, 3) ?></div>
                            <div>后牙：<?= OrthodonticsFirstRecord::formatOutHtml($patient_info['overbite_posterior_teeth'], $patient_info['overbite_posterior_teeth_abnormal'], $patient_info['overbite_posterior_teeth_other'], OrthodonticsFirstRecordExamination::$getOverbitePosteriorTeeth, OrthodonticsFirstRecordExamination::$getOverbitePosteriorTeethAbnormal, 3) ?></div>
                        <td></tr></table></td></tr>
        <tr><td><table><tr><td class="td-fixed-with">覆盖</td><td>
                            <div>前牙：<?= OrthodonticsFirstRecord::formatOutHtml($patient_info['cover_anterior_teeth'], $patient_info['cover_anterior_teeth_abnormal'], '', OrthodonticsFirstRecordExamination::$getCoverAnteriorTeeth, OrthodonticsFirstRecordExamination::$getCoverAnteriorTeethAbnormal, 4) ?></div>
                            <div>后牙：<?= OrthodonticsFirstRecord::formatOutHtml($patient_info['cover_posterior_teeth'], $patient_info['cover_posterior_teeth_abnormal'], '', OrthodonticsFirstRecordExamination::$getCoverPosteriorTeeth, OrthodonticsFirstRecordExamination::$getCoverPosteriorTeethAbnormal, 4) ?></div>
                        <td></tr></table></td></tr>
        <tr><td><table><tr><td class="td-fixed-with">咬合关系</td><td>   
                            <div><?= $recordExaminationLabels['left_canine'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['left_canine'], OrthodonticsFirstRecordExamination::$getLeftCanine) ?></div>
                            <div><?= $recordExaminationLabels['right_canine'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['right_canine'], OrthodonticsFirstRecordExamination::$getRightCanine) ?></div>
                            <div><?= $recordExaminationLabels['left_molar'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['left_molar'], OrthodonticsFirstRecordExamination::$getLeftMolar) ?></div>
                            <div><?= $recordExaminationLabels['right_molar'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['right_molar'], OrthodonticsFirstRecordExamination::$getRightMolar) ?></div>
                            <div><?= $recordExaminationLabels['midline_teeth'] . '：' . OrthodonticsFirstRecord::formatNormalConnect($patient_info['midline_teeth'], $patient_info['midline_teeth_value'], OrthodonticsFirstRecordExamination::$getMidlineTeeth) ?></div>
                            <div><?= $recordExaminationLabels['midline'] . '：' . OrthodonticsFirstRecord::formatNormalConnect($patient_info['midline'], $patient_info['midline_value'], OrthodonticsFirstRecordExamination::$getMidline) ?></div>
                        </td></tr></table></td></tr>

        <tr>
            <th rowspan="6">模型检查：</th>
            <td>
                <table><tr><td class="td-fixed-with">拥挤度</td><td>
                            <div>
                                <?php  echo $recordModelCheckLables['crowded_maxillary'] . '：';
                                    if($patient_info['crowded_maxillary']){
                                        echo  $patient_info['crowded_maxillary'] . 'mm';
                                    }
                                ?>
                            </div>
                            <div>
                                <?php  echo $recordModelCheckLables['crowded_mandible'] . '：';
                                if($patient_info['crowded_mandible']){
                                    echo  $patient_info['crowded_mandible'] . 'mm';
                                }
                                ?>
                            </div>
                        </td></tr></table>
            </td>
        </tr>
        <tr>
            <td>
                <table><tr><td class="td-fixed-with">牙尖区宽度</td><td>
                            <div>
                                <?php  echo $recordModelCheckLables['canine_maxillary'] . '：';
                                if($patient_info['canine_maxillary']){
                                    echo  $patient_info['canine_maxillary'] . 'mm';
                                }
                                ?>
                            </div>
                            <div>
                                <?php  echo $recordModelCheckLables['canine_mandible'] . '：';
                                    if($patient_info['canine_mandible']){
                                        echo  $patient_info['canine_mandible'] . 'mm';
                                    }
                                ?>
                            </div>
                        </td></tr></table>
            </td>
        </tr>
        <tr>
            <td>
                <table><tr><td class="td-fixed-with">磨牙区宽度</td><td>
                            <div>
                                <?php  echo $recordModelCheckLables['molar_maxillary'] . '：';
                                if($patient_info['molar_maxillary']){
                                    echo  $patient_info['molar_maxillary'] . 'mm';
                                }
                                ?>
                            </div>
                            <div>
                                <?php  echo $recordModelCheckLables['molar_mandible'] . '：';
                                if($patient_info['molar_mandible']){
                                    echo  $patient_info['molar_mandible'] . 'mm';
                                }
                                ?>
                            </div>
                        </td></tr></table>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo  $recordModelCheckLables['spee_curve'] . '：';
                    if($patient_info['spee_curve']){
                        echo   $patient_info['spee_curve'] . 'mm';
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $recordModelCheckLables['transversal_curve'] . '：' . OrthodonticsFirstRecord::formatNormal($patient_info['transversal_curve'], OrthodonticsFirstRecordModelCheck::$getTransversalCurve) ?>
            </td>
        </tr>
        <tr>
            <td>
                <table><tr><td class="td-fixed-with">bolton比值</td><td>
                            <div><?= $recordModelCheckLables['bolton_nterior_teeth'] . '：' . Html::encode($patient_info['bolton_nterior_teeth'] ? $patient_info['bolton_nterior_teeth'] : '') ?></div>
                            <div><?= $recordModelCheckLables['bolton_all_teeth'] . '：' . Html::encode($patient_info['bolton_all_teeth'] ? $patient_info['bolton_all_teeth'] : '') ?></div>
                        </td></tr></table>
            </td>
        </tr>
        <tr>
            <th><?php echo $recordModelCheckLables['examination'] . '：'; ?></th>
            <td>
                <!--                <div>曲面断层片分析：方法</div>
                                <div>投影测量分析：试试</div>
                                <div>CBCT分析：额额</div>-->
                <?= Html::encode($patient_info['examination']) ?>
            </td>
        </tr>
        <tr>
            <th>牙齿检查：</th>
            <td>
                <table>
                    <tr>
                        <td class="td-pd-30">
                            龋齿
                            <?php echo $this->render('_orthToothMap', ['position' => $patient_info['dental_caries']]); ?> 
                        </td>
                        <td class="td-pd-30">
                            扭转
                            <?php echo $this->render('_orthToothMap', ['position' => $patient_info['reverse']]); ?> 
                        </td>

                    </tr>
                    <tr>
                        <td>
                            阻生
                            <?php echo $this->render('_orthToothMap', ['position' => $patient_info['impacted']]); ?> 
                        </td>
                        <td>
                            异位
                            <?php echo $this->render('_orthToothMap', ['position' => $patient_info['ectopic']]); ?> 
                        </td>
                    </tr>
                    <tr>
                        <td>
                            缺失
                            <?php echo $this->render('_orthToothMap', ['position' => $patient_info['defect']]); ?> 
                        </td>
                        <td>
                            滞留
                            <?php echo $this->render('_orthToothMap', ['position' => $patient_info['retention']]); ?> 
                        </td>
                    </tr>
                    <tr>
                        <td>
                            修复体
                            <?php echo $this->render('_orthToothMap', ['position' => $patient_info['repair_body']]); ?> 
                        </td>
                        <td>
                            <?= $patient_info['other_remark'] ? Html::encode($patient_info['other_remark']) : '其他' ?>
                            <?php echo $this->render('_orthToothMap', ['position' => $patient_info['other']]); ?> 
                            <!--TODO-->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th><?php echo $triageAttributeLabels['first_check'] . '：'; ?></th>
            <td>
                <?= isset($firstCheckData[$patient_info['recordId']]) ? Html::encode($firstCheckData[$patient_info['recordId']]) : '' ?>
            </td>
        </tr>
        <tr>
            <th> <?php echo $recordTeethCheckLables['orthodontic_target'] . '：'; ?></th>
            <td>
                <?= Html::encode($patient_info['orthodontic_target']); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $recordTeethCheckLables['cure'] . '：'; ?></th>
            <td>
                <?= Html::encode($patient_info['cure']) ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $recordTeethCheckLables['special_risk'] . '：'; ?></th>
            <td>
                <?= Html::encode($patient_info['special_risk']) ?>
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
