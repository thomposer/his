<?php
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\Appointment */
/* @var $form yii\widgets\ActiveForm */
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;

?>
<?php $this->beginBlock('renderCss')?>
<?php
$css = <<<CSS
   #ajaxCrudModal .modal-body {
         padding: 15px 30px;
    }
    .field-patientrecord-status > label{
        float: left;
        margin-right: 12px;
    }
    #patientrecord-status label{
        /*float: left;*/
        margin-left: 20px;
    }

CSS;
$this->registerCss($css);
?>
<?php $this->endBlock()?>
<?php
$form = ActiveForm::begin([
    'options' => [
//        'class' => 'form-horizontal',
    ]
]);
?>
<div class = 'row'>
    <div class="col-sm-12">
        <?= $form->field($recordModel, 'status')->radioList(['7' => '取消预约', '8' => '用户失约'])->label('请选择<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-md-12'>
        <?= $form->field($model, 'appointment_cancel_reason')->textInput(['maxlength'=>30,'placeholder'=>'您可填写取消原因或者其他备注信息（不超过30个字）','class' => 'form-control appointment-cancel-reason'])->label($attributes['appointment_cancel_reason']); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl; ?>';
    require([baseUrl + '/public/js/lib/common.js']);
</script>
