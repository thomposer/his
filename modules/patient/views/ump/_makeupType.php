<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\Outpatient */
/* @var $form yii\widgets\ActiveForm */
$this->registerCss('
    #ajaxCrudModal .modal-body {
        padding: 30px 10px 0px 10px;
    }
');
?>

<div class="row">
    <div class="col-md-12">
        <div class="form-group field-usercard-checktype required">
            <div id="usercard-checktype" class="radio-inline"><label class="radio-inline"><input type="radio" name="makeup_type" value="1" checked=""> 预约就诊</label></div>
            <div id="usercard-checktype" class="radio-inline"><label class="radio-inline"><input type="radio" name="makeup_type" value="2"> 直接登记</label></div>

            <div class="help-block"></div>
        </div>
    </div>
</div>
