<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\patient\models\PatientRecord */
/* @var $form ActiveForm */
?>
<div class="select-package-form">

    <?php $form = ActiveForm::begin(['options' =>['style' => 'margin-top:5px;']]); ?>
        <input type="hidden" name="PackageRecord[template_id]" class="template-value" value="<?= isset($packageList[$model->template_id]) ? $model->template_id : '' ?>"> <!--默认传空-->
        <?php foreach ($packageList as $value): ?>
            <?php
                $mouseover = ($packageDetail[$value['id']]['inspectList'] ? ('检验医嘱：<br/><p>' . Html::encode(implode('﹑', $packageDetail[$value['id']]['inspectList'])) . '</p>') : '').
                        ($packageDetail[$value['id']]['checkList'] ? ('检查医嘱：<br/><p>' . Html::encode(implode('﹑', $packageDetail[$value['id']]['checkList'])) . '</p>') : '').
                        ($packageDetail[$value['id']]['cureList'] ? ('治疗医嘱：<br/><p>' . Html::encode(implode('﹑', $packageDetail[$value['id']]['cureList'])) . '</p>') : '').
                        ($packageDetail[$value['id']]['recipeList'] ? ('处方医嘱：<br/><p>' . Html::encode(implode('﹑', $packageDetail[$value['id']]['recipeList'])) . '</p>') : '');
            ?>
                <label class="package-list">
                    <?php if($value['id'] == $model->template_id): ?>
                    <input type="radio" name="PackageRecord[template_id]" <?= $disabled ? "disabled" : "" ?> checked value="<?= Html::encode($value['id']) ?>"> <?= Html::encode($value['name']) ?> 
                    <?php else: ?>
                        <input type="radio" name="PackageRecord[template_id]" <?= $disabled ? "disabled" : "" ?> value="<?= Html::encode($value['id']) ?>"> <?= Html::encode($value['name']) ?> 
                    <?php endif; ?>
                    
                    <span class="fa fa-question-circle blue" data-toggle="tooltip" data-html="true" data-placement="right" data-original-title="<?= $mouseover ?>"></span>
                </label>
        <?php endforeach; ?>
    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerCss("
                .select-package-form label{
                    width: 100%;
                }
")
?>
<?php
$this->registerJs("
                $('.package-list input[type=radio]').unbind('click').click(function(){
                        if($('.template-value').val() == $(this).val()){
                            $(this).prop('checked',false);
                            $('.template-value').val('');
                        }else{
                            $('.template-value').val($(this).val());
                        }
                });
")
?>
