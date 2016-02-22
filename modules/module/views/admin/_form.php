<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;

/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Permission */
/* @var $form yii\widgets\ActiveForm */
$layoutUrl = '/modules/apply/views/layouts/layout.php';
if(Yii::$app->session->get('spot')){
    $layoutUrl = '/views/layouts/layout.php';
}
?>
<?php AutoLayout::begin(['viewFile' => '@app'.$layoutUrl])?>
<?php $this->beginBlock('renderCss')?>
    <?php $this->registerCssFile('@web/public/css/rbac.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="create-form col-xs-12">
	<div class = "box">
        <div class = "box-body">
        <div class = "col-md-6">
		<?php
		$form = ActiveForm::begin ();
		?> 
	    <?= $form->field($model, 'module_description')->textInput(['maxlength' => true, 'placeholder' => '请输入模块的名称'])?>
	    <?= $form->field($model, 'module_name')->label('模块英文简称')->textInput(['maxlength' => '20', 'placeholder' => '请输入模块的英文简称'])?>
	    <?= $form->field($model, 'menus')->textarea(['rows' => 6, 'placeholder' => '菜单的格式为(菜单url,菜单名称,是否显示在侧边栏，是否超级管理员功能): url1,菜单名称,0,0;url2,菜单名称,0,0;'])?>
	
	    <div class="form-group">
	        <?= Html::submitButton('添加', ['class' => 'btn btn-success'])?>
	       
	    </div>
	    <?php ActiveForm::end(); ?>
	    </div>
	    </div>
	</div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
