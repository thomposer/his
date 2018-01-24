<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot_set\models\SpotType;
use app\modules\spot\models\OrganizationType;
use app\assets\AppAsset;



/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\spotType */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
$status = OrganizationType::getTypeFields($model->organization_type_id,['status'])['status'];
$status == 2 ? $disabled = true : $disabled = false;
if($disabled){
    $organizationType = array_column(OrganizationType::getSpotType(), 'name','id');
}else{
    $organizationType = array_column(OrganizationType::getSpotType('status = 1'), 'name','id');
}

?>
<?php $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/spot/timeConfig.css')?>
<?php $this->endBlock()?>
<div class="spot-type-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'organization_type_id')->dropDownList($organizationType,['prompt'=>'请选择', 'disabled' => $disabled])->label($attributeLabels['organization_type_id'].'<span class = "label-required">*</span>'); ?>

    <?= $form->field($model, 'time')->dropDownList(SpotType::$getTime,['prompt'=>'请选择'])->label($attributeLabels['time'].'<span class = "label-required">*</span>'); ?>
    <div class="row">
        <div class="col-sm-12">
        <span><?=Html::encode($attributeLabels['third_platform'])?>：&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color:#FF5000 ;">（注：勾选平台后，则平台允许对该服务类型进行预约操作）</span>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model,'thirdPlatform')->checkboxList(SpotType::$getThirdPlatform,['class'=>'custom-appointment-third-platform'])->label(false); ?>
            </div>
        </div>


	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
<script type="text/javascript">
        var status = '<?= $model->isNewRecord ? 1 : 0; ?>';
</script>
    