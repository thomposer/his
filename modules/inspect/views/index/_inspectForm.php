<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\widgets\FileInput;
use app\assets\AppAsset;
use app\modules\spot\models\Inspect;
use app\modules\inspect\models\InspectRecordUnion;

$report['record_id'] = Yii::$app->request->get('id');
$report['type'] = 1;
$report['id'] = $val['id'];
if (isset($val['report_time'])) {
    $report['report_time'] = $val['report_time'];
    $report['username'] = $val['username'];
}
$fileList = $val['file_url'][0] ? explode(',', $val['file_url']) : array();
$fileNameList = $val['file_name'] ? explode(',', $val['file_name']) : array();
$fileSizeList = $val['size'][0] ? explode(',', $val['size']) : array();
$fileIdList = $val['file_id'][0] ? explode(',', $val['file_id']) : array();
$fileUploadData = [
    'id' => 'avatarInpsect' . $val['id'],
    'eventId' => 'Inpsect' . $val['id'],
    'type' => isset($hidden) ? 3 : 1,
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
];
AppAsset::addCss($this, '@web/public/css/outpatient/upload.css');
/* @var $this yii\web\View */
/* @var $model app\modules\cure\models\Cure */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="fill-info">
    <span class="item_title" href='#<?= $val['id'] ?>'><?= Html::encode($val['name']) . ($val['deliver'] == 1 ? Html::tag('span', '（外送）', ['class' => 'label-required']) : '') . '--' . $val['specimen_number']; ?></span>
    <?= $this->render(Yii::getAlias('@orderFillerInfo'), $report) ?>
</div>
<div class="inspect-record-index col-xs-12">
    <?php
    $form = ActiveForm::begin([
                'id' => 'inspect',
            ])
    ?>

    <div class = 'box shadow'>
        <div class="inspect-upload medical-upload-custom">
            <label class="col-sm-12 header  header-custom" >检查图片</label>
            <?php if (!empty($val['file_url']) || !isset($hidden)): ?>
                <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => $fileUploadData]); ?>
            <?php else: ?>
                <textarea maxlength="255" rows="5" class="form-control texarea-custom">无上传图像</textarea>
            <?php endif; ?>
        </div>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive', 'id' => 'grid'],
            'tableOptions' => ['class' => 'table table-hover cure-form'],
            'headerRowOptions' => ['class' => 'header'],
            'layout' => '{items}',
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['class' => 'col-sm-1'],
                    'header' => '序号',
                ],
                [
                    'attribute' => 'name',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'result',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'value' => function ($model)use($status) {
                $html = Html::encode($model->result);
                $options = [];
                if (in_array(strtoupper($model->result_identification), ['H', 'HH', 'P', 'Q', 'E'])) {
                    $options['class'] = 'red';
                } else if (in_array(strtoupper($model->result_identification), ['L', 'LL'])) {
                    $options['class'] = 'blue';
                }
                $hiddenHtml = "<input type='hidden' name='id" . $model->inspect_record_id . "' class='checkitemid' value='$model->id'>";
                if ($status == 2) {
                    $text = Html::input('text', 'InspectRecord[result]', $model->result, ['class' => 'form-control']);
                    $text.=$hiddenHtml;
                } else {
                    $text = Html::tag('span', $html, $options);
                    $text .= Html::input('text', 'InspectRecord[result]', $model->result, ['class' => 'form-control hid L-remark']);
                    $text .=$hiddenHtml;
                }
                return $text;
            }
                ],
                [
                    'attribute' => 'result_identification',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return InspectRecordUnion::getResultIdentification($model->result_identification);
                    }
                ],
                [
                    'attribute' => 'unit',
                    'headerOptions' => ['class' => 'col-sm-2'],
                ],
                [
                    'attribute' => 'reference',
                    'headerOptions' => ['class' => 'col-sm-2'],
                ],
            ],
        ]);
        ?>
    </div>
    <?php if (!isset($hidden)): ?>
        <div class="form-group">
            <?php if ($status == 2) { ?>
                <?= Html::button('保存', ['class' => 'btn btn-default btn-form confirm-inspect']) ?>
            <?php } else { ?>
                 <?= Html::button('修改', ['class' => 'btn btn-default btn-form update-inspect']) ?>
                 <?php if ($status ==1): ?>
                 	<?= Html::button('打印报告', ['class' => 'btn btn-default btn-form print-check', 'name' => $val['id'] . 'myshow']); ?>
            	 <?php endif; ?>
            <?php } ?>
            <?php if ($status ==1): ?>
                 <?= Html::button('补打条码', ['class' => 'btn btn-default btn-form print-label', 'name' => $val['id'] . 'label', 'specimen_number' => $val['specimen_number']]); ?>
            <?php endif; ?>
            <?= Html::a('报警', Url::to(['@apiMessageInspectWarn', 'id' => $val['id'],'doctorId'=>$val['id']]), ['class' => 'hide warnModal-' . $val['id'], 'data-modal-size' => 'normal', 'role' => 'modal-remote']) ?>
        </div>
    <?php endif; ?>
    <?php ActiveForm::end() ?>
</div>
