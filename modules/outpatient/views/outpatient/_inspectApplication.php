<?php

use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;

$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/check/common.css');
?>

<div class="inspect-application-print">
    <div class = 'application-bg'>
        <h5 class = 'title'>实验室检查项目</h5>
    </div>
    <?php
    $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal common']
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'onInspect')->checkboxList(ArrayHelper::map($inspectList, 'id', 'name'),
                [
                    'value'=> $inspectId,
                    'item'=>function($index, $label, $name, $checked, $value){
                        $checkCancelStr = $checked?"<span style='color:#FF5000'>（已取消）</span>":"";
                        return '<label><input type="checkbox" checked="checked" name="'.$name.'" value="'.$value.'" '.'>'.  Html::encode($label) .$checkCancelStr.'</label>';
                    }
                ]


                )->label(false); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
