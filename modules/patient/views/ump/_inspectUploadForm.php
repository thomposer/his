<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\widgets\FileInput;
use app\assets\AppAsset;

$report['record_id'] = Yii::$app->request->get('id');
$report['type'] = 1;
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
        'uploadUrl' => Url::to(['@apiInspectUpload']),
        'deleteUrl' => Url::to(['@apiInspectDelete']),
        'extraData' => [
            'inspect_record_id' => $val['id'],
            'record_id' => $report['record_id']
             ],
        'tActions' => '{upload} {delete}',
];
AppAsset::addCss($this, '@web/public/css/patient/tab.css');
/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="inspect-upload-index">
    <?php
    $form = ActiveForm::begin([
        'id' => 'inspect',
    ])
    ?>
    
    <div class = 'box shadow'>
        <div class="inspect-upload im-upload" id="insepct-upload-<?= Html::encode($val['id']); ?>">
           <label class="col-sm-12 inspect-header" >上传附件</label>
        <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => $fileUploadData]); ?>
        </div>
    </div>
<script type = "text/javascript">
//            define = '';//将define清空，避免输入框渲染不出来
</script>
    <?php ActiveForm::end() ?>
</div>
