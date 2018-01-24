<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Permission */
/* @var $form yii\widgets\ActiveForm */
$this->title = '新增权限分类';
$this->params['breadcrumbs'][] = ['label' => '权限管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$attribute = $model->attributeLabels();
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
  <!-- Select2 -->
    <?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>   
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="permission-form col-xs-12" >
    <div class = 'box'>
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>      
    </div>
        <div class = 'box-body'>
         <div class = 'col-md-6'>
            <?php $form = ActiveForm::begin([
          'options' => ['class' => 'form-input'],
          ]); ?> 
       
        <?= $form->field($model, 'category')->textInput(['maxlength' => true,'placeholder'=>'必须：英文缩写'])->label($attribute['category'].'<span class = "label-required">*</span>') ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 6])->label($attribute['description'].'<span class = "label-required">*</span>') ?>    
        <div class="form-group" >
            <?= Html::a('取消',Yii::$app->request->referrer,['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        	<?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
        </div>
        <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
    <script type="text/javascript">
    	require(["<?= $baseUrl ?>"+"/public/js/rbac/permission.js?v="+'<?= $versionNumber ?>'],function(main){
    		main.init();
		});
		
	</script>        
<?php $this->endBlock();?>
<?php AutoLayout::end();?>