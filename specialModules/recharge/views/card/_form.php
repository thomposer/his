<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\specialModules\recharge\models\UserCard;

$labels = $model->attributeLabels();
?>

<div class="user-card-form col-md-12">

    <div class='card-center'>
        <div class="row">
            <div class="col-md-6 form-title title-item">
                <span class="item-num"></span>
                <span class="item-text"> 基本信息</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label" for="">卡号</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_card_id'] ?>" maxlength="50">
                    <div class="help-block"></div>
                </div>       
            </div>
            <div class="col-md-3">
                <div class="form-group ">
                    <label class="control-label" for="">验证码</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_identifying_code'] ?>" maxlength="11">

                    <div class="help-block"></div>
                </div>        
            </div>
            <div class="col-md-3">
                <div class="form-group ">
                    <label class="control-label" for="">卡类型码</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_card_type_code'] ?>" maxlength="50">
                    <div class="help-block"></div>
                </div>        
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group ">
                    <label class="control-label" for="">状态</label>
                    <input type="text" id="" class="form-control" name="" value="<?= UserCard::$getStatus[$record['f_status']] ?>" maxlength="11">

                    <div class="help-block"></div>
                </div>        
            </div>
            <div class="col-md-3">
                <div class="form-group ">
                    <label class="control-label" for="">生成时间</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_create_time'] ? date('Y-m-d H:i:s', $record['f_create_time']) : '' ?>" maxlength="50">
                    <div class="help-block"></div>
                </div>        
            </div>

            <div class="col-md-3">
                <div class="form-group ">
                    <label class="control-label" for="">注册服务</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $record['f_identifying_code'] ?>" maxlength="11">
                    <div class="help-block"></div>
                </div>        
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="form-group ">
                    <label class="control-label" for="">描述</label>
                    <textarea id="historypresent" disabled="disabled" class="form-control" name="" rows="4"><?= $record['f_card_desc'] ?></textarea>
                    <div class="help-block"></div>
                </div>        
            </div>
        </div>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <?php if ($service): ?>
        <div class="row">
            <div class = 'col-md-6 form-title title-item'>
                <span class="item-num"></span>
                <span class="item-text"> 服务信息</span>
            </div>
        </div>
        <?php
        if (isset($model->errors['service_left']) && !empty($model->errors['service_left'])) {
            $errors = $model->errors['service_left'][0];
        }
        ?>
        <?php foreach ($service as $key => $value): ?>
            <?php
            $model->service_left = ($left && isset($left[$value['id']])) ? $left[$value['id']]['service_left'] : $value['service_total'];
            $model->service_total = $value['service_total'];
            $model->service_id = $value['id'];
            if (isset($errors)) {
                $error = $errors[1];
                $k = $errors[0];
                if ($k == $key) {
                    $model->clearErrors('service_left');
                    $model->addError('service_left', '剩余次数不能大于总次数,且不能为负');
                } else {
                    $model->clearErrors('service_left');
                }
            }
            ?>
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group ">
                        <label class="control-label" for="">服务类型</label>
                        <input type="text" id="" class="form-control input-disable" name="" value=" <?= $value['service_name'] ?>" maxlength="50">
                    </div>        
                </div>
                <div class="col-md-3">
                    <?php echo $form->field($model, 'service_total')->textInput(['maxlength' => true, 'class' => 'form-control input-disable'])->label('总次数') ?>
                </div>
                <div class="col-md-3">

                    <?php echo $form->field($model, 'service_left')->textInput(['maxlength' => true, 'name' => 'UserCard[service_left][]'])->label('剩余次数') ?>
                    <?php echo $form->field($model, 'service_id')->hiddenInput(['maxlength' => true, 'name' => 'UserCard[service_id][]'])->label(false) ?>
                    <?php echo $form->field($model, 'service_total')->hiddenInput(['maxlength' => true, 'name' => 'UserCard[service_total][]'])->label(false) ?>
                    <!--<div class="form-group">-->
                    <!--<label class="control-label" for="">剩余次数</label>-->
                    <!--<input type="text" id="" class="form-control" name="service_left" value="<?php // $left ? $left['service_left'] : ''                        ?>" maxlength="11">-->

                    <!--<div class="help-block"></div>-->
                    <!--</div>-->        
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class='card-center'>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group ">
                    <label class="control-label" for="">激活时间</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $left[$service[0]['id']]['activate_time'] ? date('Y-m-d H:i:s', $left[$service[0]['id']]['activate_time']) : '' ?>" >

                    <div class="help-block"></div>
                </div>        
            </div>
            <div class="col-md-3">
                <div class="form-group  ">
                    <label class="control-label" for="">停用时间</label>
                    <input type="text" id="" class="form-control" name="" value="<?= $left[$service[0]['id']]['invalid_time'] ? date('Y-m-d H:i:s', $left[$service[0]['id']]['invalid_time']) : '' ?>" >
                    <div class="help-block"></div>
                </div>       
            </div>
        </div>
    </div>

    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'card_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'card_physical_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'card_type_code')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'card_type')->hiddenInput()->label(false) ?>

    <?php // echo $form->field($model, 'user_name')->hiddenInput()->label(false)  ?>
    <?php // echo  $form->field($model, 'phone')->hiddenInput()->label(false)  ?>
    <?= $form->field($model, 'parent_spot_id')->hiddenInput()->label(false) ?>
    <div class = 'row'>
        <div class = 'col-md-6 form-title title-item'>
            <span class="item-num"></span>
            <span class="item-text"> 持卡人信息</span>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'user_name')->textInput(['maxlength' => true])->label('姓名 <span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true])->label($labels['phone'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-4'>
            <?= $form->field($model, 'f_effective_time')->hiddenInput()->label(false) ?>
        </div>
    </div>
    <div class="form-group">
        <?php
        $title = $model->id ? '保存' : '激活';
        ?>
        <?= Html::a('返回', ['card-index'], ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?php if ((!isset($left['activate_time']) || empty($left['activate_time'])) || (isset($record['f_status']) && $record['f_status'] != 2)): ?>
            <?= Html::submitButton($title, ['class' => 'btn btn-default btn-form']) ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
