<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\modules\spot\models\RecipeList;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\spot\models\CureList;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\modules\spot_set\models\ClinicCure;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\RecipeTemplate */

$this->title = '修改处方模板';
$this->params['breadcrumbs'][] = ['label' => '医生门诊', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '处方模板', 'url' => ['recipetemplate-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$skinTestList = array_column(ClinicCure::getCureList(null,['b.type' => 1]), 'name','id');
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/recipeTemplate.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@templateSidebar'),['type'=>2]) ?>
<div class="recipe-template-update col-xs-10">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['recipetemplate-index'],['class' => 'right-cancel second-cancel','data-pjax' => 0]) ?>      
    </div>
        <div class = "box-body">
        
            <?= $this->render('_recipeTemplateForm', [
                'model' => $model,
                'type' => $type,
                'recipeTemplateInfoDataProvider' => $recipeTemplateInfoDataProvider,
                'skinTestList' => $skinTestList
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
<?php AppAsset::addScript($this, '@web/public/plugins/select2/select2.full.min.js')?>
<?php AppAsset::addScript($this, '@web/public/plugins/select2/i18n/zh-CN.js') ?>
<script type="text/javascript">
    var baseUrl = "<?= $baseUrl ?>";
    var unit = <?= json_encode(RecipeList::$getUnit, true) ?>;
    var doseUnit = <?= json_encode(RecipeList::$getDoseUnit,true) ?>;
    var defaultUsed = <?= json_encode(RecipeList::$getDefaultUsed,true) ?>;
    var dosage_form = <?= json_encode(RecipeList::$getType,true) ?>;
    var defaultAddress = <?= json_encode(RecipeList::$getAddress, true) ?>;
    var defaultFrequency = <?= json_encode(RecipeList::$getDefaultConsumption, true) ?>;
    var skinTestList = <?= json_encode($skinTestList,true); ?>;
    var itemUrl = '<?= Url::to(['@apiMedicineDescriptionItem']) ?>';//查看用药指南url
    var skinTestStatusList = <?= json_encode(RecipeRecord::$getSkinTestStatus,true) ?>;//皮试状态
    var indexUrl = "<?= Url::to(['@outpatientOutpatientRecipetemplateIndex']) ?>";
    var recipeSearchUrl = '<?= Url::to(['@apiSearchPackageTemplateRecipe']) ?>';
    
    require([baseUrl + "/public/js/outpatient/recipeTemplate.js"], function (main) {
        main.init();
    });
</script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>