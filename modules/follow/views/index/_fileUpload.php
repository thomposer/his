<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\FileInput;
use app\assets\AppAsset;


$fileList = $followFile['file_url'][0] ? explode(',', $followFile['file_url']) : array();
$fileNameList = $followFile['file_name'] ? explode(',', $followFile['file_name']) : array();
$fileSizeList = $followFile['size'][0] ? explode(',', $followFile['size']) : array();
$fileIdList = $followFile['file_id'][0] ? explode(',', $followFile['file_id']) : array();
$fileUploadData = [
    'id' => 'avatar' . $patientInfo['recordId'],
    'eventId' => 'Medical' . $patientInfo['recordId'],
    'type' => (isset($hidden)&&$hidden==1)?2:1,
    'fileList' => $fileList,
    'fileNameList' => $fileNameList,
    'fileSizeList' => $fileSizeList,
    'fileIdList' => $fileIdList,
    'uploadUrl' => Url::to(['@apiFollowUpload']),
    'deleteUrl' => Url::to(['@apiFollowDelete']),
    'extraData' => [
        'follow_id' => $model->id,
    ],
    'maxFileCount'=>5
];
//$fileUploadData = [
//    'id' => 'avatarInpsect' . $val['id'],
//    'eventId' => 'Inpsect' . $val['id'],
//    'type' => isset($hidden) ? 3 : 1,
//    'fileList' => $fileList,
//    'fileNameList' => $fileNameList,
//    'fileSizeList' => $fileSizeList,
//    'fileIdList' => $fileIdList,
//    'uploadUrl' => Url::to(['@apiInspectUpload']),
//    'deleteUrl' => Url::to(['@apiInspectDelete']),
//    'extraData' => [
//        'inspect_record_id' => $val['id'],
//        'record_id' => $report['record_id']
//    ],
//];

AppAsset::addCss($this, '@web/public/css/outpatient/upload.css')
?>


<div class="form-group image-upload mediaFile-upload">
    <?php if ((!empty($followFile)&&$followFile['file_id'])||(!isset($hidden)||$hidden==2)): ?>
        <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => $fileUploadData]); ?>

    <?php else: ?>
        <textarea readonly maxlength="255" rows="5" class="form-control texarea-custom">无上传图像</textarea>
    <?php endif; ?>
</div>