<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\Charge */
$this->title = '退费明细';
$this->params['breadcrumbs'][] = ['label' => '退费记录', 'url' => Yii::$app->request->referrer];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
CrudAsset::register($this);
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/charge/form.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="charge-update col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">
        
            <?= $this->render('_form', [
                'dataProvider' => $dataProvider,
                'userInfo' => $userInfo,
                'refundInfo'=> $refundInfo,
                'chargeType' => $chargeType,
                'discountType' => $discountType,
                'discountPrice' => $discountPrice,
                'discountReason' => $discountReason,
                'chargeTotalDiscount' => $chargeTotalDiscount,
                'cardTotalDiscount' => $cardTotalDiscount,
                'doctorName' => $doctorName,
                'entrance' => 2,
                'recipeInspectState' => $recipeInspectState,
            ]) ?>
        </div>
    </div>
    <div class = 'save-charge'>
        <?= Html::button(Html::tag('span','¥').'确定重新收费',['class' => 'btn btn-default btn-again','data-url' => Url::to(['refund','id' => Yii::$app->request->get('id')]),'role'=>'modal-remote-bulk','data-request-method' =>'post','title'=>'确定重新收费', 'data-toggle'=>'tooltip','data-pjax'=>"0",'data-confirm-title'=>"系统提示",'data-confirm-message'=>"确定重新收费吗？"]) ?>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type="text/javascript">
    	var baseUrl = "<?= $baseUrl ?>";
        var payType = '<?= $payType ?>';
        var income = '<?= $income ?>';
        var flowCount = '<?= $flowCount ?>';
        var action = '<?= Yii::$app->controller->action->id ?>';
        require(["<?= $baseUrl ?>"+"/public/js/charge/update.js?v="+ '<?= $versionNumber ?>'],function(main){
    		main.init();
    	});
    </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>