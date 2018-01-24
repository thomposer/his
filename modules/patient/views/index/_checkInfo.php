<?php 
use app\modules\outpatient\models\ChildExaminationCheck;
use app\modules\patient\models\Patient;
use yii\helpers\Html;
use app\modules\outpatient\models\ChildExaminationAssessment;
use app\common\Percentage;
use app\modules\outpatient\models\ChildExaminationInfo;
use app\modules\triage\models\TriageInfo;
$assessmentAttributeLabels = (new ChildExaminationAssessment())->attributeLabels();
$triageAttributeLabels = (new TriageInfo())->attributeLabels();
$basicAttributeLabels = (new \app\modules\outpatient\models\ChildExaminationBasic())->attributeLabels();
$checkAttributeLabels = (new \app\modules\outpatient\models\ChildExaminationCheck())->attributeLabels();
$checkInfoAttributeLabels = (new \app\modules\outpatient\models\ChildExaminationInfo())->attributeLabels();
$allergy= isset($allergyOutpatient[$patient_info['recordId']])?$allergyOutpatient[$patient_info['recordId']]:[];
?>
    <table id="w0 " class="table detail-view check-info-<?= $patient_info['recordId'] ?> hidden">
            <tbody>
            <tr>
                <th>测评年龄：</th>
                <td colspan="2">
                    <?php
                    if ($patient_info['birthday'] > $patient_info['reportTime']) {
                        echo '未出生';
                    } else {
                        if (!empty($patient_info['birthday'])) {
                            echo  Patient::dateDiffage($patient_info['birthday'], $patient_info['reportTime']);
                        }
                    }
                    ?>
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
                <td colspan="2" class="first-chech-content">
                    <?= isset($firstCheckData[$patient_info['recordId']])?Html::encode($firstCheckData[$patient_info['recordId']]):'' ?>
                </td>
            </tr>
            <tr>
                <th>生长评估：</th>
                <td colspan="2">
                    <div>
                        身长或身高(cm)：
                        第
                        <?php
                            $heightcm = $patient_info['heightcm'] ? Percentage::getPercentage($patient_info['heightcm'], $patient_info['sex'], 1, $patient_info['birthday'],$patient_info['reportTime']) : '';
                            echo $heightcmPercentage = $heightcm?$heightcm:'--';
                        ?>
                        百分位
                    </div>
                    <div>
                        体重(kg)：
                        第
                        <?php
                            $weightkg = $patient_info['weightkg'] ? Percentage::getPercentage($patient_info['weightkg'], $patient_info['sex'], 2, $patient_info['birthday'],$patient_info['reportTime']) : '--';
                            echo $weightkgPercentage = $weightkg?$weightkg:'--';
                        ?>
                        百分位
                    </div>
                    <div>
                        头围(cm)：
                        第
                        <?php
                            $headCircumference = $patient_info['head_circumference'] ? Percentage::getPercentage($patient_info['head_circumference'], $patient_info['sex'], 3, $patient_info['birthday'],$patient_info['reportTime']) : '--';
                            echo $headCircumferencePercentage = $headCircumference?$headCircumference:'--';
                        ?>
                        百分位
                    </div>
                    <div>
                        BMI(kg/m<sup>2</sup>)：
                        第
                        <?php
                            $bmi = Patient::getBmi($patient_info['heightcm'], $patient_info['weightkg']);
                            $bmiValue = $bmi ? Percentage::getPercentage($bmi, $patient_info['sex'], 4, $patient_info['birthday'],$patient_info['reportTime']) : '--';
                            echo $bmiValue = $bmiValue?$bmiValue:'--';
                        ?>
                        百分位
                    </div>
                    <div>
                        生长评估总结：
                        <?php
                            if (!empty($patient_info['growthResult'])) {
                                echo  ChildExaminationAssessment::$getSummary[$patient_info['growthResult']];
                            }else{
                                echo '-';
                            }
                        ?>

                    </div>
                </td>
            </tr>
            <tr>
                <th>睡眠及大小便</th>
                <td colspan="2">
                    <div>
                        <?php echo $checkInfoAttributeLabels['sleep']."："; echo Html::encode($patient_info['sleep']); ?>
                    </div>
                    <div>
                        <?php echo $checkInfoAttributeLabels['shit']."："; echo Html::encode($patient_info['shit']); ?>
                    </div>
                    <div>
                        <?php echo $checkInfoAttributeLabels['pee']."："; echo Html::encode($patient_info['pee']); ?>
                    </div>
                </td>
            </tr>
<!--            <tr>-->
<!--                <th>前囟：</th>-->
<!--                <td colspan="2">-->
<!--                    --><?php
//                    if ($patient_info['bregmatic'] != '') {
//                        echo Html::encode($patient_info['bregmatic']);
//                    }
//                    ?>
<!--                </td>-->
<!--            </tr>-->
<!--            <tr>-->
<!--                <th>--><?php //echo $basicAttributeLabels['jaundice']."：";?><!--</th>-->
<!--                <td colspan="2">-->
<!--                    --><?php
//                    if ($patient_info['jaundice']  != '') {
//                        echo Html::encode($patient_info['jaundice']);
//                    }
//                    ?>
<!--                </td>-->
<!--            </tr>-->
<!--            <tr>-->
<!--                <th>生长评估总结：</th>-->
<!--                <td colspan="2">-->
<!--                    --><?php
//                    if (!empty($patient_info['growthResult'])) {
//                       echo  ChildExaminationAssessment::$getSummary[$patient_info['growthResult']];
//                    }else{
//                        echo '-';
//                    }
//                    if ($patient_info['growthRemark'] !='') {
//                        echo '('.Html::encode($patient_info['growthRemark']).')';
//                    }
//                    ?>
<!--                </td>-->
<!--            </tr>-->
            <tr>
                <th>体格检查：</th>
                <td colspan="2">
                  <?php
                      $strAppearance = '-';
                      if (!empty($patient_info['appearance'])) {
                          $strAppearance =  ChildExaminationCheck::$getType[$patient_info['appearance']];
                      }
                      if ($patient_info['appearance_remark'] !='') {
                          $strAppearance .= Html::encode('('.$patient_info['appearance_remark'].')');
                      }
                      echo Html::tag('div', $checkAttributeLabels['appearance'].'：'.$strAppearance);
                      $strSkin = '-';
                      if (!empty($patient_info['skin'])) {
                          $strSkin =  ChildExaminationCheck::$getType[$patient_info['skin']];
                      }
                      if ($patient_info['skin_remark'] !='') {
                          $strSkin .= Html::encode('('.$patient_info['skin_remark'].')');
                      }
                      echo Html::tag('div', $checkAttributeLabels['skin'] .'：'.$strSkin);
                      $strHeadFace = '-';
                          if (!empty($patient_info['headFace'])) {
                              $strHeadFace =  ChildExaminationCheck::$getType[$patient_info['headFace']];
                          }
                          if ($patient_info['headFace_remark'] !='') {
                              $strHeadFace .= Html::encode('('.$patient_info['headFace_remark'].')');
                          }
                      echo Html::tag('div',  $checkAttributeLabels['headFace'] .'：'.$strHeadFace);
                      $strEye = '-';
                      if (!empty($patient_info['eye'])) {
                          $strEye =  ChildExaminationCheck::$getType[$patient_info['eye']];
                      }
                      if ($patient_info['eye_remark'] !='') {
                          $strEye .= Html::encode('('.$patient_info['eye_remark'].')');
                      }
                      echo Html::tag('div', $checkAttributeLabels['eye'] .'：' .$strEye);
                      $strEar = '-';
                      if (!empty($patient_info['ear'])) {
                          $strEar =  ChildExaminationCheck::$getType[$patient_info['ear']];
                      }
                      if ($patient_info['ear_remark'] !='') {
                          $strEar .= Html::encode('('.$patient_info['ear_remark'].')');
                      }
                      echo Html::tag('div',$checkAttributeLabels['ear'] .'：' .$strEar);
                      $strNose = '-';
                      if (!empty($patient_info['nose'])) {
                          $strNose =  ChildExaminationCheck::$getType[$patient_info['nose']];
                      }
                      if ($patient_info['nose_remark'] !='') {
                          $strNose .= Html::encode('('.$patient_info['nose_remark'].')');
                      }
                      echo Html::tag('div',$checkAttributeLabels['nose'] .'：'.$strNose);
                      $strThroat = '-';
                      if (!empty($patient_info['throat'])) {
                          $strThroat =  ChildExaminationCheck::$getType[$patient_info['throat']];
                      }
                      if ($patient_info['throat_remark'] !='') {
                          $strThroat .= Html::encode('('.$patient_info['throat_remark'].')');
                      }
                      echo Html::tag('div', $checkAttributeLabels['throat'] .'：' .$strThroat);
                      $strTooth = '-';
                      if (!empty($patient_info['tooth'])) {
                          $strTooth =  ChildExaminationCheck::$getType[$patient_info['tooth']];
                      }
                      if ($patient_info['tooth_remark'] !='') {
                          $strTooth .= Html::encode('('.$patient_info['tooth_remark'].')');
                      }
                      echo Html::tag('div', $checkAttributeLabels['tooth'] .'：' .$strTooth);
                      $strChest = '-';
                      if (!empty($patient_info['chest'])) {
                          $strChest =  ChildExaminationCheck::$getType[$patient_info['chest']];
                      }
                      if ($patient_info['chest_remark'] !='') {
                          $strChest .= Html::encode('('.$patient_info['chest_remark'].')');
                      }
                      echo Html::tag('div', $checkAttributeLabels['chest'] .'：' .$strChest);
                      $strBellows = '-';
                      if (!empty($patient_info['bellows'])) {
                          $strBellows =  ChildExaminationCheck::$getType[$patient_info['bellows']];
                      }
                      if ($patient_info['bellows_remark'] !='') {
                          $strBellows .= Html::encode('('.$patient_info['bellows_remark'].')');
                      }
                      echo Html::tag('div',$checkAttributeLabels['bellows'] .'：' .$strBellows);
                      $strCardiovascular = '-';
                      if (!empty($patient_info['cardiovascular'])) {
                          $strCardiovascular =  ChildExaminationCheck::$getType[$patient_info['cardiovascular']];
                      }
                      if ($patient_info['cardiovascular_remark'] !='') {
                          $strCardiovascular .= Html::encode('('.$patient_info['cardiovascular_remark'].')');
                      }
                      echo Html::tag('div', $checkAttributeLabels['cardiovascular'] .'：' .$strCardiovascular);
                      $strBelly = '-';
                      if (!empty($patient_info['belly'])) {
                          $strBelly =  ChildExaminationCheck::$getType[$patient_info['belly']];
                      }
                      if ($patient_info['belly_remark'] !='') {
                          $strBelly .= Html::encode('('.$patient_info['belly_remark'].')');
                      }
                      echo Html::tag('div',  $checkAttributeLabels['belly'] .'：'  .$strBelly);
                      $strGenitals = '-';
                      if (!empty($patient_info['genitals'])) {
                          $strGenitals =  ChildExaminationCheck::$getType[$patient_info['genitals']];
                      }
                      if ($patient_info['genitals_remark'] !='') {
                          $strGenitals .= Html::encode('('.$patient_info['genitals_remark'].')');
                      }
                      echo Html::tag('div', $checkAttributeLabels['genitals'] .'：' .$strGenitals);
                      $strBack = '-';
                      if (!empty($patient_info['back'])) {
                          $strBack =  ChildExaminationCheck::$getType[$patient_info['back']];
                      }
                      if ($patient_info['back_remark'] !='') {
                          $strBack .= Html::encode('('.$patient_info['back_remark'].')');
                      }
                      echo Html::tag('div',  $checkAttributeLabels['back'] .'：' .$strBack);
                      $strLimb = '-';
                      if (!empty($patient_info['limb'])) {
                          $strLimb =  ChildExaminationCheck::$getType[$patient_info['limb']];
                      }
                      if ($patient_info['limb_remark'] !='') {
                          $strLimb .= Html::encode('('.$patient_info['limb_remark'].')');
                      }
                      echo Html::tag('div',  $checkAttributeLabels['limb'] .'：' .$strLimb);
                      $strNerve = '-';
                      if (!empty($patient_info['nerve'])) {
                          $strNerve =  ChildExaminationCheck::$getType[$patient_info['nerve']];
                      }
                      if ($patient_info['nerve_remark'] !='') {
                          $strNerve .= Html::encode('('.$patient_info['nerve_remark'].')');
                      }
                      echo Html::tag('div', $checkAttributeLabels['nerve'] .'：'  .$strNerve);
                  ?>

                </td>
            </tr>
            <tr>
                <th>视力与听力</th>
                <td colspan="2">
                    <div>
                        <?php echo $checkInfoAttributeLabels['visula_check']."："; echo Html::encode($patient_info['visula_check']); ?>
                    </div>
                    <div>
                        <?php echo $checkInfoAttributeLabels['hearing_check']."："; echo Html::encode($patient_info['hearing_check']); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>营养与饮食</th>
                <td colspan="2">
                    <div>
                        <?php echo $checkInfoAttributeLabels['feeding_patterns']."："; echo Html::encode($patient_info['feeding_patterns']); ?>
                    </div>
                    <div>
                        <?php echo $checkInfoAttributeLabels['feeding_num']."："; echo Html::encode($patient_info['feeding_num']); ?>
                    </div>
                    <div>
                        <?php echo $checkInfoAttributeLabels['substitutes']."："; echo Html::encode($patient_info['substitutes']); ?>
                    </div>
                    <div>
                        <?php echo $checkInfoAttributeLabels['dietary_supplement']."："; echo Html::encode($patient_info['dietary_supplement']); ?>
                    </div>
                    <div>
                        <?php echo '食物种类'."：";
                            $foodTypeArr = explode(',',$patient_info['food_types']);
                            $foodTypeStr = '';
                            foreach($foodTypeArr as $key => $value){
                                $foodTypeStr.= ChildExaminationInfo::$getFoodType[$value].'、';
                            }
                            $foodType = rtrim($foodTypeStr,'、');
                            echo $foodType;
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th>发育评估：</th>
                <td class="padding-none" colspan="2">
                    <?php
                        echo '<table id="w1" class="table recipe-table child-assessment"><tbody>';
                            echo '<tr>';
                            echo '<th>' . "ASQ-3评估结果";
                            echo  '</th>';
                            $str = '';
                            $str .= Html::tag('div', $assessmentAttributeLabels['communicate'].'：' .(empty($patient_info['communicate'])?'-':ChildExaminationAssessment::$getCommunicate[$patient_info['communicate']]));
                            $str .= Html::tag('div', $assessmentAttributeLabels['coarse_action'].'：' .(empty($patient_info['coarse_action'])?'-':ChildExaminationAssessment::$getCommunicate[$patient_info['coarse_action']]));
                            $str .= Html::tag('div', $assessmentAttributeLabels['fine_action'].'：' .(empty($patient_info['fine_action'])?'-':ChildExaminationAssessment::$getCommunicate[$patient_info['fine_action']]));
                            $str .= Html::tag('div', $assessmentAttributeLabels['solve_problem'].'：' .(empty($patient_info['solve_problem'])?'-':ChildExaminationAssessment::$getCommunicate[$patient_info['solve_problem']]));
                            $str .= Html::tag('div', $assessmentAttributeLabels['personal_society'].'：' .(empty($patient_info['personal_society'])?'-':ChildExaminationAssessment::$getCommunicate[$patient_info['personal_society']]));
                            echo '<td style="width: 85%">' . $str . '</td>';
                            echo '</tr>';

//                            echo '<tr>';
//                            echo '<th>' . "ASQ-SE评估结果";
//                            echo  '</th>';
//                            $str = '';
//                            $str .= Html::tag('div', $assessmentAttributeLabels['score'].'：' .Html::encode($patient_info['score']));
//                            $str .= Html::tag('div',$assessmentAttributeLabels['evaluation_result'].'：'.(empty($patient_info['evaluation_result'])?'-':ChildExaminationAssessment::$getEvaluationResult[$patient_info['evaluation_result']]));
//                            echo '<td style="width: 85%">' . $str . '</td>';
//                            echo '</tr>';

                            $str = empty($patient_info['evaluation_type_result']) ? '-' : Html::encode($patient_info['evaluation_type_result']);
                            echo '<tr>';
                            echo '<th>' . "其他评估结果";
                            echo  '</th>';
                            echo '<td style="width: 85%">' . $str . '</td>';
                            echo '</tr>';
                    echo '</tbody></table>';
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $checkInfoAttributeLabels['inspect_content']."：";?></th>
                <td colspan="2">
                    <div>
                        <?php echo Html::encode($patient_info['inspect_content']); ?>
                    </div>
                </td>
            </tr>
<!--            <tr>-->
<!--                <th>初步诊断：</th>-->
<!--                <td colspan="2">-->
<!--                    --><?php
//                    if (!empty($patient_info['evaluation_diagnosis'])) {
//                        echo  Html::encode($patient_info['evaluation_diagnosis']);
//                    }else{
//                        echo '-';
//                    }
//                    ?>
<!--                </td>-->
<!--            </tr>-->
            <tr>
                <th>指导意见：</th>
                <td colspan="2">
                    <?php
                    if (!empty($patient_info['evaluation_guidance'])) {
                        echo  Html::encode($patient_info['evaluation_guidance']);
                    }else{
                        echo '-';
                    }
                    ?>
                </td>
            </tr>
            </tbody>
    </table>