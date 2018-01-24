<?php

use dosamigos\datepicker\DatePicker;
use app\modules\follow\models\Follow;
use yii\helpers\ArrayHelper;

if (($model->follow_state > 1&&$update!=1) || (isset($readonly) && $readonly == 1)) {
    $readonly = 'readonly';
} else {
    $readonly = false;
}
?>

<div class = 'row title_child_div'>
    <div class = 'col-sm-12'>
        <p class="title_p">
            <span class="circle_span"></span>
            <span class="title_span">随访计划信息</span>
        </p>
    </div>
</div>
<div class = 'row'>
    <div class = 'col-sm-4'>
        <?php
        $data = $form->field($model, 'complete_time');
        if ($readonly) {
            $data = $data->textInput(['maxlength' => 11, 'readonly' => $readonly]);
        } else {
            $data = $data->widget(
                    DatePicker::className(), [
                'addon' => false,
                'inline' => false,
                'language' => 'zh-CN',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'startDate' => date('Y-m-d')
                ],
                'options' => [
                    'autocomplete' => 'off'
                ]
            ]);
        }
        $data = $data->label($modelAttribute['complete_time'] . '<span class = "label-required">*</span>');
        echo $data;
        ?>
    </div>
    <div class = 'col-sm-4'>
        <?= $form->field($model, 'execute_role')->dropDownList(Follow::$getExecuteRole, ['prompt' => '请选择', 'disabled' => $readonly?true:false])->label($modelAttribute['execute_role'] . '<span class = "label-required">*</span>') ?>
    </div>

    <div class="col-sm-4">
        <?php
        $model->planCreatorName = $model->planCreatorName?$model->planCreatorName:Yii::$app->user->identity->username;
        echo $form->field($model, 'planCreatorName')->textInput(['maxlength' => 11, 'readonly' => 'readonly'])->label($modelAttribute['planCreatorName'] . '<span class = "label-required">*</span>')
        ?>
    </div>

</div>

<div class="row">
    <div class = 'col-sm-4'>
        <?php
            echo $form->field($model, 'create_time')->textInput(['maxlength' => 11, 'readonly' => 'readonly'])->label($modelAttribute['create_time'] . '<span class = "label-required">*</span>');
        ?>
    </div>
    <div class = 'col-sm-4'>
        <?= $form->field($model, 'follow_plan_executor')->dropDownList(ArrayHelper::map($userInfo, 'id', 'username'), ['prompt' => '请选择', 'disabled' => $readonly?true:false])->label($modelAttribute['follow_plan_executor']) ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <?=
        $form->field($model, 'content')->textarea([ 'readonly' => $readonly, 'rows' => 7])->label($modelAttribute['content'] . '<span class = "label-required">*</span>')
        ?>
    </div>
</div>