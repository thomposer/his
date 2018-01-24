<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/23
 * Time: 22:28
 */
use yii\helpers\Html;
use app\modules\spot\models\RecipeList;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\CureRecord;
use app\assets\AppAsset;

$recipeAttributeLabels = (new RecipeList())->attributeLabels();
AppAsset::addCss($this, '@web/public/css/outpatient/highRisk.css');
?>
<table id="w0" class="order-info-<?= $patient_info['recordId'] ?> table detail-view hidden">
    <tbody>
        <tr>
            <th> 实验室检查：</th>
            <td>
                <?php
//             $itemRecord = InspectRecord::getInspectRecord($patient_info['recordId']);
                $itemRecord = $inspectData[$patient_info['recordId']];
                $str = '';
                if (!empty($itemRecord)) {
                    $str .= Html::encode($itemRecord['name']);
//                 unset($itemRecord['name']);
//                 foreach ($itemRecord as $key => $value) {
//                     $str .= Html::encode($value['name']) . ',';
//                 }
                }
                $str = trim($str);
                echo rtrim($str, ',');
                ?>
            </td>
            <td>
                <?php
                //1代表实验室检查
//                $record = Report::checkReport($patient_info['recordId'], 1, 1);
//                if ($record) {
                if (isset($inspectReportData[$patient_info['recordId']]) && (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@patientIndexInformation'), $this->params['permList']))) {
                    echo Html::a('查看报告', ['@patientIndexInformation', 'id' => $patient_info['recordId'], 'reportType' => 1, 'isReturn' => $isReturn ? 1 : 0, 'patientId' => $patient_info['id']], ['class' => 'view-check-buttons', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']);
                }
                ?>
            </td>
        </tr>
        <tr>
            <th>影像学检查：</th>
            <td>
                <?php
//             $checkRecord = CheckRecord::getCheckRecord($patient_info['recordId']);
                $checkRecord = $checkData[$patient_info['recordId']];
                $str = '';
                if (!empty($checkRecord)) {
                    $str .= Html::encode($checkRecord['name']);
//                 unset($checkRecord['name']);
//                 foreach ($checkRecord as $key => $value) {
//                     $str .= Html::encode($value['name']) . ',';
//                 }
                }
                echo rtrim($str, ',');
                ?>
            </td>
            <td>
                <?php
                //2代表影像学检查
//            $record = Report::checkReport($patient_info['recordId'], 1, 2);
//            if ($record) {
              if (isset($checkReportData[$patient_info['recordId']]) && (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@patientIndexInformation'), $this->params['permList']))) {
                    echo Html::a('查看报告', ['@patientIndexInformation', 'id' => $patient_info['recordId'], 'reportType' => 2, 'isReturn' => $isReturn ? 1 : 0, 'patientId' => $patient_info['id']], ['class' => 'view-check-buttons', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']);
                }
                ?>
            </td>
        </tr>
        <tr>
            <th> 治疗：</th>
            <td colspan="2">
                <?php
//             $itemRecord = CureRecord::getCureRecord($patient_info['recordId']);
                $itemCureRecord = $cureData[$patient_info['recordId']];
                $str = '';
                if (!empty($itemCureRecord)) {
                    unset($itemCureRecord['name']);
                    foreach ($itemCureRecord as $key => $value) {
                        $cureResult = '';

                        if ($value['type'] == 1) {
                            $cureResult = empty(CureRecord::$getCureResult[$value['cure_result']]) ? '' : '，' . CureRecord::$getCureResult[$value['cure_result']];
                        } else {
                            $cureResult = empty($value['cure_result']) ? '' : '，' . Html::encode($value['cure_result']);
                        }


                        $otherDesc = '';
                        if (!empty($value['remark']) || !empty($value['description'])) {
                            $otherDesc .='（';
                            $otherDesc .= empty($value['description']) ? '' : Html::encode($value['description']);
                            if (!empty($value['remark'])) {
                                $otherDesc .= empty($value['description']) ? '' : '；';
                                $otherDesc .= Html::encode($value['remark']);
                            }
                            $otherDesc .='）';
                        }

                        $str .= Html::encode($value['name']) . $cureResult . $otherDesc . '<br>';
                    }
                }
                echo rtrim($str, '<br>');
                ?>
            </td>
        </tr>
        <tr>
            <?php
//         $recipRecord = RecipeRecord::getRecipeRecord($patient_info['recordId']);
            $recipRecord = $recipeData[$patient_info['recordId']];
            if (!empty($recipRecord)) {
                echo " <th class='recipe-border-right'> 处方：</th>";
            } else {
                echo " <th> 处方：</th>";
            }
            ?>
            <td class="padding-none" colspan="2">
                <?php
                if (!empty($recipRecord)) {
                    unset($recipRecord['name']);
                    echo '<table id="w1" class="table recipe-table"><tbody>';
                    foreach ($recipRecord as $key => $value) {
                        $specification = '';
                        empty($value['specification']) ? $specification = '' : $specification = '（' . $value['specification'] . '）';
                        echo '<tr>';

                        $highRisk = "";
                        if( 1 == $value['high_risk']){
                            $highRisk = '<span class="high-risk">'. RecipeList::$getHighRiskDesc[$value['high_risk']].'</span>';
                        }

                        echo '<th>'.$highRisk ." ". Html::encode($value['name']) . Html::encode($specification);
                        '</th>';
                        $str = '';
                        $str .= empty($value['dosage_form']) ? '' : Html::tag('div', $recipeAttributeLabels['type'] . ': ' . RecipeList:: $getType[$value['dosage_form']]);
                        $str .= empty($value['dose']) ? '' : Html::tag('div', '剂量：' . $value['dose'] . RecipeList:: $getDoseUnit[$value['dose_unit']]);
                        $str .= empty($value['used']) ? '' : Html::tag('div', '用法：' . RecipeList:: $getDefaultUsed[$value['used']]);
                        $str .= empty($value['frequency']) ? '' : Html::tag('div', '用药频次：' . RecipeList:: $getDefaultConsumption[$value['frequency']]);
                        $str .= $value['day'] == '' ? '' : Html::tag('div', '天数：' . $value['day'] . '天');
                        $str .= $value['num'] == '' ? '' : Html::tag('div', '数量：' . $value['num'] . RecipeList:: $getUnit[$value['unit']]);
//                                    $str .= empty($value['unit']) ? '' : Html::tag('div', '单位：' . RecipeList:: $getUnit[$value['unit']]);
                        if ($value['skin_test_status'] != 0) {
                            if ($value['skin_test_status'] == 1) {
                                $str .= '<span class="skin_test">需要皮试：</span>';
                                $str .= empty($value['skin_test']) ? '' : Html::encode($value['skin_test']);
                                $str .= empty($value['cureName']) ? '' : Html::tag('div', '皮试类型：' . Html::encode($value['cureName']));
                                $str .= $value['skin_test_status'] == 2 ? '' : Html::tag('div', '皮试结果：' . CureRecord::$getCureResult[$value['cureResult']]);
                            } else {
                                $str .= '免皮试';
                            }
                        }
                        echo '<td style="width: 30%">' . $str . '</td>';
                        $description = empty($value['description']) ? '' : Html::encode($value['description']);
                        $remark = empty($value['remark']) ? '' : Html::encode($value['remark']);
                        $out = Html::tag('div', '医嘱：' . $description) . '<br>' . Html::tag('div', '药师：' . $remark);
                        echo '<td>' . $out . '</td>';
                        echo '</tr>';
                    }
                }
                echo '</tbody></table>';
                ?>
            </td>
        </tr>
    </tbody>
</table>
