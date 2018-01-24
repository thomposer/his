<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\RecipeList */

$this->title = '新增处方医嘱';
$this->params['breadcrumbs'][] = ['label' => '处方医嘱', 'url' => ['cure-index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
        <?php AppAsset::addCss($this, '@web/public/plugins/select2/select2.min.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<?php echo $this->render(Yii::getAlias('@spotChargeItemNav')) ?>
<div class="recipe-list-create col-xs-10">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">    

            <?= $this->render('_form', [
                'model' => $model,
                'medicineDescription' => $medicineDescription,
                'commonTagList' => $commonTagList,
                'spotList' => $spotList,
            ]) ?>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type="text/javascript">
    	var itemUrl = '<?= Url::to(['@apiMedicineDescriptionItem']) ?>';//默认使用指征url
    	var getItemUrl = '<?= Url::to(['@apiMedicineDescriptionView']) ?>';//查看对应指征详情url
		require(["<?= $baseUrl ?>"+"/public/js/spot/recipe.js"],function(main){
			main.init();
		});
	</script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>