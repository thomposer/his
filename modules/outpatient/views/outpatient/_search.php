<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\outpatient\models\Outpatient;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\search\OutpatientSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
$params = Yii::$app->request->queryParams;
$type = isset($params['type']) ? $params['type'] : 3;
?>

<div class="outpatient-search hidden-xs">

    <?php
        $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'form-horizontal search-form','data-pjax' => true],
                'fieldConfig' => [
                    'template' => "{input}",
                ]
    ]);
    ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入姓名']) ?>
	<?php if($type == 3): ?>
    <?= $form->field($model, 'status')->dropDownList(Outpatient::$getSelectStatus, ['prompt'=>'请选择','style' => 'width:120px;']) ?>
    <?php endif; ?>
    <?= Html::hiddenInput('type',$type) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
     
    </div>

    <?php ActiveForm::end(); ?>

</div>
