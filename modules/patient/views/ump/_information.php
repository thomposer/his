<?php

use yii\helpers\Html;
use app\modules\patient\models\Patient;
use app\assets\AppAsset;
use app\modules\outpatient\models\Report;
use app\modules\triage\models\TriageInfo;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\RecipeRecord;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
/* @var $form yii\widgets\ActiveForm */
use app\modules\spot\models\RecipeList;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\Json;
use app\modules\patient\models\PatientRecord;

AppAsset::addCss($this, '@web/public/css/patient/ump.css');
AppAsset::addCss($this, '@web/public/css/outpatient/preview.css');
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<div class = 'ump-create-case col-sm-1 col-md-1'>
    <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/makeup-type', $this->params['permList'])): ?>
        <?php
        $createArr = ['class' => 'btn btn-default font-body2 makeup-type', 'data-pjax' => 0, 'role' => 'modal-create', 'contenttype' => 'application/x-www-form-urlencoded', 'data-modal-size' => 'small', 'data-url' => Url::to(['makeup-type'])];
        if (!Yii::$app->request->get('patientId')) {
            $createArr['disabled'] = 'disabled';
        }
        ?>
        <?= Html::button("<i class='fa fa-plus'></i>&nbsp;&nbsp;新增", $createArr) ?>
    <?php endif ?>
</div>
<?php
Pjax::begin([
    'id' => 'ump-record'
])
?>
<div class="patient-basic col-sm-12 col-md-12">
    <?php
    if (!empty($historyPatientInfo)) {
        echo "<div class='row treatment-form'>";
    }else{
        echo "<div class='row makeup-none'></div>";
    }
    ?>
    <?php foreach ($historyPatientInfo as $patient_info): ?>
        <div class="box box-success none_radius collapsed-box border-top">
            <div class="box-header with-border box-detail">
                <div class="box-tools pull-left">
                    <button data-widget="collapse" class="btn btn-box-tool btn-click" type="button"><i class="fa fa-plus"></i>
                    </button>
                </div>
                <div class="naviList"> <?= Html::encode(date('Y/m/d', $patient_info['diagnosis_time'])) ?> -  <?= Html::encode(PatientRecord::$getType[$patient_info['type']]) ?> - <?= Html::encode($patient_info['spot_name']) ?> - <?= Html::encode($patient_info['username']) ?>
                    <?php
                    if($patient_info['birthday'] > $patient_info['reportTime']){
                        echo '-（未出生）';
                    }else {
                        if (!empty($patient_info['birthday'])) {
                            echo  '-（'.Patient::dateDiffage($patient_info['birthday'],$patient_info['reportTime']).'）';
                        }
                    }
                    ?>
                </div>
                <div class="box-tools header-edit-top">
                    <?= Html::a('<i class="fa fa-edit header-edit-fa"></i>', ['makeup-base', 'recordId' => $patient_info['recordId'],], ['role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']) ?>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding"  style="display: none;">
                <div class=" basic-content-header">
                    <span class = 'basic-left-info'>
                        关键体征数据：
                    </span>
                    <span class="header-edit">
                        <?= Html::a('<i class="fa fa-edit header-edit-fa"></i>', ['signs-data', 'recordId' => $patient_info['recordId'],], ['role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']) ?>
                    </span>
                </div>
                <?php
                Pjax::begin([
                    'id' => 'ump_signsData' . $patient_info['recordId']
                ])
                ?>
                <table id="w0" class="table detail-view">
                    <tbody>
                        <tr>
                            <th>身高(cm)：</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['heightcm'])) {
                                    echo Html::encode($patient_info['heightcm']) . '(cm)';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>体重(kg)：</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['weightkg'])) {
                                    echo Html::encode($patient_info['weightkg']) . '(kg)';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>体温(℃)：</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['temperature'])) {
                                    echo Html::encode($patient_info['temperature']) . '℃';
                                    echo '－' . Html::encode(Patient::$temperature_type[$patient_info['temperature_type']]);
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>头围(cm)：</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['head_circumference'])) {
                                    echo Html::encode($patient_info['head_circumference']) . '(cm)';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>BMI指数：</th>
                            <td>
                                <?php
                                $bmi = Patient::getBmi($patient_info['heightcm'],$patient_info['weightkg']);
                                if(!is_null($bmi) && $bmi !== 0){
                                    echo $bmi."kg/m²";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>血型：</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['bloodtype']) && $patient_info['bloodtype']) {
                                    echo Html::encode($patient_info['bloodtype']) . '型';
                                }
                                $bloodTypeSupplement = $patient_info['blood_type_supplement']?explode(',',$patient_info['blood_type_supplement']):'';
                                if(!empty($bloodTypeSupplement)){
                                    $bloodTypeSupplementStr = '，';
                                    foreach($bloodTypeSupplement as $key => $value){
                                        $bloodTypeSupplementStr .= TriageInfo::$bloodTypeSupplement[$value].'，';
                                    }
                                    $bloodTypeSupplement = rtrim($bloodTypeSupplementStr,'，');

                                }
                                echo $bloodTypeSupplement;
                                ?>

                            </td>
                        </tr>
                        <tr>
                            <th> 呼吸（次/分钟）：</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['breathing'])) {
                                    echo Html::encode($patient_info['breathing']) . '（次/分钟）';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 脉搏（次/分钟）</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['pulse'])) {
                                    echo Html::encode($patient_info['pulse']) . '（次/分钟）';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 血压（mmHg）：</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['shrinkpressure'])) {
                                    echo "收缩压" . $patient_info['shrinkpressure'] . '（mmHg）';
                                }
                                if (!is_null($patient_info['diastolic_pressure'])) {
                                    echo "舒张压" . $patient_info['diastolic_pressure'] . '（mmHg）';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>氧饱和度（%）：</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['oxygen_saturation'])) {
                                    echo Html::encode($patient_info['oxygen_saturation']) . '%';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>疼痛评分（分）：</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['pain_score'])) {
                                    echo Html::encode($patient_info['pain_score']) . '分';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>跌倒评分（分）：</th>
                            <td>
                                <?php
                                if (!is_null($patient_info['fall_score'])) {
                                    echo Html::encode($patient_info['fall_score']) . '分';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>就诊方式：</th>
                            <td>
                                <?php
                                if (5 == $patient_info['treatment_type']) {
                                    echo Html::encode($patient_info['treatment']);
                                }else if(0 == $patient_info['treatment_type']){
                                    echo '';
                                }else{
                                    echo Html::encode(Patient::$treatment_type[$patient_info['treatment_type']]);
                                }
                                ?>
                            </td>
                        </tr>

                    </tbody>
                </table>
                <?php Pjax::end() ?> 
                <div class=" basic-content-header">
                    <span class = 'basic-left-info'>
                        历史病历：
                    </span>
                    <span class="header-edit">
                        <?= Html::a('<i class="fa fa-edit header-edit-fa"></i>', ['record-info', 'recordId' => $patient_info['recordId'],], ['role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']) ?>
                    </span>
                </div>
                <?php
                Pjax::begin([
                    'id' => 'ump_recordInfo' . $patient_info['recordId']
                ])
                ?>
                <table id="w0" class="table detail-view">
                    <tbody>
                        <tr>
                            <th>发病日期：</th>
                            <td>
                                <?php
                                if ($patient_info['incidence_date'] == 0) {
                                    echo '';
                                } else {
                                    echo date('Y-m-d', $patient_info['incidence_date']);
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 主诉：</th>
                            <td>
                                <?= Html::encode($patient_info['chiefcomplaint']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 现病史：</th>
                            <td>
                                <?= Html::encode($patient_info['historypresent']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 既往病史：</th>
                            <td>
                                <?= Html::encode($patient_info['pasthistory']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 过去用药史：</th>
                            <td>
                                <?= Html::encode($patient_info['pastdraghistory']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 食物过敏：</th>
                            <td>
                                <?= Html::encode($patient_info['food_allergy']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 药物过敏：</th>
                            <td>
                                <?= Html::encode($patient_info['meditation_allergy']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 个人史：</th>
                            <td>
                                <?= Html::encode($patient_info['personalhistory']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 家族史：</th>
                            <td>
                                <?= Html::encode($patient_info['genetichistory']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 体格检查：</th>
                            <td>
                                <?= Html::encode($patient_info['physical_examination']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 实验室及影像学检查：</th>
                            <td>
                                <?= Html::encode($patient_info['examination_check']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 初步诊断：</th>
                            <td>
                                <?= Html::encode($patient_info['first_check']) ?>
                            </td>
                        </tr>

                        <tr>
                            <th> 治疗意见：</th>
                            <td>
                                <?= Html::encode($patient_info['cure_idea']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 随诊：</th>
                            <td>
                                <?= Html::encode($patient_info['followup']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th> 备注：</th>
                            <td>
                                <?= Html::encode($patient_info['remark']) ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="makeup-file-header">
                                    上传附件：
                                </div>
                                <div id="file-upload-<?= $patient_info['recordId'] ?>">
                                <?= $this->render('_fileUpload', ['patientInfo' => $patient_info]) ?>
                                    </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php Pjax::end() ?> 
                <div class=" basic-content-header">
                    <span class = 'basic-left-info'>
                        医嘱信息：
                    </span>
                    <span class="header-edit">
                        <?= Html::a('<i class="fa fa-edit header-edit-fa"></i>', ['makeup/reception', 'id' => $patient_info['recordId'], 'patientId' => $patient_info['id'], 'doctorId' => $patient_info['doctor_id']], ['role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']) ?>
                    </span>
                </div>
                <?php
                Pjax::begin([
                    'id' => 'ump_reception' . $patient_info['recordId']
                ])
                ?>
                <table id="w0" class="table detail-view">
                    <tbody>
                        <tr>
                            <th> 实验室检查：</th>
                            <td>
                                <?php

                                $inspectName = $inspectData[$patient_info['recordId']]['name'];
                                echo Html::encode(rtrim($inspectName, ','));
                                ?>
                            </td>
                            <td>
                                <span class="outpatient-header-edit">
                                    <?= Html::a('<i class="fa fa-upload"></i>', ['makeup/inspect-upload', 'id' => $patient_info['recordId']], ['role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>影像学检查：</th>
                            <td>
                                <?php
//                                 $checkRecord = CheckRecord::getCheckRecord($patient_info['recordId']);
                                $checkRecord = $checkData[$patient_info['recordId']]['name'];
//                                 $str = '';
//                                 foreach ($checkRecord as $key => $value) {
//                                     $str.=Html::encode($value['name']) . ',';
//                                 }
                                echo Html::encode(rtrim($checkRecord, ','));
                                ?>
                            </td>
                            <td>
                                <span class="outpatient-header-edit">
                                    <?= Html::a('<i class="fa fa-upload"></i>', ['makeup/check-upload', 'id' => $patient_info['recordId']], ['role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']) ?>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th> 治疗：</th>
                            <td>
                                <?php
//                                 $itemRecord = CureRecord::getCureRecord($patient_info['recordId']);
                                $str = '';
                                $cureItemRecord = $cureData[$patient_info['recordId']];
                                if(!empty($cureItemRecord)){
                                    unset($cureItemRecord['name']);
                                    foreach ($cureItemRecord as $key => $value) {
                                        $remark = empty($value['remark'])?'':'（'. Html::encode($value['remark']). '）';
                                        $str.=Html::encode($value['name']) .$remark.'<br>';
                                    }
                                }
                                echo rtrim($str, ',');
                                ?>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <th> 处方：</th>
                            <td>
                                <?php
//                                 $itemRecord = RecipeRecord::getRecipeRecord($patient_info['recordId']);
                                $str = '';
                                $recipeRecord = $recipeData[$patient_info['recordId']];
                                if(!empty($recipeRecord)){
                                    unset($recipeRecord['name']);
                                    foreach ($recipeRecord as $key => $value) {
                                        $str.=Html::encode($value['name']) . ',';
                                    }
                                }
                                echo rtrim($str, ',');
                                ?>
                            </td>
                            <td></td>
                        </tr>

                    </tbody>
                </table>

                <?php
                $record = Report::checkReport($patient_info['recordId'],1,$reportType=3);
                if ($record) {
                    echo '<div class = "row reportCheck">';
                    echo Html::a('查看报告', ['@patientIndexInformation', 'id' => $patient_info['recordId'],'reportType'=>3],['class' => 'btn btn-default', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']);
                    echo '</div>';
                }
                ?>
                <?php Pjax::end() ?> 
                <div class=" basic-content-header">
                    <span class = 'basic-left-info'>
                        收费信息：
                    </span>
                    <span class="header-edit">
                        <?= Html::a('<i class="fa fa-edit header-edit-fa"></i>', ['makeup/charge', 'id' => $patient_info['recordId'], 'patientId' => $patient_info['id'], 'doctorId' => $patient_info['doctor_id']], ['role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large']) ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>
<?php Pjax::end() ?> 
<?php $this->beginBlock('renderJs') ?>
<script type = "text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    var defaultUsed = <?= Json::encode(RecipeList::$getDefaultUsed, JSON_ERROR_NONE) ?>;
    var defaultFrequency = <?= Json::encode(RecipeList::$getDefaultConsumption, JSON_ERROR_NONE) ?>;
    var defaultUnit = <?= Json::encode(RecipeList::$getUnit, JSON_ERROR_NONE) ?>;
    var defaultAddress = <?= Json::encode(RecipeList::$getAddress, JSON_ERROR_NONE) ?>;
    require(['<?= $baseUrl ?>' + '/public/js/patient/ump_record.js?v=' + '<?= $versionNumber ?>'], function (main) {
        main.init();
    })
</script>
<?php
$this->endBlock()?>