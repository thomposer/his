<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\specialModules\recharge\models\MembershipPackageCard;
use yii\helpers\Html;
use yii\helpers\Url;
$baseUrl = Yii::$app->request->baseUrl;

/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CardRecharge */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
$packageCardModel = $model->getModel('membershipPackageCard');
$unionModel = $model->getModel('union');
$packageCardModelAttribute = $packageCardModel->attributeLabels();
$unionModelAttribute = $unionModel->attributeLabels();
$action = Yii::$app->controller->action->id;
$id = Yii::$app->request->get('id');
$newRecord = $packageCardModel->isNewRecord;
if(isset($canPatientCreate)){
    $readOnly = true;
    $statusReadOnly = true;
    $patientStatus = true;
}else{
    $readOnly = !$newRecord ?true:false;
    $patientStatus = false;
    if(!$newRecord){
        if(time() > $vidateTime){
            $statusReadOnly = true;
        }else{
            $statusReadOnly = false;
        }
    }
}
if(time() > $vidateTime && !$newRecord){
    $packageCardModel->status = 2;
}


?>
	<div class="recipe-list-form">
        <div class="card-recharge-form-header">
            <?php
            if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/package-card-update', $this->params['permList'])) {
                if(isset($canPatientCreate)) {
                    echo Html::a("<i class='material-dollar fa fa-pencil-square-o'></i>修改", ['package-card-update','id' => $id,'type' => 1], ['class' => 'update-package-card', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large', 'data-pjax' => 0]);
                }
            }
            ?>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'membershipCardForm','action' => $packageCardModel->isNewRecord?Url::to(['@rechargeIndexCreatePackageCard','step' => 1]):'']); ?>
        <?php
            if(!$id) {
                echo $this->render('_stepTab', ['step' => 1]);
            }
        ?>
        <div class="module-title">
            <div class='row'>
                <div class="col-sm-6">
                    <span class="module-title-adorn"></span><span class="module-title-content">卡片信息</span>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($packageCardModel, 'package_card_id')->dropDownList(ArrayHelper::map($cardList,'id','name'),['prompt' => '请选择套餐','class'=>'select2 form-control', 'disabled' => $readOnly])->label($packageCardModelAttribute['package_card_id'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-md-6'>
                <?php
                    if ($newRecord) {
                        echo $form->field($packageCardModel, 'buyTime')->textInput(['maxlength' => true, 'disabled' => 'true', 'value' => date('Y-m-d H:i')]);
                    } else {
                        echo $form->field($packageCardModel, 'buyTime')->textInput(['maxlength' => true, 'disabled' => 'true', 'value' => date('Y-m-d H:i',$packageCardModel->buyTime)]);
                    }
                ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($packageCardModel, 'price')->textInput(['maxlength' => true, 'disabled' => 'true']) ?>
            </div>
            <div class='col-md-6'>
                <?= $form->field($packageCardModel, 'validityTime')->textInput(['maxlength' => true, 'disabled' => 'true']) ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($packageCardModel, 'status')->dropDownList(MembershipPackageCard::$getStatus, ['prompt' => '请选择','disabled' => $statusReadOnly])->label($packageCardModelAttribute['status'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-12'>
                <?= $form->field($packageCardModel, 'content')->textarea(['rows' => 5,'disabled' => true]); ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-12'>
                <?= $form->field($packageCardModel, 'remark')->textInput(['maxlength' => true,'disabled' => $readOnly]) ?>
            </div>
        </div>


        <div class="module-title">
            <div class='row'>
                <div class="col-sm-6">
                    <span class="module-title-adorn"></span><span class="module-title-content">客户信息</span>
                </div>
            </div>
        </div>
        <div class="module-content">
            <div class='row'>
                <div class='col-md-6'>
                    <?= $form->field($unionModel, 'iphone')->textInput(['autocomplete'=>'off','disabled' => $patientStatus,'class' => 'form-control union-iphone'])->label($unionModelAttribute['iphone'].'<span class = "label-required">*（需与就诊者的手机号一致）</span>') ?>
                </div>
                <div class='col-md-6'>
                    <?= $form->field($unionModel, 'patientInfo')->textInput(['readonly' => true]) ?>
                    <?= $form->field($unionModel, 'patient_id')->hiddenInput()->label(false) ?>
                </div>
            </div>
            <?php if(!isset($canPatientCreate)):?>
            <div class = 'row'>
				<div class='col-md-12'>
            	<?php if (isset($this->params['permList']['role']) || in_array(Yii::getAlias('@patientIndexCreate'), $this->params['permList'])) : ?>
				找不到客户?<?= Html::a('点击进入病历库新增>>',['@patientIndexCreate'],['style' => 'text-decoration:underline','target' => '_blank']) ?>	
				<?php endif;?>
				</div>
            </div>
            <?php endif;?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>



