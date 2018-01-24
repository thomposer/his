<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

use app\modules\spot\models\Spot;
/* @var $this yii\web\View */
/* @var $model app\modules\user\models\search\UserSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="user-search hidden-xs">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
<span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入'.$attributeLabels['username'] ]) ?>

    <?php  echo $form->field($model, 'email')->textInput(['placeholder' => '请输入'.$attributeLabels['email'] ]) ?>

    <?php  echo $form->field($model, 'iphone')->textInput(['placeholder' => '请输入'.$attributeLabels['iphone'] ]) ?>
    
    <?php  echo $form->field($model, 'clinic_id')->dropDownList(ArrayHelper::map(Spot::getSpotList(['status' => 1]),'id','spot_name'),['prompt' => '请选择所属诊所','class'=>'form-control']) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default delete-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
