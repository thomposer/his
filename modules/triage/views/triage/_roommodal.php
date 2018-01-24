<?php

use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$css = <<<CSS
    #ajaxCrudModal .modal-body {
         border-top:1px solid #ddd;
    }
CSS;
$this->registerCss($css);
?>

<!-- 医生列表 -->
<?php foreach ($room as $v): ?>
    <div class="row my-row" id="j_roomList" record_id="<?= $record_id ?>">

        <div class="col-sm-8">
            <?php
            $status = $room_id == $v[id];
            if ($status) {
                $str = '<span class="active-lable">(当前)</span>';
            } else {
                $str = '';
            }
            ?>
            <?php
            if ($v['frequent'] == 1) {
                $strFre = '<span class="active-frequent">常用</span>';
            } else {
                $strFre = '';
            }
            ?>
            <label class="control-label my-lable" for=""><span class="clinic-name-modal"><?= Html::encode($v['clinic_name']) ?></span><?= $str . $strFre ?></label>
        </div>
        <div class="col-sm-4 mar">
            <?php if (!$status): ?>
                <?= Html::button('选择', ['class' => 'btn btn-default btn-chose-room', 'room_id' => $v[id]]) ?>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
<!--            <div class="row my-row mab-10" id="j_doctorList">-->
<!--            </div>-->
<!-- 医生列表 -->
<!--</div>-->
<!--</div>-->


<?php
$choseRoomUrl = Url::to(['@triageTriageChoseroom']);
$js = <<<JS
        var choseRoomUrl = "$choseRoomUrl";
        require(["$baseUrl/public/js/triage/triage.js?v=$versionNumber"], function (main) {
             main.init();
        });
JS;
$this->registerJs($js);
?>
<?php 