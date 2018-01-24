<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */
$this->title = '修改密码';
$this->params['breadcrumbs'][] = ['label' => '我的账号'];
$this->params['breadcrumbs'][] = $this->title;
$attribute = $model->attributeLabels();

?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss');?>

<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="item-create col-xs-12">
    <div class = "box">
        <div class = "box-body">    
            <div class="edit-password-form col-md-8">

                <?php $form = ActiveForm::begin([
                    'method' => 'post'
                ]); ?>
                
                <?= $form->field($model, 'oldPassword')->passwordInput()->label($attribute['oldPassword'].'<span class = "label-required">*</span>') ?>    
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => '8-20位，包含数字、符号及字母至少两种'])->label($attribute['password'].'<span class = "label-required">*</span>') ?>
                <?= $form->field($model, 'reType_password')->passwordInput()->label($attribute['reType_password'].'<span class = "label-required">*</span>') ?>
                <div class="form-group">
                    <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
                    <?= Html::a('取消',Yii::$app->request->referrer,['class' => 'btn btn-cancel btn-form']) ?>
                </div>
            
                <?php ActiveForm::end(); ?>

            </div>
        
            
        </div>
    </div>
</div>

<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
<?php $this->endBlock()?>
<?php AutoLayout::end()?>