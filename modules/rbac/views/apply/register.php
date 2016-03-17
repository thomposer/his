<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php $this->endBlock()?>
<?php $this->beginBlock('content')?>
<div class="menu-create col-xs-12">
    <div class = "box">
        <div class = "box-body">
            <div class="menu-form col-md-6">
            
                <?php $form = ActiveForm::begin([
                         'action' => '',
                         'method' => 'post'
                 ]); ?>
                <?= $form->field($model,'username')->textInput() ?>
                
                <?= $form->field($model,'email')->textInput() ?>
                
                <?php // $form->field($model, 'item_data')->checkboxList($item_name?ArrayHelper::map($item_name, 'name', 'description'):array())?>
            
                
                <div class="form-group">
                    <?= Html::submitButton('添加', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('返回列表',['index'],['class' => 'btn btn-primary']) ?>
                </div>
            
                <?php ActiveForm::end(); ?>            
            </div>
        </div>
    </div>
</div>
<?php $this->endBlock()?>
<?php $this->beginBlock('renderJs')?>

<?php $this->endBlock();?>
<?php AutoLayout::end();?>
