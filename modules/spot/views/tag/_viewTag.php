<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use app\modules\spot\models\Tag;
$attribute = $model->attributeLabels();

?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<div class = "box delete_gap tag-content">
    <div class = "box-body">
        <div class="tag-form-view">

            <?php $form = ActiveForm::begin(); ?>

            <div class = 'row'>
                <div class = 'col-sm-12'>
                    <?php
                    if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/update', $this->params['permList'])) {
                        echo Html::a("<i class='fa fa-pencil-square-o'></i>修改", ['update', 'id' => $model->id], ['class' => 'form-update btn-tag-from-update', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-pjax' => 0]);
                    }
                    ?>
                </div>
            </div>

            <div class = 'row'>
                <div class = 'col-sm-6'>
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'disabled' => true])->label($attribute['name'].'<span class = "label-required">*</span>') ?>
                </div>
                <div class = 'col-sm-6'>
                    <?= $form->field($model, 'type')->dropDownList(Tag::$getType, ['prompt' => '请选择', 'disabled' => true])->label($attribute['type'].'<span class = "label-required">*</span>') ?>
                    <div class="field-tag-type-tips"><span style="color:#ff4b00;">Tips：</span>标签关联项目后，分类不可再修改</div>
                </div>
            </div>

            <div class = 'row'>
                <div class = 'col-sm-12'>
                    <?= $form->field($model, 'description')->textarea(['maxlength' => true,'disabled' => true,'style' => 'height: 100px;']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

<?php
$this->registerJs('$("#tagStatus").val($("#tagStatus").val() == 1?"正常":"停用")');
?>

<?php Pjax::end() ?>
