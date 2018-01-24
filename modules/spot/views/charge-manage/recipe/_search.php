    <?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\search\RecipeListSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="recipe-list-search hidden-xs">

    <?php $form = ActiveForm::begin([
        'action' => ['recipe-index'],
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>

    <?= $form->field($model, 'product_name')->textInput(['placeholder' => '请输入'.$attributeLabels['product_name'] ]) ?>

    <div class="form-group field-advicetagid">
		<a  href = "<?=Url::to(['@apiTagSearch','discountTagChecked' => $model->discountTagChecked,'commonTagChecked' => $model->commonTagChecked]) ?>" id = "apiTagSearchUrl" data-request-method="post" role="modal-remote" data-toggle="tooltip"> 
		<?= $form->field($model, 'adviceTagId')->textInput(['id' => 'search-advicetagid','placeholder' => '请选择药品标签']) ?>
		</a>
		<?= $form->field($model, 'discountTagChecked')->hiddenInput(['id' => 'discountTagChecked'])->label(false) ?>
		<?= $form->field($model, 'commonTagChecked')->hiddenInput(['id' => 'commonTagChecked'])->label(false) ?>
	</div>
    
    <?= $form->field($model, 'unionSpotId')->dropDownList(array_column($spotList, 'spot_name', 'id'),['prompt'=>'请选择'.$attributeLabels['unionSpotId'] ]) ?>
        
    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
