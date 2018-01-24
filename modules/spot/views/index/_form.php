<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot\models\Spot;
use app\modules\user\models\User;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Spot */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="spot-form">

    <?php $form = ActiveForm::begin([
		    'method' => 'post',
		    'options' =>  ['enctype' => 'multipart/form-data'],
		]); ?>
     <div class = 'col-sm-2 col-md-2'>
      <?= $form->field($model, 'icon_url')->hiddenInput(['id' =>'avatar_url'])->label(false); ?>
      <div id="crop-avatar">
            <!-- Current avatar -->
            <div class="avatar-view" title="上传图标">
               <?php if($model->icon_url):?>
               <?= Html::img(Yii::$app->params['cdnHost'].$model->icon_url,['alt' => '诊所图标','onerror'=>"this.src='{$baseUrl}/public/img/user/img_user_big.png'"]) ?>
               <?php else:?>
                <?= Html::img(Yii::$app->request->baseUrl.'/public/img/user/img_user_big.png',['alt' => '诊所图标','style' => 'width:100%;'])?>
               <?php endif;?> 
               <div class = 'btn btn-default font-body2 header_img'>上传图标</div>             
            </div> 
                       
        </div>
      
    </div>
    <div class = 'col-sm-10 col-md-10'>
    <div class = 'row'>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'spot_name')->textInput(['maxlength' => true])->label($attributeLabels['spot_name'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'fax_number')->textInput(['maxlength' => true]) ?>
    </div>
    </div>
    <div class = 'row'>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'contact_iphone')->textInput(['maxlength' => true])->label($attributeLabels['contact_iphone'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'contact_name')->textInput(['maxlength' => true])->label($attributeLabels['contact_name'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>
    </div>
    </div>

    <div class = 'row'>
    <div class = 'col-sm-4'>
    <div class="city-control"><?= $form->field($model, 'address')->textInput(['maxlength' => true,'data-toggle' => 'city-picker']) ?></div>
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'detail_address')->textInput(['maxlength' => true]) ?>
    </div>
    <?php if($_COOKIE['createSpot'] == 1):?>
     <div class = 'col-sm-4'>
    <?= $form->field($model, 'status')->dropDownList(Spot::$getStatus,['disabled'=>true]) ?>
     </div>
    <?php else:?>
     <div class = 'col-sm-4'>
    <?= $form->field($model, 'status')->dropDownList(Spot::$getStatus) ?>
     </div>
    <?php endif;?>
    </div>
    <?php if($_COOKIE['createSpot'] == 1):?>
    <div class = 'row'>
    <div class = 'col-sm-12'>
    <?= $form->field($model, 'addSelected')->checkboxList(['1' => '我将加入该诊所'])->label(false) ?>
    </div>
    </div>
    <?php endif;?>
    <?php if($_COOKIE['createSpot'] == 1):?>
        <div class="form-group">
            <?= Html::submitButton('创建诊所', ['class' => 'btn btn-default btn-form btn-new-color btn-disabled-color','disabled' => 'true']) ?>
        </div>
    <?php else:?>
    <div class="form-group">
        <?= Html::a('取消',['index'],['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>
    <?php endif;?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
