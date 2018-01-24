<?php

use yii\widgets\ActiveForm;
use app\specialModules\recharge\models\CardRecharge;
use dosamigos\datetimepicker\DateTimePickerAsset;
use yii\helpers\Url;
use rkit\yii2\plugins\ajaxform\Asset;
use app\assets\AppAsset;
use kartik\datetime\DateTimePicker;
Asset::register($this);

// DateTimePickerAsset::register($this)->js[] = 'js/locales/bootstrap-datetimepicker.zh-CN.js';
/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CardRecharge */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>
<?php
$this->registerCss("
           .title-item{
                line-height:16.5px;
                margin: 15px 0 7.5px 0;
            }
            .item-num{
                width: 4px;
                height: 15px;
                border-radius: 25px;
                background: #76A6EF;
                float: left;
                margin-right: 5px;
            }
            .item-text{
                font-size: 15.5px;
                font-weight: 600;
            }
            .first-recharge-info,.card-basic-info{
                margin-bottom: 10px;
            }
            .phone-label-tips{
                font-size:12px;
                color:#A8B1BC;
                padding-left:5px;
            }
            .btn-create-card{
                margin: 20px 10px 20px 10px;
                background-color: #fff;
                border: 1px solid #76a6ef;
                color: #76a6ef;
                opacity: 1;
            }
           .btn-create-card:hover, .btn-create-card:visited, .btn-create-card:active {
                background-color: #fff;
                border: 1px solid #76a6ef;
                color: #76a6ef;
                opacity: 1;
            }
    ")
?>
<?php AppAsset::addCss($this, '@web/public/css/make_appointment/selectPatient.css') ?>

<?php $this->beginBlock('renderCss') ?>
<?php $this->endBlock(); ?>
<div class="card-recharge-form col-md-12">

    <?php
    $form = ActiveForm::begin([
                'id' => 'card-info-form',
    ]);
    ?>
    <div class="card-basic-info">
        <div class = 'row'>
            <div class="col-sm-6 title-item">
                <span class="item-num"></span><span class="item-text">卡基本信息</span>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'f_card_id')->textInput(['maxlength' => true]) ?>
            </div>
            <div class = 'col-sm-6 buy-time'>
                <?=
                   $form->field($model, 'f_buy_time')->widget(
                       DateTimePicker::className(), [
                       'language' => 'zh-CN',
                       'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                       'readonly' => true,
                       'pluginOptions' => [
                           'autoclose' => true,
                           'format' => 'yyyy-mm-dd hh:ii',
                           'size'=>'lg',
                           'minuteStep'=>1,
                       ],
                       'options' => [
                           'autocomplete' => 'off',
                       ],
                   ])->label($attribute['f_buy_time']);
//                 if ($model->f_buy_time) {
//                     $model->f_buy_time = strstr($model->f_buy_time, '-')?$model->f_buy_time:date('Y-m-d H:i', $model->f_buy_time);
//                     echo $form->field($model, 'f_buy_time')->textInput(['maxlength' => true,'readonly' => true]);
//                 } else {
//                     if ($model->isNewRecord) {
//                         echo $form->field($model, 'f_buy_time')->textInput(['maxlength' => true, 'value' => date('Y-m-d H:i'),'readonly' => true]);
//                     } else {
//                         $model->f_buy_time = '';
//                         echo $form->field($model, 'f_buy_time')->textInput(['maxlength' => true,'readonly' => true]);
//                     }
//                 }
                ?>
            </div>
        </div>
        <?php //if ($model->isNewRecord): ?>
            <div class="row">
                <div class = 'col-sm-6'>
                    <?= $form->field($model, 'f_category_id')->dropDownList($cardCategory, ['prompt' => '请选择', $model->isNewRecord ? "" : "disabled" => "disabled"])->label($attribute['f_category_id'] . '<span class = "label-required">*</span>') ?>
                </div>
                <div class = 'col-sm-6'>
                    <?= $form->field($model, 'f_sale_id')->dropDownList($sales, ['prompt' => '请选择']) ?>
                </div>
            </div>
<?php //endif; ?>
        <div class = 'row'>
            <div class = 'col-sm-12'>
                <?= $form->field($model, 'f_give_info')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="customer-info">
        <div class = 'row'>
            <div class="col-sm-6 title-item">
                <span class="item-num"></span><span class="item-text">客户信息</span>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'f_phone')->textInput(['maxlength' => true, 'autocomplete' => 'off'])->label($attribute['f_phone'] . '<span class="label-required">*</span><span class="phone-label-tips">(需与就诊患者的手机号一致)</span>'); ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'f_user_name')->textInput(['maxlength' => true])->label($attribute['f_user_name'] . '<span class="label-required">*</span>'); ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'f_id_info')->textInput(['maxlength' => true]) ?>
            </div>
            <div class = 'col-sm-6'>   
                <?= $form->field($model, 'f_baby_name')->textInput(['maxlength' => true]) ?>
                <input type="hidden" id="cardrecharge-submitType" class="form-control cardSubmitType" name="CardRecharge[submitType]" value="1">
            </div>
        </div>
    </div>
    <div id="phone-card-category">

    </div>
    <?php ActiveForm::end(); ?>
    <?php
    $cardCategory = Url::to(['@apiRechargeGetPhoneCardCategory']);
    $cardDeatail = Url::to(['@cardRechargePreview']);
    $baseUrl = Yii::$app->request->baseUrl;
    $js = <<<JS
      var baseUrl = "$baseUrl";
      var getPhoneCategory = '$cardCategory';
      var cardDeatail = '$cardDeatail';
       require([baseUrl + "/public/js/recharge/create.js"], function (main) {
            main.init();
       });
JS;
    $this->registerJs($js);
    ?>
</div>
