<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\overview\models\Search\OverviewSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="overview-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'id')->textInput(['placeholder' => '请输入'.$attributeLabels['id'] ]) ?>

    <?= $form->field($model, 'user_id')->textInput(['placeholder' => '请输入'.$attributeLabels['user_id'] ]) ?>

    <?= $form->field($model, 'spot')->textInput(['placeholder' => '请输入'.$attributeLabels['spot'] ]) ?>

    <?php // echo $form->field($model, 'spot_name')->textInput(['placeholder' => '请输入'.$attributeLabels['spot_name'] ]) ?>

    <?php // echo $form->field($model, 'parent_spot')->textInput(['placeholder' => '请输入'.$attributeLabels['parent_spot'] ]) ?>

    <?php // echo $form->field($model, 'status')->textInput(['placeholder' => '请输入'.$attributeLabels['status'] ]) ?>

    <?php // echo $form->field($model, 'template')->textInput(['placeholder' => '请输入'.$attributeLabels['template'] ]) ?>

    <?php // echo $form->field($model, 'contact_iphone')->textInput(['placeholder' => '请输入'.$attributeLabels['contact_iphone'] ]) ?>

    <?php // echo $form->field($model, 'contact_name')->textInput(['placeholder' => '请输入'.$attributeLabels['contact_name'] ]) ?>

    <?php // echo $form->field($model, 'contact_email')->textInput(['placeholder' => '请输入'.$attributeLabels['contact_email'] ]) ?>

    <?php // echo $form->field($model, 'province')->textInput(['placeholder' => '请输入'.$attributeLabels['province'] ]) ?>

    <?php // echo $form->field($model, 'city')->textInput(['placeholder' => '请输入'.$attributeLabels['city'] ]) ?>

    <?php // echo $form->field($model, 'area')->textInput(['placeholder' => '请输入'.$attributeLabels['area'] ]) ?>

    <?php // echo $form->field($model, 'detail_address')->textInput(['placeholder' => '请输入'.$attributeLabels['detail_address'] ]) ?>

    <?php // echo $form->field($model, 'fax_number')->textInput(['placeholder' => '请输入'.$attributeLabels['fax_number'] ]) ?>

    <?php // echo $form->field($model, 'telephone')->textInput(['placeholder' => '请输入'.$attributeLabels['telephone'] ]) ?>

    <?php // echo $form->field($model, 'icon_url')->textInput(['placeholder' => '请输入'.$attributeLabels['icon_url'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default delete-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>