<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\widgets\FileInput;
use app\modules\inspect\models\InspectRecordUnion;
$report['record_id'] = Yii::$app->request->get('id');
$report['type']=1;
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
        'id' => 'Inspect'.$val['id'],
        'name' => 'Inspect'.$val['id'],
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
    <?php
    $form = ActiveForm::begin([
        'options' => ['class' => 'inspect-report'],
    ])
    ?>

    <div class = 'box'>
        <div class="form-group field-checkrecord-checkimg">
            <label class="col-sm-12 check-header check-custom" for="checkrecord-checkimg">检查图片</label>
            <?php if (!$val['file_url']): ?>
                <textarea readonly maxlength="255" rows="5" class="form-control check-texarea">无上传图像</textarea>
            <?php else: ?>
                <?php echo $this->render(Yii::getAlias('@fileUpload'), ['data' => $fileUploadData]); ?>
            <?php endif ?>
        </div>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'grid-view table-responsive', 'id' => 'grid'],
            'tableOptions' => ['class' => 'table table-hover cure-form inspect-tbody'],
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
                ],
                [
                    'attribute' => 'result',
                    'headerOptions' => ['class' => 'col-sm-2'],
                    'format' => 'raw',
                    'header' => '检查结果',
                    'value' => function($model){
                        $html = Html::encode($model->result);
                        $options = [];
                        if(in_array(strtoupper($model->result_identification), ['H','HH','P','Q','E'])){
                            $options['class'] = 'red';
                        }else if(in_array(strtoupper($model->result_identification), ['L','LL'])){
                            $options['class'] = 'blue';
                        }
                        $text = Html::tag('span',$html,$options);
                        return $text;
                    }
                ],
                [
                    'attribute' => 'result_identification',
                    'format' => 'raw',
                    'header' => '结果判断',
                    'value' => function ($model){
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
    <div class="form-group">
        <?php
        echo Html::button('打印报告', ['class' => 'btn btn-default btn-print print-check' ,'name'=>'Inspect'.$val['id']]);
        ?>
    </div>
    <?php ActiveForm::end() ?>
</div>
