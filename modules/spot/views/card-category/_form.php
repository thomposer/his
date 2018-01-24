<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\spot\models\CardRechargeCategory;
use rkit\yii2\plugins\ajaxform\Asset;
use yii\helpers\Html;

Asset::register($this);
$attribute = $model->attributeLabels();
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CardRechargeCategory */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$form = ActiveForm::begin([
            'id' => 'card-category'
        ]);
?>

<div class = 'row'>
    <div class = 'col-md-6'>
        <?= $form->field($model, 'f_category_name')->textInput(['maxlength' => true])->label('卡种' . $attribute['f_category_name'] . '<span class = "label-required">*</span>') ?> 
    </div>
    <div class = 'col-md-6'>
        <?= $form->field($model, 'f_parent_id')->dropDownList(ArrayHelper::map($cardCategory, 'f_physical_id', 'f_category_name'), ['prompt' => '请选择'])->label('所属卡组<span class = "label-required">*</span>') ?> 
    </div>
</div>

<div class = 'row'>
    <div class = 'col-md-12'>
        <?= $form->field($model, 'f_category_desc')->textarea(['rows' => 4, 'placeholder' => '请输入' . $attribute['f_category_desc'] . '(不多于100个字)', 'maxlength' => 100]) ?>
    </div>
</div>
<div>服务折扣（%）</div>
<div class="service-content form-group">
    <div class = 'row'>
        <div class = 'col-md-4 feeDiscount'>
            诊金
            <?= Html::hiddenInput('tag_id[]', 0) ?>
        </div>

    </div>
    <div class = 'service-card-discount'>
        <?php foreach ($cardDiscountList as $k => $val): ?>
            <div class="appointConfig"><div class="row clinic-shift-time">
                    <div class="col-sm-4">
                        <div class="form-group field-carddiscount-tag_id ">
                            <?= Html::dropDownList('tag_id[]', $val['tag_id'], ArrayHelper::map($tagList, 'id', 'name'), ['class' => 'form-control tag-select', 'prompt' => '请选择充值卡折扣标签', 'selectV' => $val['tag_id']]) ?>
                            <?= Html::hiddenInput('deleted[]', 2, ['class' => 'deleted']) ?>
                            <?= Html::hiddenInput('newRecord[]', $val['tag_id'] == '' ? 1 : 2, ['class' => 'new-record']) ?>
                            <?= Html::hiddenInput('change[]', -1, ['class' => 'change']) ?>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <a href="javascript:void(0);" class="btn-from-delete-add btn clinic-delete btn-from-delete-add-margin-top-0" style="display: inline-block;">
                                <i class="fa fa-minus"></i>
                            </a> 
                            <a href="javascript:void(0);" class="btn-from-delete-add btn clinic-add btn-from-delete-add-margin-top-0" style="display: none;"> 
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div>自动升级</div>
<div class="service-content">
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'f_auto_upgrade')->radioList(CardRechargeCategory::$getAutoUpgrage)->label(false) ?> 
        </div>
    </div>
    <div class = 'row auto-upgrade-content'>
        <div class = 'col-md-10'>
            <?= $form->field($model, 'f_upgrade_amount')->textInput(['maxlength' => true])->label('条件：' . $model->f_upgrade_time . '天内，充值额累计（元）') ?> 
        </div>
        <!--        <div class = 'col-md-6 auto_upgrade'>
                    <div class="form-group field-cardrechargecategory-f_upgrade_time">
                        <label class="control-label" for="cardrechargecategory-f_upgrade_time">时长范围：</label>
        <?php // echo $model->f_upgrade_time  ?>天
                        <div class="help-block"></div>
                    </div>
                </div>-->
    </div>
</div>
<div class="form-group text-center">
    <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]); ?>	
    <?= Html::submitButton('确认', ['class' => 'btn btn-default btn-form']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php
$baseUrl = Yii::$app->request->baseUrl;
$tagList = json_encode($tagList, true);
$cardDiscountList = json_encode($cardDiscountList, true);
$js = <<<JS
   var tagList = $tagList; 
   var cardDiscountList = $cardDiscountList;
   require(["$baseUrl/public/js/spot/cardCate.js"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
