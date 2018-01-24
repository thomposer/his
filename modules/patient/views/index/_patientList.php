<?php

use app\assets\AppAsset;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use app\modules\patient\models\Patient;
use yii\helpers\Url;

AppAsset::addCss($this, '@web/public/css/lib/tabSwift.css');
AppAsset::addCss($this, '@web/public/css/outpatient/preview.css');
AppAsset::addCss($this, '@web/public/css/patient/patient.css');
AppAsset::addCss($this, '@web/public/css/lib/patient_record.css');
AppAsset::addCss($this, '@web/public/css/inspect/inspect.css');
AppAsset::addCss($this, '@web/public/css/patient/dentalCheckRow.css');
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php
if (empty($patientRecord)) {
    echo Html::tag('div', '没有找到数据。', ['class' => 'patient-no-data']);
}
?>
<?php foreach ($patientRecord as $key => $patient_info): ?>
    <?php
    $fileList = $patient_info['file_url'][0] ? explode(',', $patient_info['file_url']) : array();
    $fileNameList = $patient_info['file_name'] ? explode(',', $patient_info['file_name']) : array();
    $fileSizeList = $patient_info['size'][0] ? explode(',', $patient_info['size']) : array();
    $fileIdList = $patient_info['file_id'][0] ? explode(',', $patient_info['file_id']) : array();

    $fileUploadData = [
        'name' => 'medicalFile[]',
        'id' => 'medicalFile' . $patient_info['recordId'],
        'eventId' => 'medicalFile',
        'type' => 4,
        'fileList' => $fileList,
        'fileNameList' => $fileNameList,
        'fileSizeList' => $fileSizeList,
        'fileIdList' => $fileIdList,
    ];
    ?>

    <div class="patient-container">
        <div class="head-img-container fl">
            <a href="<?= Url::to(['@patientIndexView', 'id' => $patient_info['patient_id'],]) ?>" data-pjax=0
               target="_blank">
                <div class="img-container">
                    <img onerror="this.src='<?= $baseUrl ?>/public/img/default.png'" src=
                         "<?php
                         if ($patient_info['head_img']) {
                             echo Yii::$app->params['cdnHost'] . $patient_info['head_img'];
                         }
                         ?>" class="head-img"/>
                         <?php
                         if ($patient_info['sex'] == 1) {
                             echo '<i class="fa fa-mars sex-img icon-male"></i>';
                         } else if ($patient_info['sex'] == 2) {
                             echo '<i class="fa fa-venus sex-img icon-female"></i>';
                         }
                         ?>
                </div>

                <div class="patient-name-left"><?= Html::encode($patient_info['username']) ?></div>
            </a>
        </div>

        <div data-toggle="collapse" data-target="#treatment-form-<?= $patient_info['recordId'] ?>"
             class="patient-more">
            <span>更多</span>
            <i class="fa fa-angle-down"></i>
        </div>
        <!--    用户基本信息展示-->
        <div class="patient-head-box fl">

            <!--        用户的基本信息-->
            <div class="patient-user-basic">
                <span class="common-span">门诊号：<?= $patient_info['case_id'] ?>，  </span>
                <span class="common-span">

                    <span class="common-span">
                        就诊年龄：
                        <?php
                        if ($patient_info['birthday'] > $patient_info['reportTime']) {
                            echo '未出生';
                        } else {
                            if (!empty($patient_info['birthday'])) {
                                echo Patient::dateDiffage($patient_info['birthday'], $patient_info['reportTime']);
                            }
                        }
                        ?>，
                    </span>

                </span>
                <span class="doctor-name common-span">  接诊医生：<?= Html::encode($patient_info['doctorName']) ?></span>
                <?php
                if ($firstCheckData[$patient_info['recordId']] != '') {
                    echo '<span class="first-check common-span">' . "， " . "初步诊断：" . Html::encode($firstCheckData[$patient_info['recordId']]) . '</span>';
                }
                ?>
                <!--                <span class="spot-name common-span">-->
                <!--                    --><? //= Html::encode($patient_info['spot_name']) ?>
                <!--                </span>-->
                <!--                <span class="common-span">-->
                <!--                    -->
                <!--                -->
                <? //= $patient_info['diagnosis_time'] != 0 ? date('Y-m-d', $patient_info['diagnosis_time']) : ''; ?><!----><? //= Html::encode($patient_info['type_description']) ?>
                <!--                </span>-->
            </div>
            <div style="font-size: 12px;">
                <span class="doctor-name">
                    <?= "就诊诊所：" . Html::encode($patient_info['spot_name']) . "， " ?>
                </span>
                <span class="first-check">
                    <?= "就诊时间：" . ($patient_info['diagnosis_time'] != 0 ? date('Y-m-d', $patient_info['diagnosis_time']) : '') . "， " ?>
                    <?= "服务类型：" . Html::encode($patient_info['type_description']) ?>
                </span>
            </div>

            <!--        四大医嘱检验项目-->
            <div class="patient-height patient-item">
                <!--                实验室检查项目-->
                <?php
                $inspectStr = '';
                $inspectStr .= '<div class="inspect-item" title="实验室检查项目">';
                $inspectStr .= '<img class="pull-left" src="' . $baseUrl . '/public/img/patient/icon_inspect_item.png" alt="">';
                $inspectStr .= '<span class="pull-left">' . Html::encode(rtrim($inspectData[$patient_info['recordId']]['name'], ',')) . '</span>';
                $inspectStr .= '</div>';
                if ($inspectData[$patient_info['recordId']]['name']) {
                    echo $inspectStr;
                }
                ?>
                <!--                影像学检查项目-->
                <?php
                $checkStr = '';
                $checkStr .= '<div class="check-item" title="影像学检查项目">';
                $checkStr .= '<img class="pull-left" src="' . $baseUrl . '/public/img/patient/icon_check_item.png" alt="">';
                $checkStr .= '<span class="pull-left">' . Html::encode(rtrim($checkData[$patient_info['recordId']]['name'], ',')) . '</span>';
                $checkStr .= '</div>';
                if ($checkData[$patient_info['recordId']]['name']) {
                    echo $checkStr;
                }
                ?>
                <!--                治疗检查项目-->
                <?php
                $cureStr = '';
                $cureStr .= '<div class="cure-item" title="治疗检查项目">';
                $cureStr .= '<img class="pull-left" src="' . $baseUrl . '/public/img/patient/icon_cure_item.png" alt="">';
                $cureStr .= '<span class="pull-left">' . Html::encode(rtrim($cureData[$patient_info['recordId']]['name'], ',')) . '</span>';
                $cureStr .= '</div>';
                if ($cureData[$patient_info['recordId']]['name']) {
                    echo $cureStr;
                }
                ?>
                <!--                处方检查项目-->
                <?php
                $recipeStr = '';
                $recipeStr .= '<div class="recipe-item" title="处方检查项目">';
                $recipeStr .= '<img class="pull-left" src="' . $baseUrl . '/public/img/patient/icon_recipe_item.png" alt="">';
                $recipeStr .= '<span class="pull-left">' . Html::encode(rtrim($recipeData[$patient_info['recordId']]['name'], ',')) . '</span>';
                $recipeStr .= '</div>';
                if ($recipeData[$patient_info['recordId']]['name']) {
                    echo $recipeStr;
                }
                ?>
            </div>
    <!--            <span class="clearfix"></span>-->
        </div>
        <span class="clearfix"></span>
        <!--    更多病历信息展示-->
        <div id="treatment-form-<?= $patient_info['recordId'] ?>" class="patient-footer-box collapse">
            <div class="row treatment-form" style="padding-left: 83px;">
                <div class="box box-success none_radius border-top box-open">
                    <div class="box-body no-padding">

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
                        <!--                        关键体征数据：-->
                        <?= $this->render('_keySign', ['patient_info' => $patient_info, 'assessment' => $assessment]) ?>
                        <!--                        护理记录：-->
                        <?= $this->render('_nurseRecord', ['patient_info' => $patient_info, 'nurseRecordData' => $nurseRecordData]) ?>
                        <!--                        健康教育：-->
                        <?= $this->render('_healthEducation', ['patient_info' => $patient_info, 'healthEducationData' => $healthEducationData]) ?>

                        <!--                        历史病历：-->
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


                        <!--                        体检信息：-->

                        <!--                        医嘱信息：-->
                        <?=
                        $this->render('_orderInfo', [
                            'patient_info' => $patient_info,
                            'inspectData' => $inspectData,
                            'checkData' => $checkData,
                            'cureData' => $cureData,
                            'recipeData' => $recipeData,
                            'inspectReportData' => $inspectReportData,
                            'checkReportData' => $checkReportData,
                        ])
                        ?>
                    </div>
                </div>
            </div>
            <span class="clearfix"></span>
        </div>
    </div>

<?php endforeach; ?>
<div class="text-right table-padding-right">
    <?php
    // 列表摘要结果显示
    if ($pagination->totalCount > 0) { //当存在数据时显示
        echo '<div class="table-summary">( ' . $pagination->totalCount . ' 结果，共 ' . $pagination->pageCount . ' 页 )</div>';
    }
    ?>
    <?=
    LinkPager::widget([
        'pagination' => $pagination,
        'hideOnSinglePage' => false, //在只有一页时也显示分页
        'firstPageLabel' => Yii::getAlias('@firstPageLabel'),
        'prevPageLabel' => Yii::getAlias('@prevPageLabel'),
        'nextPageLabel' => Yii::getAlias('@nextPageLabel'),
        'lastPageLabel' => Yii::getAlias('@lastPageLabel'),
    ]);
    ?>
</div>

<?php
$this->registerJs("
        tabSwift('box-success');
        $('.patient-more').unbind('click').click(function(){
            var state = $(this).attr('aria-expanded');
            if(state == 'false' || state == undefined){
            
                $(this).find('.fa').removeClass('fa-angle-down');
                $(this).find('.fa').addClass('fa-angle-up');
            }else{
                $(this).find('.fa').addClass('fa-angle-down');
                $(this).find('.fa').removeClass('fa-angle-up');
            }
        });
        $('body').off('click','.kv-file-download').on('click','.kv-file-download',function(){
            var src = $(this).parents('.file-thumbnail-footer').siblings('.kv-file-content').children('.kv-preview-data').attr('src');
            window.open(src);
        });
            ");
AppAsset::addScript($this, '@web/public/js/lib/common.js');
?>
