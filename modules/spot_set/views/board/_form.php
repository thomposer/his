<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot_set\models\Board;
use kartik\widgets\FileInput;
use yii\helpers\Url;
use app\assets\AppAsset;
use yii\widgets\Pjax;
AppAsset::addCss($this, '@web/public/css/board/board.css');
AppAsset::addCss($this, '@web/public/css/outpatient/upload.css');
AppAsset::addCss($this, '@web/public/css/outpatient/preview.css');
$attribute = $model->attributeLabels();
$img_type = array('gif','jpg','png','jpeg','bmp');
$fileList = $val['file_url']?$val['file_url']:array();
$fileNameList = $val['file_name']?$val['file_name']:[];
$fileSizeList = $val['size']?$val['size']:[];
$fileIdList = $val['id']?$val['id']:[];
$extension = pathinfo($val['file_url'], PATHINFO_EXTENSION);
$initialPreviewConfig = [];
if(!empty($fileList)){
    if($extension == 'pdf'){
        $fileList = '<embed class="kv-preview-data" src="'.Yii::$app->params['cdnHost'].$val['file_url'].'" width="100%" height="100%" type="application/pdf">';
    }else if(in_array($extension, $img_type)){
        $fileList = '<img src="'.Yii::$app->params['cdnHost'].$val['file_url'].'" class="kv-preview-data file-preview-image" style="max-height:100%;max-width:100%;">';
    }else{
        $fileList = '<div class="file-preview-other"  style="width:auto;height:auto;"><span class="file-other-icon"><i class="glyphicon glyphicon-file"></i></span></div>';
    }
    $initialPreviewConfig[] = [
        'caption' => $fileNameList,
        'size' => $fileSizeList,
        'url' => Url::to(['@apiUploadBoardDelete']),
        'key' => $fileIdList
    ];
}
    $layoutTemplates = [
        'btnBrowse' => '<div tabindex="500" class="{css}" {status}>{icon} <span class="hidden-xs">选择附件</span></div>',
        'btnLink' => '<a href="{href}" tabindex="500" title="{title}" class="{css}" {status}>{icon}<span class="hidden-xs">上传附件</span></a>',
        'main1' => '{preview}'.
            '<div class="kv-upload-progress hide"></div>'.
            '<div class="input-group {class}">'.
            '{caption}'.
            '<div class="input-group-btn input-group-btn-custom">'.
            '{remove}'.
            '{cancel}'.
            '{browse}'.
            '{upload}'.
            '</div>'.
            '</div>',
    ];
?>
<div class="board-form col-md-12">

    <?php $form = ActiveForm::begin([]); ?>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label($attribute['name'].'<span class = "label-required">*</span>') ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'status')->dropDownList(Board::$getStatus,['prompt' => '请选择'])->label($attribute['status'].'<span class = "label-required">*</span>') ?>
            </div>
        </div>

    <div class="board-record-index ">
        <div class = 'box shadow'>
            <div class="board-upload medical-upload-custom">
                <label class="col-sm-12 header  header-custom" >上传附件</label>
                    <?=
                    FileInput::widget([
                        'name' => 'board[]',
                        'id' => 'board'.$val['id'],
                        'options'=>[
                            'multiple'=>true,
                            'language' => 'zh-CN',
                        ],
                        'language' => 'zh-CN',
                        'pluginOptions' => [
                            'layoutTemplates' => $layoutTemplates,
                            'previewClass' => 'pre-custom',
                            'maxFileSize' => '4096',
                            'uploadUrl' => Url::to(['@apiUploadBoardUpload']),
                            'initialPreview' => $fileList,
                            'initialCaption' => '公告附件',
                            'initialPreviewConfig' => $initialPreviewConfig,
                            'maxFileCount' => 1,
                            'showPreview' => true,
                            'dropZoneTitle' => '',
                            'fileActionSettings' =>[
                                'removeIcon' => '<i class="icon_custom icon_button_view fa fa-trash-o" id = "trash-file"></i>',
                                'removeClass' => 'btn-upload-custom',
                                'zoomIcon' =>'<i class="icon_custom icon_button_view fa fa-eye"></i>',
                                'zoomClass' => 'btn-upload-custom',
                                'uploadIcon' => '<i class="icon_custom icon_button_view fa fa-upload"></i>',
                                'uploadClass' => 'btn-upload-custom',
                                'indicatorNew' => '<i class="fa icon_button_view_red fa-exclamation icon_custom"></i>',
                                'indicatorLoading' => '<i class="fa icon_button_view_red fa-exclamation icon_custom"></i>',
                            ],
                            'uploadClass' => 'btn btn-upload-action-custom',
                            'uploadIcon' => '',
                            'browseIcon' => '<i class="fa fa-plus"></i>',
                            'browseClass' => 'btn btn-primary btn-browse-custom',
                            'showClose' => false,
                            'showRemove' => false,
                            'showUpload' => true,
                            'showCancel' => false,
                            'initialPreviewAsData' => false,
                        ],
                        'pluginEvents' => [
                            // 上传成功后的回调方法，需要的可查看data后再做具体操作，一般不需要设置
                            "fileuploaded" => "function (event, data, id, index) {
                            var json = data.jqXHR.responseJSON;
                            $('#board_name').val(json.name);
                            $('#board_file_name').val(json.name);
                            $('#board_size').val(json.size);
                            $('#board_type').val(json.type);
                            $('#board_url').val(json.url);
                        }",
                            //上传两个文件的时候，模拟点击删除按钮，删除第一个文件
                            'filebatchselected' => "function(event,data){
                            var cur_data = $('.pre-custom').find('.file-preview-frame');
                            if(cur_data.length >1){
                                var id = cur_data[1].id;
                                $('#'+id).find('.kv-file-remove').click();
                                showInfo('最多上传一个附件','150px',2);
                            }
                        }",
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
        <div class="row file_property">
            <?= $form->field($model, 'file_name')->hiddenInput(['id' =>'board_file_name','class'=>'file_name'])->label(false); ?>
            <?= $form->field($model, 'size')->hiddenInput(['id' =>'board_size'])->label(false); ?>
            <?= $form->field($model, 'file_url')->hiddenInput(['id' =>'board_url'])->label(false); ?>
            <?= $form->field($model, 'type')->hiddenInput(['id' =>'board_type'])->label(false); ?>
        </div>
    </div>
    <div class="form-group board-save">
        <?= Html::a('取消',Yii::$app->request->referrer,['class' => 'btn btn-cancel btn-form second-cancel board-add']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form board-submit', 'id'=>'board-file']) ?>
    </div>
    <?php ActiveForm::end(); ?>
<?php  $this->beginBlock('renderJs')?>
    <script type = "text/javascript">
        require([ baseUrl+ "/public/js/spot/board.js"], function (main) {
            main.init();
        });
    </script>
<?php  $this->endBlock()?>