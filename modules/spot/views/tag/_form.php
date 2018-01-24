<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\Tag;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Tag */
/* @var $form yii\widgets\ActiveForm */

$attribute = $model->attributeLabels();

?>
<?php
    $this->registerCss('
        .tag-form .field-tag-type{
            margin-bottom: 0px;
        }
        .tag-form .field-tag-type-tips{
            margin-bottom: 15px;
        }
            ');
?>
<div class="tag-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => '请输入标签名称，不超过10个字'])->label($attribute['name'].'<span class = "label-required">*</span>') ?>
        <?= $form->field($model, 'type')->dropDownList(Tag::$getType, ['prompt' => '请选择', 'disabled' => ($haveUnion ? true : false)])->label($attribute['type'].'<span class = "label-required">*</span>') ?>
        <div class="field-tag-type-tips"><span style="color:#ff4b00;">Tips：</span>充值卡折扣标签，可用于会员卡充值打折，通用标签用于筛选</div>
        <?= $form->field($model, 'description')->textarea(['style' => 'height: 100px;','placeholder' => '请输入标签描述，不超过100个字']) ?>
        <?php //  $form->field($model, 'status')->dropDownList($model::$getStatus)->label($attribute['status'].'<span class = "label-required">*</span>') ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
