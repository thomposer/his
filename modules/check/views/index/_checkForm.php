<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\FileInput;
use app\assets\AppAsset;
$attribute = $model->attributeLabels();

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
        'id' => 'avatarCheck'.$val['id'],
        'eventId' => 'Inpsect'.$val['id'],
        'type' => isset($hidden)?3:1,
        'fileList' => $fileList,
        'fileNameList' => $fileNameList,
        'fileSizeList' => $fileSizeList,
        'fileIdList' => $fileIdList,
        'uploadUrl' => Url::to(['@apiUploadIndex']),
        'deleteUrl' => Url::to(['@apiUploadDelete']),
        'extraData' => [
            'check_record_id' => $val['id'],
            'record_id' => $report['record_id']
             ],
];

AppAsset::addCss($this, '@web/public/css/outpatient/upload.css');
/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="fill-info">
    <span class="item_title"><?= Html::encode($val['name']); ?></span>
    <?= $this->render(Yii::getAlias('@orderFillerInfo'),$report) ?>
</div>

<div class="check-record-index col-xs-12">
    <div class = 'box'>
    <?php $form = ActiveForm::begin(['options' => [ 'class' => 'check-index-form','enctype' => 'multipart/form-data']]); ?>
           <div class="form-group medical-upload-custom">
           <label class="col-sm-12 header header-custom" >检查图片</label>
           <?php if(!empty($val['file_url']) || !isset($hidden)):?>
             <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => $fileUploadData]); ?>
            
           <?php else:?>
                <textarea readonly maxlength="255" rows="5" class="form-control texarea-custom">无上传图像</textarea>
           <?php endif;?>
           </div>
            <?= $form->field($model, 'description')->textarea(['readonly' => $status == 1?true:false,'rows' => 5,'maxlength' => '255','id'=>'description_'.$val['id'],'value' => $val['description'],'placeholder' => '描述症状及可能存在的病情等...'])->label($attribute['description'],['class'=>"col-sm-12 header"]) ?>

            <?= $form->field($model, 'result')->textarea(['readonly' => $status == 1?true:false,'rows' => 5,'maxlength' => '255','id' => 'result_'.$val['id'],'value' => $val['result'],'placeholder' => '描述治疗结论等...'])->label($attribute['result'],['class'=>"col-sm-12 header"]) ?>
    </div>
    <?php if(!isset($hidden)):?>
    <div class="form-group">
        <?php 
        if($status==2){ 
            echo Html::button('保存', ['class' => 'btn btn-default btn-form confirm-check']);
        }else{
            echo Html::button('修改', ['class' => 'btn btn-default btn-form update-check']);
            echo Html::button('打印报告', ['class' => 'btn btn-default btn-form print-check' ,'name'=>$val['id'].'myshow' ]);
        }
        ?>

    </div>

    <?php endif;?>
    <?php ActiveForm::end(); ?>
</div>

