<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\Pjax;

CrudAsset::register($this);
//
//$this->title = '支付配置';
$this->params['breadcrumbs'][] ='支付配置';
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
$canRead = isset($model->appid) && $model->appid ? 1 : 2;
?>
<?php AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php']) ?>
<?php $this->beginBlock('content') ?>
<?php Pjax::begin(['id' => 'crud-datatable-pjax']) ?>

<div class="payment-config-update col-xs-12">
<?php echo $this->render('_tab.php'); ?>
    <div class = "box delete_gap">
        <div style=" height: 10px;">
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
<?php
$js = <<<JS
   require(["$baseUrl/public/js/paymentconfig/create.js?v=$versionNumber"], function (main) {
        main.canReadStatus="$canRead";
        main.init();
    });
JS;
$this->registerJs($js);
?>
<?php Pjax::end(); ?>
<?php $this->endBlock() ?>
<?php $this->beginBlock('renderJs') ?>

<?php $this->endBlock() ?>
<?php
AutoLayout::end()?>