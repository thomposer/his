<?php
use app\assets\AppAsset;
use yii\helpers\Url;
use app\modules\spot\models\RecipeList;


/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\RecipeList */

$baseUrl = Yii::$app->request->baseUrl;
?>

<?php  AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>


<div class="recipe-list-create col-xs-12">
    <div class = "box-body">
        <?= $this->render('_form', [
            'model' => $model,
            'recipeList'=>$recipeList
        ]) ?>
    </div>
</div>

<script type="text/javascript">
    var recipeList = <?= json_encode($recipeList,true) ?>;
    var doseUnit =  <?= json_encode(RecipeList::$getDoseUnit,true) ?>;
    var error = <?= $model->errors?1:0 ?>;
    require(["<?= $baseUrl ?>"+"/public/js/spot_set/recipeClinic.js?v="+versionNumber],function(main){
        main.init();
    });
</script>

