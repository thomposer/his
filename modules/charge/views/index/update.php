<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use app\assets\AppAsset;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\Charge */
$this->title = '收费明细';
$this->params['breadcrumbs'][] = ['label' => '收费记录', 'url' => Yii::$app->request->referrer];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
CrudAsset::register($this);
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
    <?php AppAsset::addCss($this, '@web/public/css/charge/form.css')?>
    <?php AppAsset::addCss($this, '@web/public/css/check/print.css') ?>
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
                'refund_reason'=> $refund_reason,
                'refundAmount' => $refundAmount,
                'discountReason' => $discountReason,
                'reason_description'=> $reason_description,
                'chargeType' => $chargeType,
                'discountType' => $discountType,
                'discountPrice' => $discountPrice,
                'income' => $income,
                'change' => $change,
                'chargeTotalDiscount'=>$chargeTotalDiscount,
                'cardTotalDiscount'=>$cardTotalDiscount,
                'doctorName' => $doctorName,
                'entrance' => 1,
                'recipeInspectState' => $recipeInspectState,
                'flowListCount' => $flowListCount,
            ]) ?>
        </div>

        <div class="hide">
            <?= $this->render('_printRebateForm', [
                'dataProvider' => $dataProvider,
                'userInfo' => $userInfo,
                'doctor_name'=>$doctor_name,
                'type'=>$type,
                'soptInfo'=>$soptInfo,
                'chargeType' => $chargeType,
                'discountType' => $discountType,
                'discountPrice' => $discountPrice,
                'refundAmount' => $refundAmount,
                'income' => $income,
                'change' => $change,
                'chargeCreateTime' => $chargeCreateTime,
                'baseUrl' => $baseUrl,
                'chargeTotalDiscount'=>$chargeTotalDiscount,
                'cardTotalDiscount'=>$cardTotalDiscount,
                'recipeInspectState' => $recipeInspectState,
            ]) ?>
        </div>

    </div>
    <div class = 'save-charge'>
        <?= Html::button(Html::tag('span','¥').'确认退费',['class' => 'btn btn-default btn-rebate','data-url' => Url::to(['update','id' => Yii::$app->request->get('id')]),'role'=>'modal-update','title'=>'确认退费', 'data-toggle'=>'tooltip','data-modal-size'=>'normal']) ?>
        <?php // Html::button('打印收费单', ['class' => 'btn btn-default btn-print' ,'name'=>Yii::$app->request->get('id').'charge_show' ]) ?>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type="text/javascript">
    	var baseUrl = "<?= $baseUrl ?>";
		var chargeId = '<?= $chargeId ?>';
		var discountType = '<?= $discountType ?>';
		var flowCount = '1';//去掉原来服务卡不能退费的限制
		var action = '<?= Yii::$app->controller->action->id ?>';
        require(["<?= $baseUrl ?>"+"/public/js/lib/jquery-migrate-1.1.0.js"],function(){
        });

        require(["<?= $baseUrl ?>"+"/public/js/lib/jquery.jqprint-0.3.js"],function(){
        });

        require(["<?= $baseUrl ?>"+"/public/js/charge/update.js?v="+ '<?= $versionNumber ?>'],function(main){
    		main.init();
    	});
    </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>