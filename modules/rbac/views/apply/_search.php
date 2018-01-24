<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\apply\models\search\ApplyPermissionListSearch */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
?>
<div class="apply-permission-list-search">
    
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
       'options' =>  ['class' => 'form-horizontal search-form'],
        'fieldConfig' => [
            'template' => "<div class='search-labels text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div>",
        ]
    ]); ?>

    <?= $form->field($model, 'user_id'); ?>
    <?php if($systemsRole):?>
        <?= $form->field($model,'spot')->dropDownList(ArrayHelper::map($spotList, 'spot', 'spot_name'),[
            'prompt' => '请选择站点'
        ])?>
    <?php endif;?>
    <?= $form->field($model, 'status')->dropDownList($status) ?>
    <?= $form->field($model, 'item_name')->dropDownList(ArrayHelper::map($roleList, 'name', 'description','spot'),[
        'prompt' => '请选择角色'
    ]) ?>   
   <div class="form-group search_button">
    <?= Html::submitButton('查询', ['class' => 'btn btn-primary btn-submit delete-btn']) ?>
<!--    --><?//= Html::a('重置',Url::to(['@rbacApplyIndex']), ['class' => 'btn btn-default']) ?>
   </div>

    <?php ActiveForm::end(); ?>
</div>
