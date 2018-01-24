<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot_set\models\OutpatientPackageTemplate;
use yii\helpers\ArrayHelper;
use rkit\yii2\plugins\ajaxform\Asset;
use yii\bootstrap\Tabs;
Asset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\OnceDepartment */
/* @var $form yii\widgets\ActiveForm */
$templateModel = $model->getModel('packageTemplate');
$templateAttributes = $templateModel->attributeLabels();
?>

<div class="once-department-form">

    <?php $form = ActiveForm::begin(['id' => 'package-template']); ?>
    <div class = 'col-md-8 template-padding-div'>
	<div class = 'row'>
		<div class = 'col-md-12'>
			 <?= $form->field($templateModel, 'name')->textInput(['maxlength' => true])->label($templateAttributes['name'].'<span class = "label-required">*</span>') ?>
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-md-6'>
			 <?= $form->field($templateModel, 'price')->textInput(['maxlength' => true])->label($templateAttributes['price'].'<span class = "label-required">*</span>') ?>
		</div>
		<div class = 'col-md-6'>
			 <?= $form->field($templateModel, 'type')->radioList(OutpatientPackageTemplate::$getType)->label($templateAttributes['type'].'<span class = "label-required">*</span>') ?>
		</div>
	</div>
	<div class = 'row'>
		<div class = 'col-md-6'>
			<?= $form->field($templateModel, 'medical_fee_price')->textInput(['maxlength' => true,'placeholder' => '请输入诊金，0-100000'])->label($templateAttributes['medical_fee_price'].'<span class = "label-required">*</span>') ?>
		</div>
	</div>
	</div>
	<div class = 'col-md-12 tab-padding-zero'>
	<?=
    Tabs::widget([
        'renderTabContent' => false,
        'navType' => ' outpatient-template-tab nav-tabs',
        'items' => [
            [
                'label' => '检验医嘱',
                'options' => ['id' => 'package-inspect']     
            ],   
            [
                'label' => '检查医嘱',
                'options' => ['id' => 'package-check']
            ],
            [
                'label' => '治疗医嘱',
                'options' => ['id' => 'package-cure']
            ],
            [
                'label' => '处方医嘱',
                'options' => ['id' => 'package-recipe']
            ],
        ]
    ]);
    ?>
    <div class = 'tab-content padding-px'>
    	<div id = "package-inspect" class="tab-pane active">
    		<?=
    		  $this->render('_inspect',[
    		      'form' => $form,
    		      'inspectModel' => $model->getModel('packageInspect'),
    		      'inspectDataProvider' => $inspectDataProvider
    		  ]);
    		
    		?>
    	</div>
    	<div id = "package-check" class="tab-pane">
    		<?=
    		  $this->render('_check',[
    		      'form' => $form,
    		      'checkModel' => $model->getModel('packageCheck'),
    		      'checkDataProvider' => $checkDataProvider
    		  ]);
    		
    		?>
    	</div>
    	<div id = "package-cure" class="tab-pane">
    		<?=
    		  $this->render('_cure',[
    		      'form' => $form,
    		      'cureModel' => $model->getModel('packageCure'),
    		      'cureDataProvider' => $cureDataProvider,
    		      'disabledCure' => $disabledCure
    		  ]);
    		
    		?>
    	</div>
    	<div id = "package-recipe" class="tab-pane">
    		<?=
    		  $this->render('_recipe',[
    		      'form' => $form,
    		      'recipeModel' => $model->getModel('packageRecipe'),
    		      'recipeDataProvider' => $recipeDataProvider
    		  ]);
    		?>
    	</div>
    </div>
    </div>
    <div class="form-group col-md-12">
        <?= Html::a('取消',['package-template-index'],['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
