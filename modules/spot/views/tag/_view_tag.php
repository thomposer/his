<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

$attribute = $model->attributeLabels();

?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<div class = "box delete_gap tag-content">
    <div class = "box-body">
        <div class="tag-form">

            <?php $form = ActiveForm::begin(); ?>

            <div class = 'row'>
                <div class = 'col-sm-12'>
                    <?php
                    if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/update', $this->params['permList'])) {
                        echo Html::a("<i class='fa fa-pencil'></i>修改", ['update', 'id' => $model->id], ['class' => 'tag-form-update', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-pjax' => 0]);
                    }
                    ?>
                </div>
            </div>

            <div class = 'row'>
                <div class = 'col-sm-6'>
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'disabled' => true])->label($attribute['name'].'<span class = "label-required">*</span>') ?>
                </div>
                <div class = 'col-sm-6'>
                    <?= $form->field($model, 'status')->textInput(['maxlength' => true,'disabled' => true,'id'=>'tagStatus'])->label($attribute['status'].'<span class = "label-required">*</span>') ?>
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
$css =<<<CSS
                #tagSelect{
                        cursor: auto;
                    }   
                .tag-form-update{
                    color: #99A3B1;
                    float: right;
                    margin-top: 20px;
                    font-size: 14px;
                    width: 70px;
                    height: 24px;
                    border-radius: 30px;
                    background-color: #EDF1F7;
                    line-height: 24px;
                    text-align: center;
                    touch-action: manipulation;
                    cursor: pointer;
                }
CSS;
$this->registerCss($css)
?>

<?php Pjax::end() ?>
