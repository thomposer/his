<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\modules\user\models\User;
use yii\helpers\ArrayHelper;
use app\modules\spot_set\models\SecondDepartment;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
$public_img_path = $baseUrl . '/public/img/';

?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    <div class='col-sm-2 col-md-2'>
        <?= $form->field($model, 'head_img')->hiddenInput(['id' => 'avatar_url'])->label(false); ?>
        <div id="crop-avatar">
            <!-- Current avatar -->
            <div class="avatar-view" title="上传头像">
                <?php if ($model->head_img): ?>
                    <?= Html::img(Yii::$app->params['cdnHost'] . $model->head_img, ['alt' => '头像', 'onerror' => "this.src='{$public_img_path}default.png'"]) ?>
                <?php else: ?>
                    <?= Html::img(Yii::$app->request->baseUrl . '/public/img/user/img_user_big.png', ['alt' => '头像']) ?>
                <?php endif; ?>
                <div class='btn btn-default font-body2 header_img'>上传头像</div>
            </div>

        </div>

    </div>
    <div class='col-sm-10 col-md-10'>
        <div class='row'>
            <div class='col-sm-4'>
                <?= $form->field($model, 'username')->textInput(['maxlength' => true])->label($attribute['username'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class='col-sm-4'>
                <?= $form->field($model, 'birthday')->widget(
                    DatePicker::className(), [
                        'inline' => false,
                        'language' => 'zh-CN',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd'
                        ],
                        'options' => [
                            'autocomplete' => 'off'
                        ]
                    ]
                )->label($attribute['birthday'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class='col-sm-4'>
                <?= $form->field($model, 'sex')->radioList(User::$getSex)->label($attribute['sex'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-sm-4'>
                <?= $form->field($model, 'iphone')->textInput(['maxlength' => true])->label($attribute['iphone'] . '<span class = "label-required">*</span><span style="color:#FF4B00;">&nbsp（登录账号）</span>') ?>
            </div>
            <div class='col-sm-4'>
                <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label($attribute['email'] . '<span class = "label-required">*</span><span style="color:#FF4B00;">&nbsp（登录账号）</span>') ?>
            </div>
            <div class='col-sm-4'>
                <?= $form->field($model, 'card')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class='row form-margin'>
            <div class='col-sm-4'>
                <?= $form->field($model, 'occupation')->dropDownList(User::$getOccuption, ['prompt' => '请选择'])->label($attribute['occupation'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class='col-sm-4'>
                <?= $form->field($model, 'occupation_type')->dropDownList(User::$getOccupationType, ['prompt' => '请选择'])->label($attribute['occupation_type'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-sm-4'>
                <?= $form->field($model, 'position_title')->dropDownList(User::$getPositionTitle, ['prompt' => '请选择'])->label($attribute['position_title']) ?>
            </div>
            <div class='col-sm-4'>
                <?= $form->field($model, 'status')->dropDownList(User::$getStatus) ?>
            </div>
        </div>
        <div id='clinic-department'>
            <?php foreach ($clinicInfo as $key => $v): ?>
                <?php $model->clinic_id = $v;
                $model->department = $department[$key]; ?>
                <div class='row clinic-department'>
                    <div class='col-sm-4'>
                        <?= $form->field($model, 'clinic_id')->dropDownList(ArrayHelper::map($spotInfo, 'id', 'spot_name'), ['name' => 'User[clinic_id][]', 'class' => 'form-control user-clinic_id', 'prompt' => '请选择', 'autocomplete' => "off"])->label($attribute['clinic_id'] . '<span class = "label-required">*</span>') ?>
                    </div>
                    <div class='col-sm-4'>
                        <?= $form->field($model, 'department')->dropDownList(ArrayHelper::map(SecondDepartment::getOnceSecondDepartment($model->clinic_id), 'id', 'name', 'onceName'), ['prompt' => '请选择 ', 'name' => 'User[department][]', 'autocomplete' => "off", 'class' => 'form-control user-department_id']) ?>
                    </div>
                    <div class='col-sm-4'>
                        <div class='form-group'>
                            <a href='javascript:void(0);' class='btn-from-delete-add btn clinic-delete'>
                                <i class='fa fa-minus'></i>
                            </a>
                            <a href='javascript:void(0);' class='btn-from-delete-add btn clinic-add'>
                                <i class='fa fa-plus'></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class='row'>
            <div class='col-sm-12'>
                <?= $form->field($model, 'role')->checkboxList(ArrayHelper::map($roleInfo, 'name', 'description')); ?>

                <?= $form->field($model, 'introduce')->textarea(['rows' => 5]) ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::a('取消', ['index'], ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
            <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
        </div>


    </div>

    <?php ActiveForm::end(); ?>

</div>
