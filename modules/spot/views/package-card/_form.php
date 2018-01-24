<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use rkit\yii2\plugins\ajaxform\Asset;
use app\modules\spot\models\PackageCard;
use app\assets\AppAsset;
use dosamigos\datetimepicker\DateTimePickerAsset;
use app\specialModules\recharge\models\MembershipPackageCard;
use app\modules\spot\models\PackageCardService;
DateTimePickerAsset::register($this)->js[] = 'js/locales/bootstrap-datetimepicker.zh-CN.js';
Asset::register($this);
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\PackageCardService */
/* @var $form yii\widgets\ActiveForm */
$contentPlaceholder = '请输入套餐内容（不多于500个字）\n'
                    . '例：2次儿童保健门诊、1次泌乳/辅食营养门诊\n'
                    . '注：此部分内容会发送短信给用户，请认真填写并核对信息的正确性。';
                    
$mouseover = '若找不到您想要的服务类型，可先返回列表页进入【套餐卡服务类型管理】处添加您想要的服务类型';
if($model->create_time) {
    $model->create_time = date('Y-m-d H:i:s',$model->create_time);
}else{
    $model->create_time = date('Y-m-d H:i:s');
}
//判断是否有用户购买
if(MembershipPackageCard::getBuyCount($model->id)){
    $readonly = true;
    $packageCardServiceList = array_column(PackageCardService::getServiceList(['status' => [1,2]]), 'name', 'id');
}else{
    $readonly = false;
}

?>
<?php $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/spot/packageCard.css') ?>
<?php $this->endBlock()?>
<div class="package-card-form col-md-8">

    <?php $form = ActiveForm::begin(['id' => 'package-card-form']); ?>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => '请输入套餐名称（不多于20个字）', 'readonly' => $readonly])->label('套餐名称<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'product_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>   
            <?= $form->field($model, 'meta')->textInput(['maxlength' => true]) ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'validity_period')->textInput(['placeholder' => '请输入有效期，有效期为1-30的整数', 'readonly' => $readonly])->label($attributes['validity_period'] . '<span class = "label-required">*</span>') ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'price')->textInput(['readonly' => $readonly])->label($attributes['price'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'default_price')->textInput() ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?php 
                
            ?>
            <?= $form->field($model, 'content')->textarea(['maxlength' => true, 'rows' => 5]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'remarks')->textarea(['maxlength' => true, 'rows' => 5, 'placeholder' => '请输入备注（不多于500个字）']) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'create_time')->textInput(['readonly' => true]) ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'status')->dropDownList(PackageCard::$getStatus, ['prompt' => '请选择'])->label($attributes['status'] . '<span class = "label-required">*</span>') ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6 title-item">
            <span class="item-num"></span><span class="item-text">服务信息</span>
        </div>
    </div>
    
    <div class="package-card-service-list">
        <div class = 'row'>
            <div class = 'col-md-6'>
                <label class="control-label">服务类型<span class="label-required">*</span></label>
                <span class="fa fa-question-circle blue" data-toggle="tooltip" data-html="true" data-placement="left" data-original-title="<?= $mouseover ?>" style="float: right;"></span>
            </div>
            <div class = 'col-md-4'>
                <label class="control-label">总次数<span class="label-required">*</span></label>
            </div>
        </div>
        <?php if ($packageServiceUnionList):?>
            <?php foreach ($packageServiceUnionList as $key => $value):?>
                <div class = 'row'>
                    <div class = 'col-md-6'>
                        <?php  if($readonly): ?>
                            <?= Html::dropDownList('PackageServiceUnion[package_card_service_id][]', $value['package_card_service_id'], $packageCardServiceList, ['class' => 'form-control', 'prompt' => '请选择','disabled' => $readonly]) ?>
                            <?= Html::input('text', 'PackageServiceUnion[package_card_service_id][]', $value['package_card_service_id'], ['class' => 'form-control hidden']) ?>
                        <?php else: ?>
                            <?= Html::dropDownList('PackageServiceUnion[package_card_service_id][]', $value['package_card_service_id'], $packageCardServiceList, ['class' => 'form-control', 'prompt' => '请选择']) ?>
                        <?php endif ?>
                        <div class="help-block"></div>
                    </div>
                    <div class = 'col-md-4'>
                        <?= Html::input('text', 'PackageServiceUnion[time][]', $value['time'], ['class' => 'form-control', 'placeholder' => '请输入服务总次数，总次数为1-999的整数','readonly' => $readonly]) ?>
                        <div class="help-block"></div>
                    </div>
                    <?php if(!$readonly): ?>
                        <div class="col-md-2">
                            <div class="form-group ">
                                <a href="javascript:void(0);" class="btn-service-delete-add btn service-delete" style="display: inline-block;">
                                    <i class="fa fa-minus"></i>
                                </a><a href="javascript:void(0);" class="btn-service-delete-add  btn service-add" style="display: inline-block;">
                                    <i class="fa fa-plus"></i>
                                </a>                
                            </div>
                        </div>
                    <?php endif ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class = 'row'>
                <div class = 'col-md-6'>
                    <?= Html::dropDownList('PackageServiceUnion[package_card_service_id][]', '', $packageCardServiceList, ['class' => 'form-control', 'prompt' => '请选择'])?>
                    <div class="help-block"></div>
                </div>
                <div class = 'col-md-4'>
                    <?= Html::input('text', 'PackageServiceUnion[time][]', '', ['class' => 'form-control', 'placeholder' => '请输入服务总次数，总次数为1-999的整数']) ?>
                    <div class="help-block"></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group ">
                        <a href="javascript:void(0);" class="btn-service-delete-add btn service-delete" style="display: inline-block;">
                            <i class="fa fa-minus"></i>
                        </a><a href="javascript:void(0);" class="btn-service-delete-add  btn service-add" style="display: inline-block;">
                            <i class="fa fa-plus"></i>
                        </a>                
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <?= Html::a('取消', ['package-card-index'], ['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->beginBlock('renderJs')?>
<?php AppAsset::addScript($this, '@web/public/js/lib/common.js') ?>
<script type="text/javascript">
    var contentPlaceholder = '<?= $contentPlaceholder ?>';
    var packageCardServiceList = <?= json_encode($packageCardServiceList,true) ?>;
    require(["<?= $baseUrl ?>" + "/public/js/spot/packageCard.js"], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock()?>


