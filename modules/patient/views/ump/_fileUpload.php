<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\FileInput;

$fileList = $patientInfo['file_url'][0] ? explode(',', $patientInfo['file_url']) : array();
$fileNameList = $patientInfo['file_name'] ? explode(',', $patientInfo['file_name']) : array();
$fileSizeList = $patientInfo['size'][0] ? explode(',', $patientInfo['size']) : array();
$fileIdList = $patientInfo['file_id'][0] ? explode(',', $patientInfo['file_id']) : array();
$fileUploadData = [
        'id' => 'avatar' . $patientInfo['recordId'],
        'eventId' => 'makeupMedical'.$patientInfo['recordId'],
        'type' => 1,
        'fileList' => $fileList,
        'fileNameList' => $fileNameList,
        'fileSizeList' => $fileSizeList,
        'fileIdList' => $fileIdList,
        'uploadUrl' =>Url::to(['@apiMedicalUpload']),
        'deleteUrl' => Url::to(['@apiMedicalDelete']),
        'extraData' => [
            'record_id' => $patientInfo['recordId']
             ],
];
?>


<div class="form-group image-upload">
    <?php if (!empty($patientInfo['file_url']) || !isset($hidden)): ?>
  <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => $fileUploadData]); ?>

    <?php else: ?>
        <textarea readonly maxlength="255" rows="5" class="form-control texarea-custom">无上传图像</textarea>
    <?php endif; ?>
</div>