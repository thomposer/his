<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\grid\GridView;
use kartik\widgets\FileInput;
use yii\helpers\Url;
use app\modules\outpatient\models\CureRecord;

$attribute = $model->attributeLabels();

$report['record_id'] = Yii::$app->request->get('id');
$report['type'] = 2;
$report['id'] = $val['id'];
if(isset($val['report_time'])){
    $report['report_time'] = $val['report_time'];
    $report['username'] = $val['username'];
}
$fileNameList = $val['file_name']?explode(',', $val['file_name']):array();
$fileSizeList = $val['size'][0]?explode(',', $val['size']):array();
$fileList = explode(',', $val['file_url']);

$fileUploadData = [
        'type' => 2,
        'id' => 'Check'.$val['id'],
        'name' => 'Check'.$val['id'],
        'fileList' => $fileList,
        'fileNameList' => $fileNameList,
        'fileSizeList' => $fileSizeList,
        'fileIdList' => $fileIdList,
];
/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="fill-info fill-info-custom">
    <span class="item_title"><?= Html::encode($val['name']); ?></span>
    <?= $this->render(Yii::getAlias('@orderFillerInfo'),$report) ?>
</div>

<div class="col-xs-12">
    <div class = 'check-box'>
        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'checkform'],
        ]) ?>

        
        <div class="form-group field-checkrecord-checkimg">
            <label class="col-sm-12 check-header check-custom" for="checkrecord-checkimg">检查图片</label>
            <?php if (!$val['file_url']): ?>
                <textarea readonly maxlength="255" rows="5" class="form-control check-texarea">无上传图像</textarea>
            <?php else: ?>
                <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => $fileUploadData]); ?>
            <?php endif ?>
        
        </div>
        <?= $form->field($model, 'description')->textarea(['readonly'=> true,'rows' => 5,'maxlength' => '255','value' => $val['description'],'class'=>'form-control check-texarea'])->label($attribute['description'],['class'=>"col-sm-12 check-header"]) ?>
        <?= $form->field($model, 'result')->textarea(['readonly' => true,'rows' => 5,'maxlength' => '255','value' => $val['result'],'class'=>'form-control check-texarea'])->label($attribute['result'],['class'=>"col-sm-12 check-header"]) ?>
    </div>

    <div class="form-group">
        <?php
            echo Html::button('打印报告', ['class' => 'btn btn-default btn-form print-check' ,'name'=>'Check'.$val['id'] ]);
        ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>