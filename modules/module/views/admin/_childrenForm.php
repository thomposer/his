<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\module\models\Title;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

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
		<?= $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map($title, 'id', 'module_description')) ?>
	    <?= $form->field($model, 'module_description')->textInput(['maxlength' => true, 'placeholder' => '请输入模块的名称'])?>
	    <?= $form->field($model, 'module_name')->label('模块英文简称')->textInput(['maxlength' => '64', 'placeholder' => '请输入模块的英文简称'])?>
	    <?= $form->field($model, 'status')->dropDownList(Title::$getStatus) ?>
	    <?= $form->field($model, 'type')->dropDownList(Title::$getType) ?>
	    <?= $form->field($model, 'sort')->textInput() ?>
	    <div class="form-group">
			<?= Html::a('取消',Url::to(['@moduleAdminIndex']),['class' => 'btn btn-cancel']) ?>
	        <?= Html::submitButton('保存', ['class' => 'btn btn-default'])?>
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
