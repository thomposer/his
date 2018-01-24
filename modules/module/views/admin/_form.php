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
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/dist/css/cropper.min.css') ?>
    <?php AppAsset::addCss($this, '@web/public/css/lib/upload.css')?>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="create-form col-xs-12">
	<div class = "box">
	<div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?= Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>
    </div>
        <div class = "box-body">
        <div class = "col-md-8">
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
	    <?= $form->field($model, 'type')->dropDownList(Title::$getType) ?>
	    <?= $form->field($model, 'icon_url')->textInput(['id' =>'avatar_url']); ?>	                    
        <div id="crop-avatar">
            <!-- Current avatar -->
            <div class="avatar-view" title="修改图标">
               <?php if($model->icon_url):?>
               <?= Html::img($model->icon_url,['alt' => '图标']) ?>
               <?php else:?>
                <?= Html::img($baseUrl.'/public/img/default.png',['alt' => '图标'])?>
               <?php endif;?>              
            </div>            
        </div>
        
	    <div class="form-group">
	        <?= Html::submitButton('保存', ['class' => 'btn btn-default'])?>
	        <?= Html::a('取消',Url::to(['@moduleAdminIndex']),['class' => 'btn btn-cancel']) ?>
	    </div>
	    <?php ActiveForm::end(); ?>
	    </div>
	    </div>
	</div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
   <script type = "text/javascript">
   	var baseUrl = '<?= $baseUrl ?>';
   	var uploadUrl = '<?= Url::to(['@manageSitesUpload']); ?>'
	require(['<?= $baseUrl?>'+'/public/js/module/form.js?v='+'<?= $versionNumber ?>'],function(main){
		main.init();
	})
   </script> 
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
