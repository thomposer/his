<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\module\models\Title;
use app\assets\AppAsset;
use yii\helpers\Url;

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
    <?php AppAsset::addCss($this, '@web/public/css/module/image_preview.css') ?>
    <?php AppAsset::addScript($this, '@web/public/js/lib/image_preview.js')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="create-form col-xs-12">
	<div class = "box">
        <div class = "box-body">
        <div class = "col-md-6">
		<?php
		$form = ActiveForm::begin ([
		    'method' => 'post',
		    'options' =>  ['enctype' => 'multipart/form-data'],
		]);
		?> 
	    <?= $form->field($model, 'module_description')->textInput(['maxlength' => true, 'placeholder' => '请输入模块的名称'])?>
	    <?= $form->field($model, 'module_name')->label('模块英文简称')->textInput(['maxlength' => '20', 'placeholder' => '请输入模块的英文简称'])?>
	    <?php if($model->isNewRecord):?>
	       <?= $form->field($model, 'menus')->textarea(['rows' => 6, 'placeholder' => '菜单的格式为(菜单url,菜单名称,是否显示在侧边栏，是否超级管理员功能): url1,菜单名称,0,0;url2,菜单名称,0,0;'])?>
	    <?php endif;?>
	    <?= $form->field($model, 'status')->dropDownList(Title::$getStatus) ?>
	    <div id="preview" <?php if($model->isNewRecord){ echo 'style="display:none"';}?>>  
          <img id="imghead" class="image_format" width="160px" alt="" border="0" src='<?php if(!$model->isNewRecord){echo Yii::$app->request->baseUrl.'/'.$model->icon_url;}?>'>        
        </div>
        <div class="btn btn-info img-upload-btn">
         <?= $form->field($model, 'icon_url')->fileInput(['class' => 'icon_img','onchange' => "previewImage(this)",'title' => "上传模块图标"]) ?>
        </div>
	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord?'添加':'修改', ['class' => 'btn btn-success'])?>
	        <?= Html::a('返回列表',Url::to(['@moduleAdminIndex']),['class' => 'btn btn-primary']) ?>
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
