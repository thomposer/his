<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\card\models\UserCard;

$labels = $model->attributeLabels();
?>

<div class="user-card-form col-md-8">

    <div class='card-center'>
        <div class="row">
            <div class="col-md-6 form-title">
                基本信息
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="">卡号</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_card_id'] ?>" maxlength="50">
                    <div class="help-block"></div>
                </div>       
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label class="control-label" for="">验证码</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_identifying_code'] ?>" maxlength="11">

                    <div class="help-block"></div>
                </div>        
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group ">
                    <label class="control-label" for="">卡类型码</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_card_type_code'] ?>" maxlength="50">
                    <div class="help-block"></div>
                </div>        
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label class="control-label" for="">状态</label>
                    <input type="text" id="" class="form-control" name="" value="<?= UserCard::$getStatus[$record['f_status']] ?>" maxlength="11">

                    <div class="help-block"></div>
                </div>        
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group ">
                    <label class="control-label" for="">描述</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_card_desc'] ?>" maxlength="50">
                    <div class="help-block"></div>
                </div>        
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label class="control-label" for="">注册服务</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_identifying_code'] ?>" maxlength="11">

                    <div class="help-block"></div>
                </div>        
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group ">
                    <label class="control-label" for="">生成时间</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_create_time'] ? date('Y-m-d H:i:s', $record['f_create_time']) : '' ?>" maxlength="50">
                    <div class="help-block"></div>
                </div>        
            </div>
        </div>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <?php if ($service): ?>
        <div class = 'row'>
            <div class = 'col-md-6 form-title'>
                服务信息:<?= $service['service_name'] ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group ">
                    <label class="control-label" for="">总次数</label>
                    <input type="text" id="" class="form-control input-disable" name="" value="<?= $service['service_total'] ?>" maxlength="50">
                    <input type="hidden"  name="service_id" value="<?= $service ? $service['id'] : '' ?>" />
                    <div class="help-block"></div>
                </div>        
            </div>
            <div class="col-md-6">
                <?php
                $model->service_left = $left ? $left['service_left'] : $service['service_total'];
                $model->service_total = $service['service_total'];
                ?>
                <?php echo $form->field($model, 'service_left')->textInput(['maxlength' => true])->label('剩余次数') ?>
                <!--<div class="form-group">-->
                <!--<label class="control-label" for="">剩余次数</label>-->
                <!--<input type="text" id="" class="form-control" name="service_left" value="<?php // $left ? $left['service_left'] : ''     ?>" maxlength="11">-->

                <!--<div class="help-block"></div>-->
                <!--</div>-->        
            </div>
        </div>
    <?php endif; ?>
    <div class='card-center'>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group ">
                    <label class="control-label" for="">激活时间</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $left['activate_time'] ? date('Y-m-d H:i:s', $left['activate_time']) : '' ?>" >

                    <div class="help-block"></div>
                </div>        
            </div>
            <div class="col-md-6">
                <div class="form-group  ">
                    <label class="control-label" for="">停用时间</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $left['invalid_time'] ? date('Y-m-d H:i:s', $left['invalid_time']) : '' ?>" >
                    <div class="help-block"></div>
                </div>       
            </div>
        </div>
    </div>

    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'card_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'card_physical_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'card_type_code')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'service_total')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'card_type')->hiddenInput()->label(false) ?>

    <?php // echo $form->field($model, 'user_name')->hiddenInput()->label(false) ?>
    <?php // echo  $form->field($model, 'phone')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'parent_spot_id')->hiddenInput()->label(false) ?>
    <div class = 'row'>
        <div class = 'col-md-6 form-title'>
            持卡人信息
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'user_name')->textInput(['maxlength' => true])->label('姓名 <span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true])->label($labels['phone'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'f_effective_time')->hiddenInput()->label(false) ?>
        </div>
    </div>
    <div class="form-group">
        <?php
        $title = $model->id ? '保存' : '激活';
        ?>
        <?= Html::a('返回', ['index'], ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?php if ((!isset($left['activate_time']) || empty($left['activate_time'])) || (isset($record['f_status']) && $record['f_status'] != 2)): ?>
            <?= Html::submitButton($title, ['class' => 'btn btn-default btn-form']) ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
