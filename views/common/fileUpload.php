<?php

use kartik\widgets\FileInput;
use app\assets\AppAsset;

$fileList = $data['fileList'];
$fileNameList = $data['fileNameList'];
$fileSizeList = $data['fileSizeList'];
$fileIdList = $data['fileIdList'];
$img_type = array('gif', 'jpg', 'png', 'jpeg','bmp');
if (!empty($fileList)) {
    foreach ($fileList as $key => $v) {
        if ($v != '') {
            $extension = pathinfo($v, PATHINFO_EXTENSION);
            if (strtolower($extension) == 'pdf') {
                $fileList[$key] = '<embed class="kv-preview-data" src="' . Yii::$app->params['cdnHost'] . $v . '" width="100%" height="100%" type="application/pdf">';
            } else if (in_array(strtolower($extension), $img_type)) {
                $fileList[$key] = '<img src="' . Yii::$app->params['cdnHost'] . $v . '" class="kv-preview-data file-preview-image" style="max-height:100%;max-width:100%;">';
            } else {
                $fileList[$key] = '<div class="file-preview-other"  style="width:auto;height:auto;"><span class="file-other-icon"><i class="glyphicon glyphicon-file"></i></span></div>';
            }
            $initialPreviewConfig[] = [
                'caption' => $fileNameList[$key],
                'size' => $fileSizeList[$key],
                // 要删除商品图的地址
                'url' => $data['deleteUrl'],
                // 商品图对应的商品图id
                'key' => $fileIdList[$key],
            ];
        }
    }
}

!isset($data['type']) && $data['type'] = 1;
if ($data['type'] == 1) {//非弹窗:上传,预览或者弹窗：上传
    $layoutTemplates = [
        'btnBrowse' => '<div tabindex="500" class="{css}" {status}>{icon} <span class="hidden-xs">选择附件</span></div>',
        'btnLink' => '<a href="{href}" tabindex="500" title="{title}" class="{css}" {status}>{icon}<span class="hidden-xs">上传附件</span></a>',
        'main1' => '{preview}' .
        '<div class="kv-upload-progress hide"></div>' .
        '<div class="input-group {class}">' .
        '{caption}' .
        '<div class="input-group-btn input-group-btn-custom">' .
        '{remove}' .
        '{cancel}' .
        '{browse}' .
        '{upload}' .
        '</div>' .
        '</div>',
    ];
    if($data['tActions']){
        //设置单个图片下方显示样式
        $layoutTemplates['actions'] = '<div class="file-actions">'.
        '    <div class="file-footer-buttons">'.
        $data['tActions'].
        '    </div>' .
        '    {drag}' .
        '    <div class="file-upload-indicator" title="{indicatorTitle}">{indicator}</div>' .
        '    <div class="clearfix"></div>' .
        '</div>';
    }
    $showBrowse = true;
    $showUpload = true;
    $removeClass = 'btn-upload-custom';
    $zoomIcon = '<i class="icon_custom icon_button_view fa fa-eye"></i>';
    $pluginEvents = [
        // 上传成功后的回调方法，需要的可查看data后再做具体操作，一般不需要设置
        "fileuploaded" => "function (event, data, id, index) {
                            var json = data.jqXHR.responseJSON;
                            console.log(json);
                            if(json.errorCode == '1014' || json.errorCode == '2001'){
                                if($('#'+id).length == 0){
                                    return ;
                                }
                                var errResult = $('#'+id).find('.progress-bar-success');
                                $(errResult).html('error');
                                $(errResult).addClass('file-preview-error')
                                $('#'+id).find('.kv-file-remove').click();
                                showInfo(json.msg,'150px',2);
                            }
                            var remove = $('#'+id).find('.kv-file-remove');
                            $(remove).attr({'data-url' : json.url,'data-key': json.key,'data-new' : 1});
                        }",
        //选择文件后的回调方法
        'filebatchselected' => "function(event,data){
                            console.log(event);
                            console.log(data);
                            var cur_data = $('." . $data['eventId'] . "').find('.file-preview-frame');
                            console.log(cur_data);
                            if(cur_data.length > 6){
                                showInfo('超过6个附件','150px',2);
                                for(var i = 6;i < cur_data.length;i = i+1){
                                    var id = cur_data[i].id;
                                    $('#'+id).find('.kv-file-remove').click();
                                }
                            }
                            for(var i = 0;i < cur_data.length;i = i+1){
                                    var id = cur_data[i].id;
                                    if($('#'+id).attr('data-template') == 'text' || $('#'+id).attr('data-template') == 'object'){
                                        showInfo('类型不允许上传','150px',2);
                                        $('#'+id).find('.kv-file-remove').click();
                                    }
                                }
                        }",
    ];
} else if ($data['type'] == 2) {//非弹窗:预览
     $layoutTemplates = [
        'main1' => '{preview}'
    ];
    $pluginOptions = [
        'layoutTemplates' => $layoutTemplates,
        'previewClass' => 'pre-custom',
        'initialPreview' => $fileList,
        'showClose' => false,
        'showBrowse' => false,
        'showUpload' => false,
        'initialPreviewConfig' => $initialPreviewConfig,
        'fileActionSettings' => [
            'removeClass' => 'btn-upload-custom hide', //单个图片删除按钮样式
            'zoomIcon' => '<i class="icon_custom icon_button_view fa fa-eye"></i>', //单个图片预览图标
            'zoomClass' => 'btn-upload-custom', //单个图片预览按钮样式
        ],
    ];
} else if ($data['type'] == 3) {//弹窗:下载
    $layoutTemplates = [
        'actionZoom' => '<button type="button" class="kv-file-download {zoomClass}" title="{zoomTitle}">{zoomIcon}</button>',
    ];
    $showBrowse = false;
    $showUpload = false;
    $removeClass = 'btn-upload-custom';
    $zoomIcon = '<i class="icon_custom icon_button_view fa fa-eye"></i>';
} else if ($data['type'] == 4) {//非弹窗：下载，预览
    $showBrowse = false;
    $showUpload = false;
    $removeIcon = '<i class="icon_custom icon_button_view fa fa-download"></i>';
    $removeClass = 'kv-file-download btn-upload-custom';
    $removeTitle = '下载';
     $zoomIcon = '<i class="icon_custom icon_button_view fa fa-eye"></i>';
}
AppAsset::addCss($this, '@web/public/css/outpatient/preview.css');
?>

<?php if (2 == $data['type']): ?>
    <?=

    FileInput::widget([
        'name' => $data['name'] ? $data['name'] : 'avatar[]',
        'id' => $data['id'],
        'pluginOptions' => $pluginOptions,
    ]);
    ?>
<?php else: ?>
    <?=

    FileInput::widget([
        'name' => $data['name'] ? $data['name'] : 'avatar[]',
        'id' => $data['id'],
        'options' => [
            'multiple' => true,
            'language' => 'zh-CN',
        ],
        'language' => 'zh-CN',
        'sortThumbs' => false,
        'pluginOptions' => [
            'layoutTemplates' => $layoutTemplates,//可配置显示内容
            'previewClass' => 'pre-custom ' .$data['previewClass']. ' '.$data['eventId'],
            'maxFileSize' => '4096',
            'uploadUrl' => $data['uploadUrl'],
            'overwriteInitial' => false, //是否覆盖已经上传的文件
            'initialPreview' => $fileList,
            'initialCaption' => '影像学附件',
            'initialPreviewConfig' => $initialPreviewConfig,
            'maxFileCount' => isset($data['maxFileCount'])?$data['maxFileCount']:6,
            'dropZoneTitle' => '',
            'fileActionSettings' => [
                'removeIcon' => isset($removeIcon) ? $removeIcon : '<i class="icon_custom icon_button_view fa fa-trash-o"></i>', //单个图片删除图标
                'removeClass' => $removeClass, //单个图片删除按钮样式
                'removeTitle' => isset($removeTitle) ? $removeTitle : '删除文件',
                'zoomIcon' => $zoomIcon, //单个图片预览图标
                'zoomClass' => 'btn-upload-custom', //单个图片预览按钮样式
                'uploadIcon' => '<i class="icon_custom icon_button_view fa fa-upload"></i>', //单个图片上传图标
                'uploadClass' => 'btn-upload-custom', //单个图片上传按钮样式
                'indicatorNew' => '<i class="fa icon_button_view_red fa-exclamation icon_custom"></i>',//未上传提示
                'indicatorLoading' => '<i class="fa icon_button_view_red fa-exclamation icon_custom"></i>',//进度条
            ],
            'uploadClass' => 'btn btn-upload-action-custom', //批量上传按钮样式
            'uploadIcon' => '', //批量上传按钮图标
            'browseIcon' => '<i class="fa fa-plus"></i>', //选择按钮图标
            'browseClass' => 'btn btn-primary btn-browse-custom', //选择按钮样式
            'showRemove' => false, //是否显示批量删除按钮
//                    'showUpload' => true,//是否显示批量上传按钮
            'showCancel' => false, //是否显示取消上传按钮
//                    'initialPreviewAsData' => false,//不懂该参数是啥意思
            'uploadExtraData' => $data['extraData'],
            'showPreview' => true, //是否显示预览图
            'showBrowse' => $showBrowse, //是否显示选择按钮
            'showUpload' => $showUpload,
            'showClose' => false, //是否显示右上角的批量删除
        ],
        // 一些事件行为
        'pluginEvents' => $pluginEvents,
    ]);
    ?>
<?php endif ?>