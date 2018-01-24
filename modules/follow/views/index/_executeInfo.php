<?php

use dosamigos\datepicker\DatePicker;
use app\modules\follow\models\Follow;

if (isset($view) && $view == 1) {
    $readonly = 'readonly';
} else {
    $readonly = false;
}
?>




<div class = 'row title_child_div'>
    <div class = 'col-sm-12'>
        <p class="title_p">
            <span class="circle_span"></span>
            <span class="title_span">随访结果信息</span>
        </p>
    </div>
</div>


<?php if ($model->follow_state != 4): ?>
    <div class = 'row'>
        <div class = 'col-sm-4'>
            <?php
            $model->followExecutorName = $model->followExecutorName ? $model->followExecutorName : Yii::$app->user->identity->username;
            echo $form->field($model, 'followExecutorName')->textInput(['maxlength' => 11, 'readonly' => 'readonly'])->label($modelAttribute['followExecutorName'] . '<span class = "label-required">*</span>')
            ?>
        </div>
        <div class = 'col-sm-4'>
            <?= $form->field($model, 'follow_method')->dropDownList(Follow::$getFollowMethod, ['prompt' => '请选择', 'disabled' => $readonly ? true : false])->label($modelAttribute['follow_method'] . '<span class = "label-required">*</span>') ?>
        </div>

        <div class="col-sm-4">
            <?= $form->field($model, 'follow_state')->dropDownList(Follow::$getFollowResult, ['prompt' => '请选择', 'disabled' => $readonly ? true : false])->label('随访结果<span class = "label-required">*</span>') ?>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <?=
            $form->field($model, 'follow_remark')->textarea(['readonly' => $readonly, 'rows' => 7])->label($modelAttribute['follow_remark'] . '<span class = "label-required">*</span>')
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div>
                <label class="control-label" for="upload-mediaFile">上传附件</label>

                <?php
                $param = ['model' => $model, 'followFile' => $followFile];
                (isset($view) && $view == 1) && $param['hidden'] = 1;
                echo $this->render('_fileUpload', $param);
                ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-sm-4">
            <?php
            $model->cancelUserName = $model->cancelUserName ? $model->cancelUserName : Yii::$app->user->identity->username;
            echo $form->field($model, 'cancelUserName')->textInput(['maxlength' => 11, 'readonly' => readonly])->label($modelAttribute['cancelUserName'] . '<span class = "label-required">*</span>')
            ?>
        </div>
        <div class="col-sm-4">
            <?=
            $form->field($model, 'cancel_time')->textInput(['maxlength' => 11, 'readonly' => readonly])->label($modelAttribute['cancel_time'] . '<span class = "label-required">*</span>')
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?=
            $form->field($model, 'cancel_reason')->textarea(['readonly' => readonly, 'rows' => 7])->label($modelAttribute['cancel_reason'] . '<span class = "label-required">*</span>')
            ?>
        </div>
    </div>
<?php endif; ?>

