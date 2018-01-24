<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
use app\modules\spot\models\SpotConfig;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\SpotConfig */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
$attributeLabels=$model->attributeLabels();
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css')?>
<?php AppAsset::addCss($this, '@web/public/css/spot/spotConfig.css')?>
<?php AppAsset::addCss($this, '@web/public/css/spot/timeConfig.css')?>
<?php  $this->endBlock();?>
    <div class = "box time-config">
        <div class = "box-body">
            <div class="spot-config-form col-md-8">
                <?php $form = ActiveForm::begin(); ?>
                <div class = 'row'>
                    <div class = 'col-md-6 bootstrap-timepicker'>
                    
                    	<?php
                            echo $form->field($model, 'begin_time')->textInput(['class' => 'form-control timepicker'])->label($attributeLabels['begin_time'].'<span class = "label-required">*</span>');
                         ?>
                    </div>
                    <div class = 'col-md-6 bootstrap-timepicker'>
                    <?php
                            echo $form->field($model, 'end_time')->textInput(['class' => 'form-control timepicker'])->label($attributeLabels['end_time'].'<span class = "label-required">*</span>');
                    ?>
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'col-md-6'>
                    <?= $form->field($model, 'reservation_during')->dropDownList(SpotConfig::$getTimeConfig) ?>
                    </div>
                </div>
               
                <div class="form-group">
                    <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
                </div>
            
                <?php ActiveForm::end(); ?>
            
            </div>
        </div>
    </div>

<?php  $this->beginBlock('renderJs')?>
<script type="text/javascript">

		require(["<?= $baseUrl ?>"+"/public/js/spot/spotConfig.js?v="+'<?= $versionNumber ?>'],function(main){
			main.init();
		})
</script>
<?php  $this->endBlock()?>

