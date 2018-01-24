<?php

use app\specialModules\recharge\models\CardRecharge;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CardRecharge */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
$readonly = $model->isNewRecord ? false : true;
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
//            .btn-register{
//                margin-left:20px;
//                border-radius: 30px;
//                border: 1px solid #76a6ef;
//            }
            .btn-water{
                background-color: #76a6ef;
                border-radius: 30px;
                color: #ffffff;
            }
            .trading-operation{
                padding-top: 25px;
            }
            .his-pencil {
                padding-right: 20px;
                width: 15px;
                height: 12px;
                background: url(/H5-his/public/img/shape.png) no-repeat top;
            }
            .card-recharge-form-edit{
                color: #99A3B1;
                float: right;
                margin-top:20px;
                font-size:14px;
                width: 70px;
                height: 24px;
                border-radius: 30px;
                background-color: #EDF1F7;
                line-height: 24px;
                text-align: center;
                touch-action: manipulation;
                cursor: pointer;
            }
            .card-recharge-form-edit:hover{
                color: #99A3B1;
            }
            .first-recharge-info,.card-basic-info{
                margin-bottom: 10px;
            }
            .phone-label-tips{
                font-size:12px;
                color:#A8B1BC;
                padding-left:5px;
            }
    ")
?>
<?php  $this->beginBlock('renderCss')?>
<?php  $this->endBlock();?>
<div class="card-recharge-form col-md-8">
    <div class="card-recharge-form-header">
        <?php
                if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/update', $this->params['permList'])) {
                    if(0 == $model->f_is_logout) {
                        echo Html::a("<i class='fa fa-pencil'></i>修改", ['update','id' => $model->f_physical_id], ['class' => 'card-recharge-form-edit', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-modal-size' => 'large', 'data-pjax' => 0]);
                    }
                }
        ?>
    </div>

    <?php $form = ActiveForm::begin(); ?>
    <div class="card-basic-info">
        <div class = 'row'>
            <div class="col-sm-6 title-item">
                <span class="item-num"></span><span class="item-text">卡基本信息</span>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'f_card_id')->textInput(['maxlength' => true,'readonly' => true]) ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'f_buy_time')->textInput(['maxlength' => true,'readonly' => true]) ?>
            </div>
        </div>

        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'f_card_fee')->textInput(['readonly' => true]) ?>
            </div>
            <div class="col-sm-6">
                <div class="form-group field-cardrecharge-f_category_id ">
                    <label class="control-label" for="cardrecharge-f_category_id">所属卡种</label>
                    <input type="text" id="cardrecharge-f_category_id" class="form-control" name="CardRecharge[f_category_id]"  value="<?= Html::encode($model->f_category_id)?>" readonly="">
                </div>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <div class="form-group field-cardrecharge-f_sale_id ">
                    <label class="control-label" for="cardrecharge-f_sale_id">健康顾问</label>
                    <input type="text" id="cardrecharge-f_sale_id" class="form-control" name="CardRecharge[f_sale_id]"  value="<?= Html::encode(CardRecharge::getSales()[$model->f_sale_id])?>" readonly="">
                </div>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-12'>
                <?= $form->field($model, 'f_give_info')->textInput(['maxlength' => true,'readonly' => true]) ?>
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
                <?= $form->field($model, 'f_user_name')->textInput(['maxlength' => true,'readonly' => true])->label($attribute['f_user_name'].'<span class="label-required">*</span>'); ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'f_phone')->textInput(['maxlength' => true,'readonly' => true])->label($attribute['f_phone'].'<span class="label-required">*</span><span class="phone-label-tips">(需与就诊患者的手机号一致)</span>'); ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'f_id_info')->textInput(['maxlength' => true,'readonly' => true]) ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'f_baby_name')->textInput(['maxlength' => true,'readonly' => true]) ?>
            </div>
        </div>
    </div>
    <div class="form-group">
    </div>
<?php ActiveForm::end(); ?>
</div>

