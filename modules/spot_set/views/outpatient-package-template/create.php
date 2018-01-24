<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\CureList;
use yii\helpers\Url;
use app\modules\outpatient\models\RecipeRecord;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\OnceDepartment */

$this->title = '新增医嘱模板／套餐';
$this->params['breadcrumbs'][] = ['label' => '医嘱模板／套餐', 'url' => ['package-template-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
	<?php AppAsset::addCss($this, '@web/public/css/lib/tab.css') ?>
	<?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css') ?>
	<?php AppAsset::addCss($this, '@web/public/css/spot_set/packageTemplate.css') ?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render('_templateSidebar')?>
<div class="once-department-create col-xs-10">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['package-template-index'],['class' => 'right-cancel second-cancel']) ?>      
    </div>
        <div class = "box-body">    

            <?= $this->render('_form', [
                'model' => $model,
                'cureDataProvider' => $cureDataProvider
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
<?php AppAsset::addScript($this, '@web/public/plugins/select2/select2.full.min.js')?>
<?php AppAsset::addScript($this, '@web/public/plugins/select2/i18n/zh-CN.js')?>
<script type="text/javascript">

    var baseUrl = '<?= $baseUrl; ?>';
    var unit = <?= json_encode(RecipeList::$getUnit, true) ?>;
    var doseUnit = <?= json_encode(RecipeList::$getDoseUnit,true) ?>;
    var defaultUsed = <?= json_encode(RecipeList::$getDefaultUsed,true) ?>;
    var dosage_form = <?= json_encode(RecipeList::$getType,true) ?>;
    var defaultAddress = <?= json_encode(RecipeList::$getAddress, true) ?>;
    var defaultFrequency = <?= json_encode(RecipeList::$getDefaultConsumption, true) ?>;
    var skinTestList = <?= json_encode(CureList::getCureOne(null,['id','name'],['type' => 1]),true); ?>;
    var skinTestStatusList = <?= json_encode(RecipeRecord::$getSkinTestStatus,true) ?>;//皮试状态
    var itemUrl = '<?= Url::to(['@apiMedicineDescriptionItem']) ?>';//查看用药指南url
    var indexUrl = '<?= Url::to(['@spot_setOutpatientPackageTemplatePackageTemplateIndex']) ?>';
    var inspectSearchUrl = '<?= Url::to(['@apiSearchPackageTemplateInspect']) ?>';
    var checkSearchUrl = '<?= Url::to(['@apiSearchPackageTemplateCheck']) ?>';
    var cureSearchUrl = '<?= Url::to(['@apiSearchPackageTemplateCure']) ?>';
    var recipeSearchUrl = '<?= Url::to(['@apiSearchPackageTemplateRecipe']) ?>';
    
    require([baseUrl + '/public/js/spot_set/packageTemplate.js'],function(main){
        main.init();
    })
</script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>