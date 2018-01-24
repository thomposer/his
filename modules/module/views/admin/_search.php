<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\module\models\search\MenuSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();

?>
<div>
    <?php $form = ActiveForm::begin([
        'options' =>  ['class' => 'form-horizontal search-form'],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>

    <?= $form->field($model, 'module_description')->textInput(['placeholder' => '请输入'.$attributeLabels['module_description'] ]) ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
        <?php //echo Html::a('重置', ['list'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
