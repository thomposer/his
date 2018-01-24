<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\modules\patient\models\Patient;

$attribute = $model->attributeLabels();
$id = Yii::$app->request->get('patientId');

$deleteOptions = [

    'data-confirm'=>false,
    'data-method'=>false,
    'data-request-method'=>'post',
    'role'=>'modal-remote',
    'data-toggle'=>'tooltip',
    'data-confirm-title'=>'系统提示',
    'data-delete' => false,
    'data-confirm-message'=>Yii::t('yii', 'Are you sure you want to delete this item?'),
];
?>

<?php Pjax::begin(['id' => 'basic-family-pjax']) ?>

<div class='row basic-form patient-form-top family-form'>
    <div class=" basic-header">
        <span class = 'basic-left-info'>
            家庭成员
        </span>

    </div>
    <?php foreach ($familyData as $key => $family): ?>
        <div class="basic-form-content">

            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group field-patient-heightcm">
                        <label class="control-label" for="patient-heightcm">成员关系</label>
                        <input type="text" id="patient-heightcm" class="form-control" name="Patient[heightcm]" value="<?= Patient::$getFamilyRelation[$family['relation']]; ?>" disabled="disabled">
                    </div>
                </div>
                <span class = 'basic-right-up basic-family-right-up'>
                    <?= Html::a('<i class="fa-family-trash fa"></i>删除',['view','id' => $id,'patient_family_id' => $family['id'], 'type' => 2],$deleteOptions); ?>
                </span>
                <span class = 'basic-right-up basic-family-right-up'>
                    <?= Html::a('<i class="fa his-pencil"></i>修改',['view','id' => $id,'patient_family_id' => $family['id']],['role' => 'modal-remote','data-toggle' => 'tooltip','data-modal-size' => 'large']) ?>
                </span>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group field-patient-heightcm">
                        <label class="control-label" for="patient-heightcm">姓名</label>
                        <input type="text" id="patient-heightcm" class="form-control" name="Patient[heightcm]" value="<?= $family['name']; ?>" disabled="disabled">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group field-patient-heightcm">
                        <label class="control-label" for="patient-heightcm">出生日期</label>
                        <input type="text" id="patient-heightcm" class="form-control" name="Patient[heightcm]" value="<?php if($family['birthday']){echo date('Y-m-d', $family['birthday']);} ?>"" disabled="disabled">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group field-patient-heightcm">
                        <label class="control-label" for="patient-heightcm">性别</label>
                        <div id="patientfamily-sex"><label><input type="radio" name="sex<?= $key ?>[]" value="1" <?= ($family['sex'] == 1) ? 'checked' : '' ?>> 男</label>
                            <label><input type="radio" name="sex<?= $key ?>[]" value="2" <?= ($family['sex'] == 2) ? 'checked' : '' ?>> 女</label>
                            <label><input type="radio" name="sex<?= $key ?>[]" value="3" <?= ($family['sex'] == 3) ? 'checked' : '' ?>> 不详</label>
                            <label><input type="radio" name="sex<?= $key ?>[]" value="4" <?= ($family['sex'] == 4) ? 'checked' : '' ?>> 其他</label>
                        </div>
                    </div>
                </div>
            </div>

          <div class="row">
            <div class="col-sm-4">
                <div class="form-group field-patient-heightcm">
                    <label class="control-label" for="patient-heightcm">手机号</label>
                    <input type="text" id="patient-heightcm" class="form-control" name="Patient[heightcm]" value="<?= $family['iphone']; ?>" disabled="disabled">
                </div>
            </div>
              <div class="col-sm-4">
                <div class="form-group field-patient-heightcm">
                    <label class="control-label" for="patient-heightcm">身份证</label>
                    <input type="text" id="patient-heightcm" class="form-control" name="Patient[heightcm]" value="<?= $family['card']; ?>" disabled="disabled">
                </div>
            </div>
        </div>
        </div>
        <div class="basic-family-more"></div>
<?php endforeach; ?>

<div class="family-add">
    <?php
    $options = [
        'class' => 'btn btn-default btn-form margin-top-30',
        'role' => 'modal-remote',
        'data-toggle' => 'tooltip',
        'data-modal-size' => 'large'
    ];
    echo Html::a('+  新增', Url::to(['', 'id' => $id, 'patient_family_id' => 0]), $options);
    ?>
</div>
</div>
<?php $this->registerJs("
    $('.family-form [type=radio]').attr({'disabled': true});
") ?>
<?php Pjax::end() ?>

