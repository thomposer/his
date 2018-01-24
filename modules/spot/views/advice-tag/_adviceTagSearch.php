<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\search\RecipeListSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php 
    $this->registerCss('
        
        #advicetagrelation-discounttag label,#advicetagrelation-commontag label{
            width : 250px;
        }  
        
        
        
    ');
?>
<div class="advice-tag-search hidden-xs">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
	<div class = 'application-bg'>
        <h5 class = 'title'>充值卡折扣标签</h5>
    </div>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'discountTag')->checkboxList(ArrayHelper::map($discountTag, 'id', 'name'))->label(false); ?>
        </div>
    </div>
    <div class = 'application-bg' style = "margin-top: 30px;">
        <h5 class = 'title'>通用标签</h5>
    </div>
    <div class = 'row' style = "margin-bottom: 15px;">
        <div class = 'col-md-12'>
            <?= $form->field($model, 'commonTag')->checkboxList(ArrayHelper::map($commonTag, 'id', 'name'))->label(false); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
	var apiTagSearchUrl = '<?= Url::to(['@apiTagSearch']) ?>';
    require(["<?= Yii::$app->request->baseUrl ?>" + "/public/js/advice-tag/search.js"], function (main) {
        main.init();
    });

</script>

