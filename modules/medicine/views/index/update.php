<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
$attribute = $model->attributeLabels();
/* @var $this yii\web\View */
/* @var $model app\modules\medicine\models\MedicineDescription */
/* @var $form yii\widgets\ActiveForm */
?>
<?= $this->render('_tab'); ?>
<div class = 'tab-content'>
   <div id = 'americanChildren' class="tab-pane active">
        <div class="medicine-description-form">
        
            <?php $form = ActiveForm::begin([
                'action' => Url::to(['@medicineIndexUpdate','id' => $id]),
                'method' => 'post'
            ]); ?>
            <?php  if(isset($this->params['permList']['role'])||in_array(Yii::getAlias('@medicineIndexCreate'), $this->params['permList'])):?>
                <?= Html::a("<i class='fa fa-plus'></i>新增指征", ['@medicineIndexCreate','id' => $id], ['class' => 'btn btn-default font-body2 medicine-create','data-pjax' => 0,'role'=>'modal-remote','data-toggle'=>'tooltip']) ?>
            <?php endif?>
            <?php if($model->isNewRecord):?>
            <p class = 'no-medicine'>暂无用药指南-使用指征</p>
            <?php else :?>
            <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
            
            <?= $form->field($model, 'indicationSelect')->dropDownList(ArrayHelper::map($medicineItemList, 'id', 'indication'))->label($attribute['indication'].'<span class = "label-required">*</span>') ?>
        
            <?= $form->field($model, 'used')->textarea(['rows' => 6]) ?>
        
            <?= $form->field($model, 'renal_description')->textarea(['rows' => 6]) ?>
            
            <?= $form->field($model, 'liver_description')->textarea(['rows' => 6]) ?>
            
            <?= $form->field($model, 'contraindication')->textarea(['rows' => 6]) ?>
            
            <?= $form->field($model, 'side_effect')->textarea(['rows' => 6]) ?>
            
            <?= $form->field($model, 'pregnant_woman')->textarea(['rows' => 6]) ?>
            
            <?= $form->field($model, 'breast')->textarea(['rows' => 6]) ?>
            
            <?= $form->field($model, 'careful')->textarea(['rows' => 6]) ?>
            <?php endif;?>
        	<?php if (!Yii::$app->request->isAjax){ ?>
        	  	<div class="form-group">
        	        <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        	    </div>
        	<?php } ?>
        
            <?php ActiveForm::end(); ?>
            
        </div>
    </div>
</div>
<script type="text/javascript">
		var getItemUrl = '<?= Url::to(['@apiMedicineDescriptionView']) ?>';
		var medicineIndexDeleteItem = '<?= Url::to(['@medicineIndexDeleteItem']) ?>';
		require([baseUrl+"/public/js/medicine/update.js?v="+versionNumber],function(main){
			main.init();
		});
  </script>