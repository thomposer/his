<?php
use app\assets\AppAsset;
use yii\helpers\Url;
use app\modules\spot\models\RecipeList;


/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\RecipeList */

$baseUrl = Yii::$app->request->baseUrl;
?>
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
    require(["<?= $baseUrl ?>"+"/public/js/spot_set/recipeClinic.js"],function(main){
        main.init();
    });
</script>
