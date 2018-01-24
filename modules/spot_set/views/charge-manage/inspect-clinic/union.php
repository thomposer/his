<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
$labels = $model->attributeLabels();
//$inspectList=  array_values($inspectList);

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\InspectClinic */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$css = <<<CSS
.field-inspectclinic-deliver .help-block{
   clear:both;
}
#inspectclinic-item label{
    width:100%;
}
CSS;
$this->registerCss($css);
?>

<div class="inspect-clinic-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'item')->checkboxList(ArrayHelper::map($itemList, 'id', 'item_name'),['itemOptions' => ['labelOptions' => ['class' => 'recipe-list-form-label']]])->label($labels['item'].'<span class = "label-required">*</span>'); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>