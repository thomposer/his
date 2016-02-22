<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\apply\models\ApplyPermissionList;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\Menu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-form col-md-6">

    <?php $form = ActiveForm::begin([
             'action' => '',
             'method' => 'post'
     ]); ?>
    <?php if($model->isNewRecord):?>
        <?= $form->field($model, 'user_id')->dropDownList($userData?ArrayHelper::map($userData, 'user_id', 'username'):array(),['class' => 'form-control select2','style' => 'width:100%']) ?>
    <?php else:?>
      <?= $form->field($model, 'user_id')->textInput(['maxlength' => 255,'readonly'=>'true']) ?>
    <?php endif;?>
    <?= $form->field($model,'reason')->textarea(['rows'=>6]) ?>
    
    <?= $form->field($model,'status')->dropDownList(ApplyPermissionList::$apply_status) ?>
    
    <?= $form->field($model, 'item_data')->checkboxList($item_name?ArrayHelper::map($item_name, 'name', 'description'):array())?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => 'btn btn-success']) ?>
        <?= Html::a('返回列表',['index'],['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
