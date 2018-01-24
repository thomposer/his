<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\modules\spot\models\CardRechargeCategory;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CardRechargeCategory */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

<?php $form = ActiveForm::begin(); ?>
<div class = 'row'>
    <div class = 'col-md-6'>
        <?= $form->field($model, 'f_category_name')->textInput(['maxlength' => true])->label('卡组名称<span class = "label-required">*</span>') ?> 
    </div>
    <div class = 'col-md-6'>
        <?php
//        echo $form->field($model, 'f_level')->widget(Select2::classname(), [
//            'data' => CardRechargeCategory::getLevel(),
//            'language' => 'de',
//            'options' => ['placeholder' => '请选择'],
//            'pluginOptions' => [
//                'allowClear' => true
//            ],
//        ])
        ?>
        <?php echo $form->field($model, 'f_level')->dropDownList(CardRechargeCategory::getLevel(), ['prompt' => '请选择'])->label($attribute['f_level'] . '<span class="f_level">(卡片自动升级，只能从低等级卡组升至高等级)</span>') ?> 
    </div>
</div>

<?= Html::hiddenInput('CardRechargeCategory[f_state]', 2) ?> 

<?= $form->field($model, 'f_category_desc')->textarea(['rows' => 4, 'placeholder' => '请输入' . $attribute['f_category_desc'] . '(不多于200个字)', 'maxlength' => 200]) ?>

<?php ActiveForm::end(); ?>

<?php Pjax::end() ?>