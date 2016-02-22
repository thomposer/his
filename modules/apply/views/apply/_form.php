<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\web\YiiAsset;
use app\common\AutoLayout;
$baseUrl = Yii::$app->request->baseUrl;
/* @var $this yii\web\View */
/* @var $model app\modules\apply\models\ApplyPermissionList */
/* @var $form yii\widgets\ActiveForm */
?>
<?php AutoLayout::begin(['viewFile' => '@app/modules/apply/views/layouts/layout.php'])?>
<?php $this->beginBlock('renderCss')?>
<?php $this->registerCssFile('@web/public/css/bootstrap/bootstrap.css')?>
<?php $this->registerCssFile('@web/public/css/rbac/rbac.css')?>

<?php $this->registerJsFile('@web/public/js/rbac/apply.js',['depends'=>AppAsset::className()])?>
<script src="<?php echo $baseUrl.'/public/js/jquery/jquery.min.js' ?>" type="text/javascript" charset="utf-8"></script>
<?php $this->endBlock();?>
<?php $this->beginBlock('content')?>
<div class="apply-permission-list-form">

   <?php $form = ActiveForm::begin([
      'options' => ['class' => 'form-horizontal'],
      'fieldConfig' => [
          'template' => "<div class='col-xs-3 col-sm-2 text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div><div class='col-xs-12 col-xs-offset-3 col-sm-3 col-sm-offset-0'>{error}</div>",
]]); ?> 

    <?= $form->field($model, 'spot')->dropDownList(ArrayHelper::map($spot,'spot', 'spot_name'),['prompt' => '请选择站点'])->label("<span class='need'>*</span>站点名称") ?>
   
    <?= $form->field($model,'reason')->textarea(['rows'=>6])->label('<span class="need">*</span>申请理由'); ?>



    <div class="footer_button" >
        <?= Html::submitButton('申请权限', ['class' => 'btn btn-success']) ?>
        <?= Html::a('返回列表', ['@applyApplyIndex'], ['class' => 'btn btn-primary returnindex'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php $this->endBlock();?>
<?php $this->beginBlock('renderJs')?>
<?php $this->endBlock();?>
<?php AutoLayout::end();?>
