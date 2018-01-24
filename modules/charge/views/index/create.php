<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\Charge */
use johnitvn\ajaxcrud\CrudAsset;
use yii\helpers\Url;
use app\modules\charge\models\ChargeInfo;
use app\common\Common;
use rkit\yii2\plugins\ajaxform\Asset;

$this->title = '收费明细';
$this->params['breadcrumbs'][] = ['label' => '收费', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
CrudAsset::register($this);
Asset::register($this);
?>
<?php
AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/charge/form.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="charge-create col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',['index'],['class' => 'right-cancel']) ?>
    </div>
        <div class = "box-body charge-body">    
            <?= $this->render('_form', [
                'dataProvider' => $dataProvider,
                'userInfo' => $userInfo,
                'chargeType' => $chargeType,
                'baseUrl' => $baseUrl,
                'id' => $id,
                'chargeTotalDiscount'=>$chargeTotalDiscount,
                'cardTotalDiscount'=>$cardTotalDiscount,
                'doctorName' => $doctorName,
                'updateMaterialButtonType' => $updateMaterialButtonType,
                'entrance' => 1,
                'recipeState' => $recipeState,
                'recipeInspectState' => $recipeInspectState
            ]) ?>
        
        </div>  
    </div>
    <div class = 'save-charge'>
       <?= Html::button(Html::tag('span','¥').'确认收费',['class' => 'btn btn-default','data-url' => Url::to(['create','id' => Yii::$app->request->get('id')]),'role'=>'modal-create','title'=>'确认收费','data-modal-size' => 'normal']) ?>
   </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type="text/javascript">
    	var baseUrl = "<?= $baseUrl ?>";
    	var aliPayUrl = "";
        var wechatUrl = "";
        var apiChargeCheckMaterialRecordNum = '<?= Url::to(['@apiChargeCheckMaterialRecordNum']) ?>';
        var updateMaterialButtonType = '<?= $updateMaterialButtonType ?>';
        require(["<?= $baseUrl ?>"+"/public/js/charge/create.js"],function(main){
    		main.init();
            window.main = main;

    	});
    </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>