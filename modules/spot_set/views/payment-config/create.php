<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use johnitvn\ajaxcrud\CrudAsset;
CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\PaymentConfig */

$this->title = '新增支付配置';
$this->params['breadcrumbs'][] = ['label' => '支付设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<div class="payment-config-create col-xs-12">
    <div class = "box">
        <div class="box-header with-border">
            <span class = 'left-title'><?= Html::encode($this->title) ?></span>
            <?= Html::a(Html::img($baseUrl . '/public/img/common/icon_back.png') . '返回', Yii::$app->request->referrer, ['class' => 'right-cancel']) ?>      
        </div>
        <div class = "box-body">    

            <?=
            $this->render('_form', [
                'model' => $model,
            ])
            ?>
        </div>
    </div>
</div>

<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>
<script  type="text/javascript"  src="<?php echo $baseUrl . '/public/js/lib/jquery.qrcode.min.js?v=23' ?>"></script>
<script type="text/javascript">
    var baseUrl = '<?= $baseUrl ?>';
    require(["<?= $baseUrl ?>" + "/public/js/paymentconfig/create.js?v="+ '<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>
<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>