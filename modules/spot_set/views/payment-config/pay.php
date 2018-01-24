<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\Pjax;
CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\PaymentConfig */

//$this->title = '支付宝配置';
$this->params['breadcrumbs'][] = '支付配置';
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('renderCss') ?>
<?php $this->endBlock(); ?>
<?php $this->beginBlock('content') ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>
<div class="payment-config-create col-xs-12">
<?php echo $this->render('_tab.php'); ?>
    <div class = "box delete_gap">
        <div style=" height: 10px;">
        </div>
        <div class = "box-body">    

            <?=
            $this->render('_payForm', [
                'model' => $model,
            ])
            ?>
        </div>
    </div>
</div>
<?php
$js = <<<JS
   require(["$baseUrl/public/js/paymentconfig/pay.js?v="+versionNumber], function (main) {
        var isNewRecord = "$model->isNewRecord";
        if(isNewRecord == 1){
            $('#pay-view .form-control').attr('readonly',false);
            $('#btn-submit[type="button"]').html('保存');
           $('#btn-submit[type="button"]').attr({'type' : 'button','class' : 'btn btn-form btn-disabled disabled','readonly':true});
        }
        main.init();
    });
JS;
$this->registerJs($js);
?>
<?php Pjax::end();?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php AutoLayout::end()?>