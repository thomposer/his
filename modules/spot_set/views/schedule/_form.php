<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot_set\models\Schedule;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\Schedule */
/* @var $form yii\widgets\ActiveForm */
$labels = $model->attributeLabels();
?>

<div class="schedule-form col-md-8">
    <?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'shift_name')->textInput(['maxlength' => true,'class' => 'form-control'])->label($labels['shift_name'] . '<span class = "label-required">*</span>') ?>

    <div id = 'clinic-shift-time'>
        <?php if (!empty($model->shift_time)):?>
        <?php foreach (explode('/', $model->shift_time) as $key => $v): ?>
                <?php $shift_time_info = explode('~', $v); ?>
        <?php $model->shift_timef = $shift_time_info[0]; ?>
                        <?php $model->shift_timet = $shift_time_info[1]; ?>
                <div class = 'row clinic-shift-time'>
                    <div class = 'col-sm-4 bootstrap-timepicker'>
                        <?= $form->field($model, 'shift_timef')->textInput(['class' => 'form-control  timepicker_start', 'name' => 'Schedule[shift_timef][]'])->label('班次时间 <span class = "label-required">*</span>') ?>
                    </div>
                    <div class = 'me-col-xs-1'>
                        <?= Html::label('至', '', ['class' => 'zhi']) ?>
                    </div>
                    <div class = 'col-sm-4 bootstrap-timepicker'>
        <?= $form->field($model, 'shift_timet')->textInput(['class' => 'form-control timepicker_end', 'name' => 'Schedule[shift_timet][]'])->label('　') ?>
                    </div>
                    <div class = 'col-sm-3'>
                        <div class = 'shift-btn form-group'>

                            <a href = 'javascript:void(0);' class = 'btn-from-delete-add btn clinic-delete'>
                                <i class = 'fa fa-minus'></i>
                            </a>
                            <a href = 'javascript:void(0);' class = 'btn-from-delete-add btn clinic-add'>
                                <i class = 'fa fa-plus'></i>
                            </a>
                        </div>
                    </div>
                </div>
    <?php endforeach; ?>
                <?php else: ?>
            <div class = 'row clinic-shift-time'>
                <div class = 'col-sm-4 bootstrap-timepicker'>
                    <?= $form->field($model, 'shift_timef[]')->textInput(['class' => 'form-control timepicker_start'])->label('班次时间 <span class = "label-required">*</span>') ?>
                </div>
                 <div class = 'me-col-xs-1'>
                        <?= Html::label('至', '', ['class' => 'zhi']) ?>
                    </div>
                <div class = 'col-sm-4 bootstrap-timepicker'>
    <?= $form->field($model, 'shift_timet[]')->textInput(['class' => 'form-control timepicker_end'])->label('　') ?>

                </div>
                <div class = 'col-sm-3'>
                    <div class = 'shift-btn form-group'>

                        <a href = 'javascript:void(0);' class = 'btn-from-delete-add btn clinic-delete'>
                            <i class = 'fa fa-minus'></i>
                        </a>
                        <a href = 'javascript:void(0);' class = 'btn-from-delete-add btn clinic-add'>
                            <i class = 'fa fa-plus'></i>
                        </a>
                    </div>
                </div>
            </div>
<?php endif; ?>

    </div>






<?= $form->field($model, 'status')->dropDownList(Schedule::$getStatus,['prompt' => '请选择'])->label($labels['status'].'<span class = "label-required">*</span>') ?>


    <div class="form-group">
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
