<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\FileInput;
use app\assets\AppAsset;

$fileList = $medicalFile['file_url'][0] ? explode(',', $medicalFile['file_url']) : array();
$fileNameList = $medicalFile['file_name'] ? explode(',', $medicalFile['file_name']) : array();
$fileSizeList = $medicalFile['size'][0] ? explode(',', $medicalFile['size']) : array();
$fileIdList = $medicalFile['file_id'][0] ? explode(',', $medicalFile['file_id']) : array();
$fileUploadData = [
        'id' => 'avatar' . $patientInfo['recordId'],
        'eventId' => 'Medical'.$patientInfo['recordId'],
        'type' => 1,
        'fileList' => $fileList,
        'fileNameList' => $fileNameList,
        'fileSizeList' => $fileSizeList,
        'fileIdList' => $fileIdList,
        'uploadUrl' =>Url::to(['@apiMedicalUpload']),
        'deleteUrl' => Url::to(['@apiMedicalDelete']),
        'extraData' => [
            'record_id' => $model->record_id,
             ],
];

AppAsset::addCss($this, '@web/public/css/outpatient/upload.css')
?>


<div class="form-group image-upload mediaFile-upload">
    <?php if (!empty($patientInfo['file_url']) || !isset($hidden)): ?>
         <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => $fileUploadData]); ?>

    <?php else: ?>
        <textarea readonly maxlength="255" rows="5" class="form-control texarea-custom">无上传图像</textarea>
    <?php endif; ?>
</div>