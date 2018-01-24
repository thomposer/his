<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use app\modules\spot\models\Spot;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Item */
/* @var $form yii\widgets\ActiveForm */
$this->params['breadcrumbs'][] = ['label' => '诊所设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/css/spot/create.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="item-create col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
    </div>
        <div class = "box-body">    
            <div class="item-form col-md-8">

                <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'appointment_type')->radioList(Spot::$appointmentType)->label(false) ?>

                    <div class="form-group">
                        <?= Html::Button('保存', ['class' => 'btn btn-default btn-form confirm-config']) ?>
                    </div>
            
                <?php ActiveForm::end(); ?>
            
            </div>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>
<?php  $this->beginBlock('renderJs')?>
    <script type="text/javascript">
        var jsonFormInit = $("form").serialize();
        var dispensingUrl = '<?= Yii::$app->getRequest()->absoluteUrl ?>';
        var apiTypeConfigGetTypeTime = '<?=  Url::to(['@apiTypeConfigGetTypeTime']) ?>';
        console.log(jsonFormInit);
        console.log(dispensingUrl);

        $('body').on('click', '.confirm-config', function () {

            var idArr = [];
            var remarkArr = [];

            var jsonFormCurr = $("form").serialize();

            var confirm_option = {
                label: "确定",
                className: 'btn-cancel btn-form',
            };
            var cancel_option = {
                label: "取消",
                className: 'btn-default btn-form',
            };
            btns = {
                cancel: cancel_option,
                confirm: confirm_option,
            }

            console.log(jsonFormCurr);

            if (jsonFormCurr != jsonFormInit) {
                bootbox.confirm(
                    {
                        message: '预约模式修改将影响诊所后续的患者预约，<br/>是否保存本次修改?',
                        title: '系统提示',
                        buttons: btns,
                        callback: function (confirmed) {
                            if (confirmed) {
                                $.ajax({
                                    cache: true,
                                    type: "POST",
                                    url: dispensingUrl,
                                    data: jsonFormCurr, // 你的formid
                                    dataType: 'json',
                                    async: false,
                                    success: function (data, textStatus, jqXHR) {
                                        window.location.href = dispensingUrl;//返回上一页并刷新
                                    },
                                    error: function () {
                                    }
                                })
                            } else {
                                return true;
                            }
                        }
                    }
                );
            }
        })
    </script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>