<?php

use app\modules\spot_set\models\Material;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\search\MaterialSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="material-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['@spot_setChargeManageMaterialIndex']),
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'type')->dropDownList(Material::$typeOption,['prompt' => '请选择']) ?>

    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>

    <?php // echo $form->field($model, 'product_name')->textInput(['placeholder' => '请输入'.$attributeLabels['product_name'] ]) ?>

    <?php // echo $form->field($model, 'en_name')->textInput(['placeholder' => '请输入'.$attributeLabels['en_name'] ]) ?>

    <?php // echo $form->field($model, 'attribute')->textInput(['placeholder' => '请输入'.$attributeLabels['attribute'] ]) ?>

    <?php // echo $form->field($model, 'specification')->textInput(['placeholder' => '请输入'.$attributeLabels['specification'] ]) ?>

    <?php // echo $form->field($model, 'unit')->textInput(['placeholder' => '请输入'.$attributeLabels['unit'] ]) ?>

    <?php // echo $form->field($model, 'price')->textInput(['placeholder' => '请输入'.$attributeLabels['price'] ]) ?>

    <?php // echo $form->field($model, 'default_price')->textInput(['placeholder' => '请输入'.$attributeLabels['default_price'] ]) ?>

    <?php // echo $form->field($model, 'meta')->textInput(['placeholder' => '请输入'.$attributeLabels['meta'] ]) ?>

    <?php // echo $form->field($model, 'manufactor')->textInput(['placeholder' => '请输入'.$attributeLabels['manufactor'] ]) ?>

    <?php // echo $form->field($model, 'warning_num')->textInput(['placeholder' => '请输入'.$attributeLabels['warning_num'] ]) ?>

    <?php // echo $form->field($model, 'warning_day')->textInput(['placeholder' => '请输入'.$attributeLabels['warning_day'] ]) ?>

    <?php // echo $form->field($model, 'remark')->textInput(['placeholder' => '请输入'.$attributeLabels['remark'] ]) ?>

    <?php // echo $form->field($model, 'status')->textInput(['placeholder' => '请输入'.$attributeLabels['status'] ]) ?>

    <?php // echo $form->field($model, 'tag_id')->textInput(['placeholder' => '请输入'.$attributeLabels['tag_id'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>