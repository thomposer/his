<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\search\OutpatientSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();

?>
<?php AppAsset::addCss($this, '@web/public/css/pharmacy/pharmacy.css') ?>
<div class="recipe-record-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入'.$attributeLabels['username'] ]) ?>
    
    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>
    
    <?= $form->field($model, 'product_name')->textInput(['placeholder' => '请输入'.$attributeLabels['product_name'] ]) ?>
    <?php // $form->field($model, 'iphone')->textInput(['placeholder' => '请输入'.$attributeLabels['iphone'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default delete-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
