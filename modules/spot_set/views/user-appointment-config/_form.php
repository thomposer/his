<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\modules\user\models\UserSpot;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\UserAppointmentConfig */
/* @var $form yii\widgets\ActiveForm */
$userAppointmentConfigAttributes = $model->getModel('userAppointmentConfig')->attributeLabels();
$userSpotAttributes = $model->getModel('userSpot')->attributeLabels();
$selectedTypeList = array_column($userTypeList, 'price','spot_type_id');
?>

<div class="user-appointment-config-form col-md-10">

    <?php $form = ActiveForm::begin([
      'options' => ['class' => 'form-horizontal','id' => 'appointment-config'],
      'fieldConfig' => [
          'template' => "<div class='labelWidth text-left'>{label}</div><div class='col-xs-9 col-sm-9 childInput'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'><div class = 'col-xs-1 widthLeft'></div><div class = 'col-xs-5 widthRightError'>{error}</div></div>",
      ]]); ?>
	<div class="row">
		<div class = 'col-md-12'>
			<div class = "form-group">
				<div class="labelWidth text-right"><label class="control-label">医生：</label></div>
				<div class = "col-xs-9 col-sm-9"><?= Html::encode($userInfo['username']) ?></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class = 'col-md-12'>
			<div class = "form-group">
				<div class="labelWidth text-right"><label class="control-label">科室：</label></div>
				<div class = "col-xs-9 col-sm-9"><?= Html::encode($userInfo['name']) ?></div>
			</div>
		</div>
	</div>
	<div class="row">
            <div class = 'col-lg-12'>
                <div class="form-group field-userappointmentconfig-spot_type_id required">
                    <div class="row">
                        <div class="labelWidth text-left col-lg-12">
                            <label class="control-label">可提供服务及服务诊金<span class="label-required">*</span>：<span class="notice-message">(医生取消关联服务类型后，原来可预约此服务类型的时间段将不能再预约该服务类型。)</span></label>
                        </div>
                    </div>
                    <div class="col-lg-12" style="margin-left: 40px;margin-top: 10px;">
                        <?php foreach ($spotTypeList as $value):?>
                            <div class="col-lg-4 type-price-config type-price" style="padding-left: 0px;">
                                <label>
                                    <input class="select-type" type="checkbox" name="UserAppointmentConfig[spot_type_id][]" value="<?= $value['id'] ?>" <?= $selectedTypeList ? (key_exists($value['id'],$selectedTypeList) ? 'checked' : '') : '' ?>>
                                    <?= Html::encode($value['name']) ?>
                                    <input class="doctor-service-type-price form-control" type="text" name="UserAppointmentConfig[price][<?= $value['id'] ?>]" placeholder="请填写诊金,0-100000" <?= $selectedTypeList ? (key_exists($value['id'],$selectedTypeList) ? '' : 'disabled') : 'disabled' ?> value="<?= $selectedTypeList[$value['id']] ?>">
                                </label>
                                <div class="help-block" style="padding-left: <?= (18 + strlen($value['name']) * 4.65) ?>px">&nbsp;<span class="error-tips"></span></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-lg-12" style="margin-left: 45px;">
                        <div class="help-block type-help-block"><span class="error-tips">dsadasdasdas</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class = 'col-lg-12'>
                <div class="form-group field-userappointmentconfig-spot_type_id required">
                    <div class="row">
                        <div class="labelWidth text-left col-lg-12">
                            <label class="control-label">系统默认服务及服务诊金<span class="label-required">*</span>：<span class="notice-message">（方便门诊不开放对外预约）</span></label>
                        </div>
                    </div>
                    <div class="col-lg-12 type-price-config simple-outpatient">
                            <label>
                                方便门诊
                                <input class="doctor-service-type-price form-control" type="text" name="UserPriceConfig[price]" placeholder="请填写诊金,0-100000" value="<?= $model->getModel('userPriceConfig')->price; ?>">
                            </label>
                            <div class="help-block" style="padding-left:60px;"><span class="error-tips"></span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        <div class = 'col-md-12'>
            <?= $form->field($model->getModel('userSpot'), 'status')->radioList(UserSpot::$getStatus)->label($userSpotAttributes['status'].'<span class = "label-required">*</span>'.'：') ?>
        </div>
        </div>

        <div class="form-group">
            <?= Html::a('取消',yii\helpers\Url::to(['@spot_setUserAppointmentConfigIndex']),['class' => 'btn btn-cancel btn-form second-cancel']) ?>
            <?= Html::button('保存', ['class' => 'btn btn-default btn-form confirm-config']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
