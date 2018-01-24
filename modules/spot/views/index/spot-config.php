<?php

use yii\helpers\Html;
use app\common\AutoLayout;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
use app\modules\spot\models\SpotConfig;
use yii\helpers\Url;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\SpotConfig */
/* @var $form yii\widgets\ActiveForm */
$this->title = '参数配置';
$this->params['breadcrumbs'][] = ['label' => '诊所管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$baseUrl = Yii::$app->request->baseUrl;
$attributeLabels=$model->attributeLabels();
$versionNumber = Yii::getAlias("@versionNumber");
?>
<?php  AutoLayout::begin(['viewFile' => '@app/views/layouts/layout.php'])?>
<?php  $this->beginBlock('renderCss')?>
<?php AppAsset::addCss($this, '@web/public/plugins/timepicker/bootstrap-timepicker.min.css')?>
<?php AppAsset::addCss($this, '@web/public/css/spot/spotConfig.css')?>
<?php  $this->endBlock();?>
<?php  $this->beginBlock('content')?>
<div class="spot-config-update col-xs-12">
    <div class = "box">
    <div class="box-header with-border">
      <span class = 'left-title'><?= Html::encode($this->title) ?></span>
      <?=  Html::a(Html::img($baseUrl.'/public/img/common/icon_back.png').'返回',Yii::$app->request->referrer,['class' => 'right-cancel']) ?>      
    </div>
        <div class = "box-body">
        
            <div class="spot-config-form col-md-8">
                <?php $form = ActiveForm::begin(); ?>
                <div class = 'row row-title'>
                    打印参数
                </div>
                <div class = 'row'>
                    <div class = 'col-md-4'>
                        <?= $form->field($model, 'appointment_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
                    </div>
                    <div class = 'col-md-2'>
                        <?= Html::a('预览', Url::to(['rebate-img','id'=>1]),['class' => 'btn btn-default rebate-btn','title'=>'预览', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
                    </div>
                    <div class = 'col-md-4'>
                        <?= $form->field($model, 'inspect_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
                    </div>
                    <div class = 'col-md-2'>
                        <?= Html::a('预览', Url::to(['rebate-img','id'=>2]),['class' => 'btn btn-default rebate-btn','title'=>'预览', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'col-md-4'>
                        <?= $form->field($model, 'check_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
                    </div>
                    <div class = 'col-md-2'>
                        <?= Html::a('预览', Url::to(['rebate-img','id'=>3]),['class' => 'btn btn-default rebate-btn','title'=>'预览', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
                    </div>
                    <div class = 'col-md-4'>
                        <?= $form->field($model, 'cure_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
                    </div>
                    <div class = 'col-md-2'>
                        <?= Html::a('预览', Url::to(['rebate-img','id'=>4]),['class' => 'btn btn-default rebate-btn','title'=>'预览', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'col-md-4'>
                        <?= $form->field($model, 'reception_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
                    </div>
                    <div class = 'col-md-2'>
                        <?= Html::a('预览', Url::to(['rebate-img','id'=>5]),['class' => 'btn btn-default rebate-btn','title'=>'预览', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
                    </div>
                    <div class = 'col-md-4'>
                        <?= $form->field($model, 'charge_rebate')->dropDownList(SpotConfig::$getrebatetype) ?>
                    </div>
                    <div class = 'col-md-2'>
                        <?= Html::a('预览', Url::to(['rebate-img','id'=>6]),['class' => 'btn btn-default rebate-btn','title'=>'预览', 'data-toggle'=>'tooltip', 'role'=>'modal-remote']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= Html::a('取消',['index'],['class' => 'btn btn-cancel btn-form']) ?>
                    <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
                </div>
            
                <?php ActiveForm::end(); ?>
            
            </div>
        </div>
    </div>
</div>
<?php  $this->endBlock()?>

<?php  $this->beginBlock('renderJs')?>
<script type="text/javascript">

		require(["<?= $baseUrl ?>"+"/public/js/spot/spotConfig.js?v="+'<?= $versionNumber ?>'],function(main){
			main.init();
		})
</script>
<?php  $this->endBlock()?>
<?php  AutoLayout::end()?>
