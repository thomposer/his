<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\widgets\Pjax;

$attribute = $model->attributeLabels();

if ($model->allergy && !empty($model->allergy)) {
    $model->has_allergy_type = 1;
    $allergyData = Json::decode($model->allergy);
    $display = 'display: block';
} else {
    $allergyData[] = [
        'source' => '',
        'reaction' => '',
        'degree' => ''
    ];
    $display = 'display:none';
}
?>
<?php
Pjax::begin([
    'id' => 'case-pjax'
])
?>
<?php
$form = ActiveForm::begin([
            'id' => 'basic-case',
            'options' => ['data' => ['pjax' => true]],
        ]);
?> 
<div class='row basic-form patient-form-top'>
    <div class=" basic-header">
        <span class = 'basic-left-info'>
            病历信息
        </span>
        <span class = 'basic-right-up basic-right-up-case'>
            <i class="fa his-pencil"></i>修改
        </span>
    </div>
    <div class="basic-form-content basic-form-content-case">
        <?= $form->field($model, 'has_allergy_type')->radioList($model::$has_allergy)->label($attribute['allergy']) ?>
        <div class="row" id="allergy-list" style="<?= $display ?>">
            <?php foreach ($allergyData as $val): ?>
                <?php
                $model->allergySource = $val['source'];
                $model->allergyReaction = $val['reaction'];
                $model->allergyDegree = $val['degree'];
                ?>
                <div class="allergy-list">
                    <div class='col-sm-4'>
                        <?= $form->field($model, 'allergySource')->dropDownList(ArrayHelper::map($allergy_list['allergy1'], 'id', 'name'), [ 'name' => 'Patient[allergySource][]', 'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '请选择过敏源'])->label(false) ?>
                    </div>
                    <div class='col-sm-4'>
                        <?= $form->field($model, 'allergyReaction')->dropDownList(ArrayHelper::map($allergy_list['allergy2'], 'id', 'name'), [ 'name' => 'Patient[allergyReaction][]', 'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '请选择过敏反应'])->label(false) ?>
                    </div>
                    <div class='col-sm-2'>
                        <?= $form->field($model, 'allergyDegree')->dropDownList(ArrayHelper::map($allergy_list['allergy3'], 'id', 'name'), [ 'name' => 'Patient[allergyDegree][]', 'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '请选择过敏程度'])->label(false) ?>
                    </div>
                    <div class='col-sm-2 allergy'>
                        <div class='form-group'>
                            <a href='javascript:void(0);'
                               class='btn-from-delete-add btn allergy-delete'> <i class='fa fa-minus'></i>
                            </a>
                            <a href='javascript:void(0);'
                               class='btn-from-delete-add btn allergy-add'> <i class='fa fa-plus'></i>
                            </a> 
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?= $form->field($model, 'personalhistory')->textarea(['rows' => 4]) ?>
        <?= $form->field($model, 'genetichistory')->textarea(['rows' => 4]) ?>
        <div class="form-group basic-btn">
            <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form case-submit']) ?>
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form btn-cancel-case']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php $this->registerJs("
    $('#case-pjax .form-control').attr({'disabled': true});
    $('[type=radio]').attr({'disabled': true});
    $('.allergy-list').each(function (){
          $(this).find('.allergy').hide();
    });
    var length = $('#allergy-list .allergy-list').length;
            if(length == 1){
                $('.allergy-delete').hide();

            }
") ?>
<?php Pjax::end() ?>

