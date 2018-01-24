<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
$attribute = $model->attributeLabels();
/* @var $this yii\web\View */
/* @var $model app\modules\medicine\models\MedicineDescription */
/* @var $form yii\widgets\ActiveForm */
?>
<?= $this->render('_tab') ?>
<div class = 'tab-content'>
   <div id = 'americanChildren' class="tab-pane active">
    <div class="medicine-description-form">
    
        <?php $form = ActiveForm::begin([
            'action' => Url::to(['@medicineIndexCreate','id' => $id]),
            'method' => 'post'
        ]); ?>
    
        <?= $form->field($model, 'indication')->textInput()->label($attribute['indication'].'<span class = "label-required">*</span>') ?>
    
        <?= $form->field($model, 'used')->textarea(['rows' => 6]) ?>
    
        <?= $form->field($model, 'renal_description')->textarea(['rows' => 6]) ?>
        
        <?= $form->field($model, 'liver_description')->textarea(['rows' => 6]) ?>
        
        <?= $form->field($model, 'contraindication')->textarea(['rows' => 6]) ?>
        
        <?= $form->field($model, 'side_effect')->textarea(['rows' => 6]) ?>
        
        <?= $form->field($model, 'pregnant_woman')->textarea(['rows' => 6]) ?>
        
        <?= $form->field($model, 'breast')->textarea(['rows' => 6]) ?>
        
        <?= $form->field($model, 'careful')->textarea(['rows' => 6]) ?>
        
    	<?php if (!Yii::$app->request->isAjax){ ?>
    	  	<div class="form-group">
    	        <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    	    </div>
    	<?php } ?>
    
        <?php ActiveForm::end(); ?>
     </div> 
  </div>
</div>