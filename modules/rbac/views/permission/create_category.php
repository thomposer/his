<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
$baseUrl = Yii::$app->request->baseUrl;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Permission */
/* @var $form yii\widgets\ActiveForm */
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
  <!-- Select2 -->
    <?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>   
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="permission-form col-xs-12" >
    <div class = 'box'>
        <div class = 'box-body'>
         <div class = 'col-md-6'>
            <?php $form = ActiveForm::begin([
          'options' => ['class' => 'form-input'],
          ]); ?> 
       
        <?= $form->field($model, 'category')->textInput(['maxlength' => true,'placeholder'=>'必须：英文缩写']) ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>    
        <div class="form-group" >
            <?= Html::submitButton('添加', ['class' => 'btn btn-success']) ?>
            <?= Html::a('返回列表', ['@rbacPermission'], ['class' => 'btn btn-primary'])?>
        </div>
        <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
    <script type="text/javascript">
    	require(["<?= $baseUrl ?>"+"/public/js/rbac/permission.js"],function(main){
    		main.init();
		});
		
	</script>        
<?php $this->endBlock();?>
<?php AutoLayout::end();?>