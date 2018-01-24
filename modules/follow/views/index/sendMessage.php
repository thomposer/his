<?php

use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\assets\AppAsset;
use yii\helpers\Html;
?>
<?php
$fileList = [];
$fileNameList = [];
$fileSizeList = [];
$fileIdList = [];
$fileUploadData = [
    'id' => 'avatar' . 999,
    'eventId' => 'Medical' . 999,
    'type' => 1,
    'fileList' => $fileList,
    'fileNameList' => $fileNameList,
    'fileSizeList' => $fileSizeList,
    'fileIdList' => $fileIdList,
    'uploadUrl' => Url::to(['@apiFollowMessageUpload']),
    'deleteUrl' => Url::to(['@apiFollowMessageDelete']),
    'extraData' => [
        'follow_id' => 999,
    ],
    'maxFileCount' => 1
];
AppAsset::addCss($this, '@web/public/css/outpatient/upload.css');

?>
<?php
    $css = <<<CSS
   .modal-footer .form-group .btn-form{
            margin-top:40px!important;
   }    
            
CSS;
    $this->registerCss($css);
?>

<div class="clinic-cure-form col-md-12">

    <?php $form = ActiveForm ::begin(); ?>


    <div class='row'>
        <?= $form->field($model, 'message')->textarea(['rows' => 6, 'placeholder' => '消息内容（限制字数为300）'])->label(false) ?>
    </div>
    <div class="row">
        <div class="image-upload mediaFile-upload">
            <?= Html::hiddenInput('Message[attachment]','',['class'=>'message-attachment']) ?>
           <?php echo $this->render('messageFileUpload', ['data' => $fileUploadData]); ?>
        </div>
        
    </div>
    <?php ActiveForm ::end(); ?>

</div>