<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\assets\AppAsset;
use kartik\widgets\FileInput;
// $attribute = $model->attributeLabels();

$report['record_id'] = Yii::$app->request->get('id');
$report['type'] = 2;
$report['id'] = $val['id'];
if(isset($val['report_time'])){
    $report['report_time'] = $val['report_time'];
    $report['username'] = $val['username'];
}
$fileList = $val['file_url'][0]?explode(',', $val['file_url']):array();
$fileNameList = $val['file_name']?explode(',', $val['file_name']):array();
$fileSizeList = $val['size'][0]?explode(',', $val['size']):array();
$fileIdList = $val['file_id'][0]?explode(',', $val['file_id']):array();

$fileUploadData = [
        'id' => 'avatar' . $val['id'],
        'eventId' => 'makeupCheckUpload'.$val['id'],
        'previewClass' => 'im-custom',
        'type' => 1,
        'fileList' => $fileList,
        'fileNameList' => $fileNameList,
        'fileSizeList' => $fileSizeList,
        'fileIdList' => $fileIdList,
        'uploadUrl' =>Url::to(['@apiUploadIndex']),
        'deleteUrl' => Url::to(['@apiUploadDelete']),
        'extraData' => [
            'check_record_id' => $val['id'],
            'record_id' => $report['record_id']
             ],
        'tActions' => '{upload} {delete}',
];
/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */
AppAsset::addCss($this, '@web/public/css/patient/tab.css');
?>

<div class="check-record-index col-xs-12">
    <div class = 'box'>
    <?php $form = ActiveForm::begin(['options' => [ 'class' => 'check-index-form','enctype' => 'multipart/form-data']]); ?>
           <div class="form-group" id="check-upload-<?= Html::encode($val['id']); ?>">
           <label class="col-sm-12 check-header" >上传附件</label>
            <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => $fileUploadData]); ?>
           </div>
    </div>

<script type = "text/javascript">
//            define = '';
</script>
    <?php ActiveForm::end(); ?>
</div>

