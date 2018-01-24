<?php

use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\charge\models\ChargeRecord;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/check/common.css');
?>

<div class="charge-record-form">
    <div class = 'cost-bg'>
        <h5 class = 'title'>选择你对 “<?= Html::encode($userInfo['username']);?>” 的检查项目</h5>
    </div>
    <?php
    $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal common'],
                'id' => 'on-inspect'
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-12'>
        <?= $form->field($model, 'onInspect')->checkboxList(ArrayHelper::map($inspectList, 'id', 'name'))->label(false); ?>
        </div>
    </div>
    <div class = 'modal-footer text-center'>
        <div class = 'form-group'>
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) ?>
            <?= Html::submitButton('保存并打印条形码', ['class' => 'btn btn-default btn-form']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
	var inspectList = <?= json_encode($inspectList,true) ?>;
    require(["<?= $baseUrl ?>"+"/public/js/inspect/on-inspect.js"],function(main){
    	main.init();
    });
</script>
