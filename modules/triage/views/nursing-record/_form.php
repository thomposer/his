<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\spot\models\NursingRecordTemplate;
use dosamigos\datetimepicker\DateTimePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\triage\models\NursingRecord */
/* @var $form yii\widgets\ActiveForm */

$attribute = $model->attributeLabels();
$versionNumber = Yii::getAlias("@versionNumber");
$baseUrl = Yii::$app->request->baseUrl;
$getContentUrl = Url::to(['@apiTriageGetContent']);
?>

<div class="nursing-record-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class = 'row'>

        <div class = 'col-md-6'>
            <?= $form->field($model, 'executor')->textInput(['maxlength' => true])->label($attribute['executor'].'<span class = "label-required">*</span>') ?>
        </div>

        <div class = 'col-md-6'>
            <?php
            echo $form->field($model, 'execute_time')->widget(
                DateTimePicker::className(), [
                    'inline' => false,
                    'language' => 'zh-CN',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd hh:ii',
                        'minuteStep'=>1,
                    ]
                ]
            )->label($attribute['execute_time'].'<span class = "label-required">*</span>')
            ?>
        </div>

    </div>

    <div class = 'row'>

        <div class = 'col-md-6'>
            <?= $form->field($model, 'template_id')->dropDownList(ArrayHelper::map(NursingRecordTemplate::getNursingRecordTemplate(), 'id', 'name'),['prompt' => '请选择'])->label($attribute['template_id'].'<span class = "label-required">*</span>') ?>
            <?php //$form->field($model, 'name')->hiddenInput()->label(false) ?>
        </div>

    </div>

    <div class = 'row'>

        <div class = 'col-md-12'>
            <?= $form->field($model, 'content')->textarea(['rows' => 6])->label($attribute['content'].'<span class = "label-required">*</span>') ?>
        </div>

    </div>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

<?php
$js = <<<JS
   var baseUrl = '$baseUrl';
   var getTemplateContentUrl = '$getContentUrl';
   require(["$baseUrl/public/js/triage/triageModal.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);

$css = <<<CSS
    #ajaxCrudModal .modal-header {
        border-bottom: 1px solid #f4f4f4;
    }
    #ajaxCrudModal .modal-body {
        padding: 15px 30px;
    }
CSS;
$this->registerCss($css);
?>
