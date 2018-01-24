<?php

use yii\helpers\Html;
use app\modules\patient\models\Patient;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
/* @var $form yii\widgets\ActiveForm */

AppAsset::addCss($this, '@web/public/css/outpatient/preview.css');
AppAsset::addCss($this, '@web/public/css/patient/patient.css');
AppAsset::addCss($this, '@web/public/css/lib/patient_record.css');
AppAsset::addCss($this, '@web/public/css/inspect/inspect.css');
AppAsset::addCss($this, '@web/public/css/lib/tabSwift.css');
AppAsset::addCss($this, '@web/public/css/patient/dentalCheckRow.css');

$recordId = (Yii::$app->getRequest()->getQueryParam('recordId') && $isReturn) ? Yii::$app->getRequest()->getQueryParam('recordId') : 0;
?>

<div class="patient-basic col-sm-12 col-md-12">
    <?php
    if (!empty($historyPatientInfo)) {
        echo "<div class='row treatment-form'>";
    } else {
        echo '<div class="no-content">暂无内容</div>';
    }
    ?>
    <?php foreach ($historyPatientInfo as $key => $patient_info): ?>
        <?php
        $fileList = $patient_info['file_url'][0] ? explode(',', $patient_info['file_url']) : array();
        $fileNameList = $patient_info['file_name'] ? explode(',', $patient_info['file_name']) : array();
        $fileSizeList = $patient_info['size'][0] ? explode(',', $patient_info['size']) : array();
        $fileIdList = $patient_info['file_id'][0] ? explode(',', $patient_info['file_id']) : array();
        $fileUploadData = [
            'name' => 'medicalFile[]',
            'id' => 'medicalFile' . $patient_info['recordId'],
            'eventId' => 'medicalFile' . $val['id'],
            'type' => isset($hidden) ? 3 : 4,
            'fileList' => $fileList,
            'fileNameList' => $fileNameList,
            'fileSizeList' => $fileSizeList,
            'fileIdList' => $fileIdList,
        ];
        ?>

        <div class="box box-success none_radius collapsed-box border-top">
            <!--            <div class="box-header with-border box-detail" >-->
            <div class="medical-record-container" >
                <div class="box-tools">
                    <button data-widget="collapse" class="btn btn-box-tool btn-click" type="button"
                            id="showDetail<?php echo $patient_info['recordId'] ?>">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>

                <div class="medical-record-title-container">
                    <div class="naviList">
                        <div style="max-width: 90%;" class="single-line">
                            <?php echo '门诊号：' . $patient_info['case_id'] . "， " ?>
                            <?php
                            if ($patient_info['birthday'] > $patient_info['reportTime']) {
                                echo '接诊年龄: 未出生' . "， ";
                            } else {
                                if (!empty($patient_info['birthday'])) {
                                    echo '就诊年龄：' . Patient::dateDiffage($patient_info['birthday'], $patient_info['reportTime']) . "， ";
                                }
                            }
                            ?>
                            <?php echo "接诊医生：" . Html::encode($patient_info['doctor_name']) ?>
                            <?php
                            if ($firstCheckData[$patient_info['recordId']] != '') {
                                echo "， " . "初步诊断：" . Html::encode($firstCheckData[$patient_info['recordId']]);
                            }
                            ?>
                        </div>
                        <div>
                            <?php if ($patient_info['makeup'] == 2): ?>
                                <span class="makeup-icon">补录</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="color: #97A3B6;" class="single-line">
                        <?php echo "就诊诊所：" . Html::encode($patient_info["spot_name"]) . "， " ?>
                        <?php echo "就诊时间：" . Html::encode(date('Y/m/d', $patient_info['diagnosis_time'])) . "， " ?>
                        <?php echo "服务类型：" . Html::encode($patient_info['type_description']) ?>
                    </div>
                </div>

            </div>
            <!-- /.box-header -->
            <span class="clearfix"></span>
            <div class="box-body no-padding" style="display: none;">

                <ul class="list">
                    <li class="current" target="key-sign-<?= $patient_info['recordId'] ?>">关键体征数据</li>
                    <li target="nurse-record-<?= $patient_info['recordId'] ?>">护理记录</li>
                    <li target="health-education-<?= $patient_info['recordId'] ?>">健康教育</li>
                    <?php if ($patient_info['record_type'] == 2): ?>
                        <li target="check-info-<?= $patient_info['recordId'] ?>">儿童保健档案</li>
                    <?php else: ?>
                        <li target="history-record-<?= $patient_info['recordId'] ?>">病历</li>
                    <?php endif; ?>
                    <li target="order-info-<?= $patient_info['recordId'] ?>">医嘱信息</li>
                </ul>

           
                <?= $this->render('_keySign', ['patient_info' => $patient_info, 'assessment' => $assessment]) ?>
                <?= $this->render('_nurseRecord', ['patient_info' => $patient_info, 'nurseRecordData' => $nurseRecordData]) ?>


                <?= $this->render('_healthEducation', ['patient_info' => $patient_info, 'healthEducationData' => $healthEducationData]) ?>

                <?php 
                if ($patient_info['record_type'] == 4 || $patient_info['record_type'] == 5) {
                    //口腔
                    if ($patient_info['dental_type'] == 2) {
                        //复诊
                        echo $this->render('_dentalSecond', ['patient_info' => $patient_info, 'dentalHistoryData' => $dentalHistoryData, 'allergyOutpatient' => $allergyOutpatient, 'firstCheckData' => $firstCheckData]);
                    } else {
                        //初诊
                        echo $this->render('_dentalFirst', ['patient_info' => $patient_info, 'dentalHistoryData' => $dentalHistoryData, 'allergyOutpatient' => $allergyOutpatient, 'firstCheckData' => $firstCheckData]);
                    }
                } elseif ($patient_info['record_type'] == 6) {
                    //正畸初诊
                    echo $this->render('_orthFirst', ['patient_info' => $patient_info, 'allergyOutpatient' => $allergyOutpatient, 'firstCheckData' => $firstCheckData, 'fileUploadData' => $fileUploadData,]);
                } elseif ($patient_info['record_type'] == 7) {
                    //正畸复诊
                    echo $this->render('_orthSecond', ['patient_info' => $patient_info, 'allergyOutpatient' => $allergyOutpatient, 'firstCheckData' => $firstCheckData, 'fileUploadData' => $fileUploadData,]);
                } else if ($patient_info['record_type'] == 2) {
                    echo $this->render('_checkInfo', ['patient_info' => $patient_info, 'firstCheckData' => $firstCheckData, 'allergyOutpatient' => $allergyOutpatient]);
                } else {
                    echo $this->render('_historyRecord', ['patient_info' => $patient_info, 'fileUploadData' => $fileUploadData, 'firstCheckData' => $firstCheckData, 'allergyOutpatient' => $allergyOutpatient]);
                }
                ?>

                <?= $this->render('_orderInfo', ['patient_info' => $patient_info, 'isReturn' => $isReturn, 'inspectData' => $inspectData, 'checkData' => $checkData, 'cureData' => $cureData, 'recipeData' => $recipeData, 'inspectReportData' => $inspectReportData, 'checkReportData' => $checkReportData,]) ?>
            </div>
        </div>

    <?php endforeach; ?>
    <?php
    if (!empty($historyPatientInfo)) {
        echo "</div>";
    }
    ?>
</div>
<span class="clearfix"></span>

<?php
$this->registerJs("
        if($recordId){
            $('#showDetail$recordId').click();
        }
        tabSwift('box-success');
        $(\"#ajaxCrudModal .modal-content .box .box-body .detail-view .kv-file-remove\").hide();
        $(\"#ajaxCrudModal .modal-content .box .box-body .detail-view .kv-file-download i\").removeClass('fa-eye');
        $(\"#ajaxCrudModal .modal-content .box .box-body .detail-view .kv-file-download i\").addClass('fa-download');
            ");
AppAsset::addScript($this, '@web/public/js/lib/common.js');
?>


